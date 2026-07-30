/**
 * UploadManager — YouTube-style media uploader.
 *
 * Markup contract (see components/form/uploader.blade.php):
 *   [data-uploader]              root
 *     [data-uploader-drop]       drag target / click-to-browse
 *     [data-uploader-input]      <input type="file">
 *     [data-uploader-jobs]       <ul> queue rendered by this manager
 *
 * Root attributes:
 *   data-upload-url   POST endpoint (FormData). Absent → idle until demo/sim.
 *   data-demo         simulate progress (for /design and offline demos)
 *   data-max-files    0 = unlimited
 *   data-max-size     bytes per file, 0 = unlimited
 *   data-concurrency  parallel uploads (default 2)
 *   data-name         FormData field name
 *
 * Events (bubble): upload:added | upload:progress | upload:complete |
 *                  upload:error | upload:cancel | upload:remove
 */

type JobStatus = 'queued' | 'uploading' | 'processing' | 'done' | 'error' | 'cancelled';

interface UploadJob {
    id: string;
    file: File;
    status: JobStatus;
    percent: number;
    error?: string;
    previewUrl?: string;
    kind: 'image' | 'video' | 'file';
    xhr?: XMLHttpRequest;
    demoTimer?: number;
    el: HTMLElement;
}

function uid(): string {
    return `up_${Math.random().toString(36).slice(2, 10)}`;
}

function formatBytes(n: number): string {
    if (n < 1024) return `${n} B`;
    if (n < 1024 * 1024) return `${(n / 1024).toFixed(1)} KB`;
    if (n < 1024 * 1024 * 1024) return `${(n / (1024 * 1024)).toFixed(1)} MB`;
    return `${(n / (1024 * 1024 * 1024)).toFixed(2)} GB`;
}

function fileKind(file: File): UploadJob['kind'] {
    if (file.type.startsWith('image/')) return 'image';
    if (file.type.startsWith('video/')) return 'video';
    return 'file';
}

const ICONS = {
    file: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>`,
    video: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="6" width="14" height="12" rx="2"/><path d="m16 10 6-3v10l-6-3z"/></svg>`,
    image: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/></svg>`,
    close: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>`,
    retry: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.5 15a9 9 0 1 0 .9-8.1L1 10"/></svg>`,
};

export class UploadManager {
    private roots: HTMLElement[] = [];
    private jobs = new WeakMap<HTMLElement, Map<string, UploadJob>>();

    constructor(scope: ParentNode = document) {
        this.roots = Array.from(scope.querySelectorAll<HTMLElement>('[data-uploader]'));
        this.roots.forEach((root) => this.bind(root));
    }

    private bind(root: HTMLElement): void {
        this.jobs.set(root, new Map());

        const drop = root.querySelector<HTMLElement>('[data-uploader-drop]');
        const input = root.querySelector<HTMLInputElement>('[data-uploader-input]');
        if (!drop || !input) return;

        drop.addEventListener('click', (e) => {
            if ((e.target as HTMLElement).closest('[data-uploader-cancel], [data-uploader-retry], [data-uploader-remove]')) return;
            input.click();
        });

        drop.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                input.click();
            }
        });

        input.addEventListener('change', () => {
            if (input.files?.length) {
                this.addFiles(root, Array.from(input.files));
                input.value = '';
            }
        });

        drop.addEventListener('dragenter', (e) => this.onDrag(e, drop, true));
        drop.addEventListener('dragover', (e) => this.onDrag(e, drop, true));
        drop.addEventListener('dragleave', (e) => {
            if (!drop.contains((e.relatedTarget as Node) || null)) {
                drop.removeAttribute('data-dragover');
            }
        });
        drop.addEventListener('drop', (e) => {
            e.preventDefault();
            drop.removeAttribute('data-dragover');
            const files = Array.from(e.dataTransfer?.files ?? []);
            if (files.length) this.addFiles(root, files);
        });
    }

    private onDrag(e: DragEvent, drop: HTMLElement, over: boolean): void {
        e.preventDefault();
        e.stopPropagation();
        if (over) drop.setAttribute('data-dragover', '');
    }

    private addFiles(root: HTMLElement, files: File[]): void {
        const maxFiles = Number(root.dataset.maxFiles || 0);
        const maxSize = Number(root.dataset.maxSize || 0);
        const map = this.jobs.get(root)!;
        let remaining = maxFiles > 0 ? Math.max(0, maxFiles - map.size) : Infinity;

        for (const file of files) {
            if (remaining <= 0) {
                this.emit(root, 'upload:error', {
                    job: null,
                    error: `Maximum of ${maxFiles} files`,
                    root,
                });
                break;
            }
            if (maxSize > 0 && file.size > maxSize) {
                this.emit(root, 'upload:error', {
                    job: null,
                    error: `${file.name} exceeds ${formatBytes(maxSize)}`,
                    file,
                    root,
                });
                continue;
            }

            const job = this.createJob(root, file);
            map.set(job.id, job);
            remaining -= 1;
            this.emit(root, 'upload:added', { job: this.publicJob(job), file, root });
        }

        this.syncHasJobs(root);
        this.pump(root);
    }

    private createJob(root: HTMLElement, file: File): UploadJob {
        const id = uid();
        const kind = fileKind(file);
        const previewUrl = kind === 'image' || kind === 'video'
            ? URL.createObjectURL(file)
            : undefined;

        const el = this.renderJob({ id, file, status: 'queued', percent: 0, previewUrl, kind });
        const job: UploadJob = {
            id,
            file,
            status: 'queued',
            percent: 0,
            previewUrl,
            kind,
            el,
        };

        const list = root.querySelector<HTMLElement>('[data-uploader-jobs]');
        list?.prepend(el);

        el.querySelector('[data-uploader-cancel]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.cancelJob(root, job.id);
        });
        el.querySelector('[data-uploader-remove]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeJob(root, job.id);
        });
        el.querySelector('[data-uploader-retry]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.retryJob(root, job.id);
        });

        const img = el.querySelector<HTMLImageElement>('.form-uploader-thumb img');
        img?.addEventListener('error', () => {
            const thumb = el.querySelector('.form-uploader-thumb');
            if (!thumb) return;
            thumb.innerHTML = `<span class="form-uploader-thumb-icon">${ICONS.image}</span>
                <span class="form-uploader-thumb-badge">img</span>`;
        });

        return job;
    }

    private renderJob(partial: Pick<UploadJob, 'file' | 'status' | 'percent' | 'previewUrl' | 'kind'> & { id?: string }): HTMLElement {
        const id = partial.id || uid();
        const li = document.createElement('li');
        li.className = 'form-uploader-job';
        li.dataset.jobId = id;
        li.dataset.status = partial.status;

        const thumb = this.thumbHtml(partial.kind, partial.previewUrl, partial.file);
        li.innerHTML = `
            ${thumb}
            <div class="form-uploader-body">
                <div class="form-uploader-head">
                    <p class="form-uploader-name" title="${this.escape(partial.file.name)}">${this.escape(partial.file.name)}</p>
                    <span class="form-uploader-size">${formatBytes(partial.file.size)}</span>
                    <div class="form-uploader-actions">
                        <button type="button" class="form-uploader-action" data-uploader-retry hidden aria-label="Retry upload">${ICONS.retry}</button>
                        <button type="button" class="form-uploader-action form-uploader-action--danger" data-uploader-cancel aria-label="Cancel upload">${ICONS.close}</button>
                        <button type="button" class="form-uploader-action form-uploader-action--danger" data-uploader-remove hidden aria-label="Remove">${ICONS.close}</button>
                    </div>
                </div>
                <div class="form-uploader-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                    <div class="form-uploader-progress-fill" style="width:0%"></div>
                </div>
                <div class="form-uploader-status">Waiting…</div>
            </div>
        `;
        return li;
    }

    private thumbHtml(kind: UploadJob['kind'], previewUrl: string | undefined, file: File): string {
        if (kind === 'image' && previewUrl) {
            return `<div class="form-uploader-thumb"><img src="${previewUrl}" alt=""></div>`;
        }
        if (kind === 'video' && previewUrl) {
            return `<div class="form-uploader-thumb">
                <video src="${previewUrl}" muted playsinline preload="metadata"></video>
                <span class="form-uploader-thumb-badge">Video</span>
            </div>`;
        }
        const icon = kind === 'video' ? ICONS.video : kind === 'image' ? ICONS.image : ICONS.file;
        const badge = file.name.split('.').pop()?.slice(0, 4) || 'file';
        return `<div class="form-uploader-thumb">
            <span class="form-uploader-thumb-icon">${icon}</span>
            <span class="form-uploader-thumb-badge">${this.escape(badge)}</span>
        </div>`;
    }

    private pump(root: HTMLElement): void {
        const map = this.jobs.get(root);
        if (!map) return;
        const concurrency = Math.max(1, Number(root.dataset.concurrency || 2));
        const active = [...map.values()].filter((j) => j.status === 'uploading' || j.status === 'processing').length;
        const slots = concurrency - active;
        if (slots <= 0) return;

        const queued = [...map.values()].filter((j) => j.status === 'queued').slice(0, slots);
        queued.forEach((job) => this.startJob(root, job));
    }

    private startJob(root: HTMLElement, job: UploadJob): void {
        const url = root.dataset.uploadUrl;
        const demo = root.hasAttribute('data-demo') || !url;

        job.status = 'uploading';
        job.percent = 0;
        this.paint(job);

        if (demo) {
            this.startDemo(root, job);
            return;
        }

        const field = root.dataset.name || 'files[]';
        const body = new FormData();
        body.append(field, job.file);

        const xhr = new XMLHttpRequest();
        job.xhr = xhr;
        xhr.open('POST', url!);
        xhr.upload.addEventListener('progress', (e) => {
            if (!e.lengthComputable) return;
            job.percent = Math.round((e.loaded / e.total) * 100);
            this.paint(job);
            this.emit(root, 'upload:progress', {
                job: this.publicJob(job),
                loaded: e.loaded,
                total: e.total,
                percent: job.percent,
                root,
            });
        });
        xhr.addEventListener('load', () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                job.status = 'processing';
                this.paint(job);
                window.setTimeout(() => {
                    if (job.status !== 'processing') return;
                    job.status = 'done';
                    job.percent = 100;
                    this.paint(job);
                    this.emit(root, 'upload:complete', {
                        job: this.publicJob(job),
                        response: xhr.responseText,
                        root,
                    });
                    this.pump(root);
                }, 600);
            } else {
                this.failJob(root, job, `Upload failed (${xhr.status})`);
            }
        });
        xhr.addEventListener('error', () => this.failJob(root, job, 'Network error'));
        xhr.addEventListener('abort', () => {
            /* cancelJob handles status */
        });
        xhr.send(body);
    }

    private startDemo(root: HTMLElement, job: UploadJob): void {
        // Pace by file size so big videos feel slower.
        const duration = Math.min(5000, Math.max(1400, job.file.size / 400_000));
        const started = performance.now();

        const tick = (now: number) => {
            if (job.status !== 'uploading') return;
            const t = Math.min(1, (now - started) / duration);
            // Ease-out curve
            job.percent = Math.round((1 - (1 - t) ** 2) * 100);
            this.paint(job);
            this.emit(root, 'upload:progress', {
                job: this.publicJob(job),
                loaded: Math.round((job.percent / 100) * job.file.size),
                total: job.file.size,
                percent: job.percent,
                root,
            });

            if (t < 1) {
                job.demoTimer = window.requestAnimationFrame(tick);
                return;
            }

            job.status = 'processing';
            this.paint(job);
            job.demoTimer = window.setTimeout(() => {
                if (job.status !== 'processing') return;
                job.status = 'done';
                job.percent = 100;
                this.paint(job);
                this.emit(root, 'upload:complete', {
                    job: this.publicJob(job),
                    response: null,
                    root,
                });
                this.pump(root);
            }, 700) as unknown as number;
        };

        job.demoTimer = window.requestAnimationFrame(tick);
    }

    private failJob(root: HTMLElement, job: UploadJob, error: string): void {
        job.status = 'error';
        job.error = error;
        this.paint(job);
        this.emit(root, 'upload:error', { job: this.publicJob(job), error, root });
        this.pump(root);
    }

    private cancelJob(root: HTMLElement, id: string): void {
        const map = this.jobs.get(root);
        const job = map?.get(id);
        if (!job) return;

        if (job.xhr) job.xhr.abort();
        if (job.demoTimer) {
            window.cancelAnimationFrame(job.demoTimer);
            window.clearTimeout(job.demoTimer);
        }

        if (job.status === 'done' || job.status === 'error') {
            this.removeJob(root, id);
            return;
        }

        job.status = 'cancelled';
        this.emit(root, 'upload:cancel', { job: this.publicJob(job), root });
        this.removeJob(root, id);
        this.pump(root);
    }

    private retryJob(root: HTMLElement, id: string): void {
        const map = this.jobs.get(root);
        const job = map?.get(id);
        if (!job || job.status !== 'error') return;
        job.status = 'queued';
        job.percent = 0;
        job.error = undefined;
        this.paint(job);
        this.pump(root);
    }

    private removeJob(root: HTMLElement, id: string): void {
        const map = this.jobs.get(root);
        const job = map?.get(id);
        if (!job) return;

        if (job.previewUrl) URL.revokeObjectURL(job.previewUrl);
        job.el.remove();
        map!.delete(id);
        this.emit(root, 'upload:remove', { job: this.publicJob(job), root });
        this.syncHasJobs(root);
    }

    private paint(job: UploadJob): void {
        const el = job.el;
        el.dataset.status = job.status;

        const fill = el.querySelector<HTMLElement>('.form-uploader-progress-fill');
        const bar = el.querySelector<HTMLElement>('.form-uploader-progress');
        const status = el.querySelector<HTMLElement>('.form-uploader-status');
        const cancelBtn = el.querySelector<HTMLElement>('[data-uploader-cancel]');
        const removeBtn = el.querySelector<HTMLElement>('[data-uploader-remove]');
        const retryBtn = el.querySelector<HTMLElement>('[data-uploader-retry]');

        if (fill) fill.style.width = `${job.percent}%`;
        if (bar) bar.setAttribute('aria-valuenow', String(job.percent));

        if (status) {
            status.textContent = this.statusLabel(job);
        }

        const terminal = job.status === 'done' || job.status === 'error';
        if (cancelBtn) cancelBtn.hidden = terminal;
        if (removeBtn) removeBtn.hidden = !terminal;
        if (retryBtn) retryBtn.hidden = job.status !== 'error';
    }

    private statusLabel(job: UploadJob): string {
        switch (job.status) {
            case 'queued':
                return 'Waiting…';
            case 'uploading':
                return `Uploading… ${job.percent}%`;
            case 'processing':
                return 'Processing…';
            case 'done':
                return 'Upload complete';
            case 'error':
                return job.error || 'Upload failed';
            case 'cancelled':
                return 'Cancelled';
        }
    }

    private syncHasJobs(root: HTMLElement): void {
        const map = this.jobs.get(root);
        if (map && map.size > 0) root.setAttribute('data-has-jobs', '');
        else root.removeAttribute('data-has-jobs');
    }

    private publicJob(job: UploadJob) {
        return {
            id: job.id,
            name: job.file.name,
            size: job.file.size,
            type: job.file.type,
            kind: job.kind,
            status: job.status,
            percent: job.percent,
            error: job.error,
        };
    }

    private emit(root: HTMLElement, type: string, detail: Record<string, unknown>): void {
        root.dispatchEvent(new CustomEvent(type, { bubbles: true, detail }));
    }

    private escape(s: string): string {
        return s.replace(/[&<>"']/g, (c) => (
            { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!
        ));
    }
}
