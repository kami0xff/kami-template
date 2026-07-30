/**
 * MultipartFormManager — multi-step forms with progress checkpoints.
 *
 * Markup (see components/form/multipart.blade.php):
 *   form[data-multipart]
 *     [data-multipart-steps]     stepper (built from panels)
 *     [data-multipart-save]      save-status pill
 *     [data-multipart-panel]     each step (data-step, data-title, data-reminder)
 *     [data-multipart-prev|next|finish|save-exit]
 *
 * Persistence:
 *   1. localStorage draft (immediate, debounced)
 *   2. Optional checkpointUrl — POST JSON for server-side resume + email reminders
 *
 * Checkpoint payload (email-reminder friendly):
 *   {
 *     id, step, reminder, index, total, progress,
 *     values, completedSteps, updatedAt, reason
 *   }
 *   reason: 'autosave' | 'step' | 'save-exit' | 'complete'
 *
 * Events (bubble): multipart:step | multipart:checkpoint | multipart:complete
 *                  multipart:save-exit | multipart:error
 */

interface DraftState {
    step: string;
    values: Record<string, string | boolean>;
    completedSteps: string[];
    updatedAt: string;
}

interface PanelInfo {
    el: HTMLElement;
    step: string;
    title: string;
    reminder: string;
    optional: boolean;
}

type SaveReason = 'autosave' | 'step' | 'save-exit' | 'complete';

export class MultipartFormManager {
    private roots: HTMLFormElement[] = [];
    private timers = new WeakMap<HTMLFormElement, number>();

    constructor(scope: ParentNode = document) {
        this.roots = Array.from(scope.querySelectorAll<HTMLFormElement>('[data-multipart]'));
        this.roots.forEach((root) => this.bind(root));
    }

    private bind(root: HTMLFormElement): void {
        const panels = this.panels(root);
        if (panels.length === 0) return;

        this.buildStepper(root, panels);

        root.querySelector('[data-multipart-prev]')?.addEventListener('click', () => this.go(root, -1));
        root.querySelector('[data-multipart-next]')?.addEventListener('click', () => this.go(root, 1));
        root.querySelector('[data-multipart-save-exit]')?.addEventListener('click', () => this.saveExit(root));
        root.querySelector('[data-multipart-finish]')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.finish(root);
        });

        root.addEventListener('input', () => this.scheduleSave(root, 'autosave'));
        root.addEventListener('change', () => this.scheduleSave(root, 'autosave'));

        root.addEventListener('submit', (e) => {
            // Final submit only after last step validated via finish()
            if (!root.hasAttribute('data-multipart-ready')) {
                e.preventDefault();
                this.finish(root);
            }
        });

        // Resume draft
        if (root.hasAttribute('data-resume')) {
            this.restore(root);
        } else {
            this.showStep(root, panels[0].step, { silent: true });
        }
    }

    private panels(root: HTMLFormElement): PanelInfo[] {
        return Array.from(root.querySelectorAll<HTMLElement>('[data-multipart-panel]')).map((el) => ({
            el,
            step: el.dataset.step || '',
            title: el.dataset.title || el.dataset.step || '',
            reminder: el.dataset.reminder || el.dataset.step || '',
            optional: el.hasAttribute('data-optional'),
        })).filter((p) => p.step);
    }

    private buildStepper(root: HTMLFormElement, panels: PanelInfo[]): void {
        const host = root.querySelector<HTMLElement>('[data-multipart-steps]');
        if (!host) return;
        host.innerHTML = '';

        panels.forEach((panel, i) => {
            const step = document.createElement('div');
            step.className = 'form-step';
            step.dataset.stepTarget = panel.step;
            step.innerHTML = `
                <div class="form-step-num">${i + 1}</div>
                <div class="form-step-label">${this.escape(panel.title)}</div>
            `;
            step.addEventListener('click', () => {
                if (!step.hasAttribute('data-clickable')) return;
                this.showStep(root, panel.step);
            });
            host.appendChild(step);
        });
    }

    private currentIndex(root: HTMLFormElement): number {
        const panels = this.panels(root);
        const current = panels.findIndex((p) => !p.el.hidden);
        return current < 0 ? 0 : current;
    }

    private go(root: HTMLFormElement, dir: 1 | -1): void {
        const panels = this.panels(root);
        const i = this.currentIndex(root);

        if (dir === 1) {
            if (!this.validatePanel(panels[i])) return;
            this.markCompleted(root, panels[i].step);
            if (i >= panels.length - 1) {
                this.finish(root);
                return;
            }
            this.showStep(root, panels[i + 1].step);
            this.checkpoint(root, 'step');
            return;
        }

        if (i > 0) {
            this.showStep(root, panels[i - 1].step);
        }
    }

    private showStep(root: HTMLFormElement, step: string, opts: { silent?: boolean } = {}): void {
        const panels = this.panels(root);
        const index = panels.findIndex((p) => p.step === step);
        if (index < 0) return;

        panels.forEach((p, i) => {
            p.el.hidden = i !== index;
        });

        const completed = this.getCompleted(root);
        const steppers = root.querySelectorAll<HTMLElement>('[data-multipart-steps] .form-step');
        steppers.forEach((el, i) => {
            el.classList.toggle('active', i === index);
            el.classList.toggle('done', completed.includes(panels[i].step) && i !== index);
            const canClick = i < index || completed.includes(panels[i].step);
            if (canClick) el.setAttribute('data-clickable', '');
            else el.removeAttribute('data-clickable');

            const num = el.querySelector('.form-step-num');
            if (num) {
                if (completed.includes(panels[i].step) && i !== index) {
                    num.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`;
                } else {
                    num.textContent = String(i + 1);
                }
            }
        });

        const prev = root.querySelector<HTMLElement>('[data-multipart-prev]');
        const next = root.querySelector<HTMLElement>('[data-multipart-next]');
        const finish = root.querySelector<HTMLElement>('[data-multipart-finish]');
        if (prev) prev.hidden = index === 0;
        const last = index === panels.length - 1;
        if (next) next.hidden = last;
        if (finish) finish.hidden = !last;

        // Completed steps + partial credit for the current open step
        const partial = completed.includes(step) ? 0 : 0.35;
        const progressPct = Math.round(((completed.length + partial) / panels.length) * 100);
        this.setProgress(root, Math.min(99, progressPct));

        const stepField = root.querySelector<HTMLInputElement>('[data-multipart-step-field]');
        if (stepField) stepField.value = step;

        if (!opts.silent) {
            this.emit(root, 'multipart:step', this.payload(root, 'step'));
            this.scheduleSave(root, 'autosave');
        }

        // Focus first input in panel
        const focusable = panels[index].el.querySelector<HTMLElement>(
            'input:not([type=hidden]):not([disabled]), select:not([disabled]), textarea:not([disabled])',
        );
        focusable?.focus({ preventScroll: true });
        panels[index].el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }

    private validatePanel(panel: PanelInfo): boolean {
        if (panel.optional) return true;

        const fields = Array.from(
            panel.el.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>(
                'input, select, textarea',
            ),
        ).filter((el) => el.name && el.type !== 'hidden' && !el.disabled);

        let ok = true;
        fields.forEach((el) => {
            const field = el.closest('.form-field');
            field?.classList.remove('is-error');
            if (!el.checkValidity()) {
                ok = false;
                field?.classList.add('is-error');
                el.reportValidity();
            }
        });

        if (!ok) {
            const first = panel.el.querySelector<HTMLElement>('.form-field.is-error input, .form-field.is-error select, .form-field.is-error textarea');
            first?.focus();
        }
        return ok;
    }

    private finish(root: HTMLFormElement): void {
        const panels = this.panels(root);
        const i = this.currentIndex(root);
        if (!this.validatePanel(panels[i])) return;

        panels.forEach((p) => this.markCompleted(root, p.step));
        this.setProgress(root, 100);
        this.checkpoint(root, 'complete').then((saved) => {
            this.emit(root, 'multipart:complete', this.payload(root, 'complete'));
            if (!saved && root.hasAttribute('data-demo')) {
                this.setSaveState(root, 'saved', 'Onboarding complete');
                return;
            }
            // Allow native submit when an action is set
            if (root.action && !root.hasAttribute('data-demo')) {
                root.setAttribute('data-multipart-ready', '');
                root.submit();
            } else {
                this.setSaveState(root, 'saved', 'Onboarding complete');
            }
        });
    }

    private saveExit(root: HTMLFormElement): void {
        this.checkpoint(root, 'save-exit').then(() => {
            this.emit(root, 'multipart:save-exit', this.payload(root, 'save-exit'));
            this.setSaveState(root, 'saved', 'Progress saved — we\'ll remind you');
        });
    }

    private scheduleSave(root: HTMLFormElement, reason: SaveReason): void {
        const ms = Number(root.dataset.autosaveMs || 1200);
        const prev = this.timers.get(root);
        if (prev) window.clearTimeout(prev);
        this.setSaveState(root, 'saving', 'Saving…');
        const t = window.setTimeout(() => {
            this.checkpoint(root, reason);
        }, ms);
        this.timers.set(root, t);
    }

    private async checkpoint(root: HTMLFormElement, reason: SaveReason): Promise<boolean> {
        const draft = this.collectDraft(root);
        this.writeLocal(root, draft);

        const payload = this.payload(root, reason);
        this.emit(root, 'multipart:checkpoint', payload);

        const url = root.dataset.checkpointUrl;
        const demo = root.hasAttribute('data-demo');

        if (demo || !url) {
            this.setSaveState(root, 'saved', reason === 'save-exit' ? 'Progress saved — we\'ll remind you' : 'Progress saved');
            return true;
        }

        this.setSaveState(root, 'saving', 'Saving…');
        try {
            const csrf = root.querySelector<HTMLInputElement>('input[name="_token"]')?.value
                || document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content
                || '';

            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
                body: JSON.stringify(payload),
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error(`Checkpoint failed (${res.status})`);
            this.setSaveState(root, 'saved', 'Progress saved');
            return true;
        } catch (err) {
            this.setSaveState(root, 'error', 'Couldn\'t save — draft kept locally');
            this.emit(root, 'multipart:error', {
                error: err instanceof Error ? err.message : 'checkpoint failed',
                root,
            });
            return false;
        }
    }

    private collectDraft(root: HTMLFormElement): DraftState {
        const panels = this.panels(root);
        const i = this.currentIndex(root);
        const values = this.serialize(root);
        return {
            step: panels[i]?.step || panels[0].step,
            values,
            completedSteps: this.getCompleted(root),
            updatedAt: new Date().toISOString(),
        };
    }

    private serialize(root: HTMLFormElement): Record<string, string | boolean> {
        const data: Record<string, string | boolean> = {};
        const fd = new FormData(root);
        fd.forEach((value, key) => {
            if (key === '_token' || key.startsWith('_multipart') || key === '_method') return;
            if (typeof value === 'string') data[key] = value;
        });
        // Checkboxes that are unchecked won't appear — record known checkboxes
        root.querySelectorAll<HTMLInputElement>('input[type=checkbox][name]').forEach((el) => {
            data[el.name] = el.checked;
        });
        return data;
    }

    private restore(root: HTMLFormElement): void {
        const draft = this.readLocal(root);
        const panels = this.panels(root);
        if (draft?.values) {
            Object.entries(draft.values).forEach(([name, value]) => {
                const fields = root.querySelectorAll<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>(
                    `[name="${CSS.escape(name)}"]`,
                );
                fields.forEach((el) => {
                    if (el instanceof HTMLInputElement && el.type === 'checkbox') {
                        el.checked = Boolean(value);
                    } else if (el instanceof HTMLInputElement && el.type === 'radio') {
                        el.checked = el.value === value;
                    } else {
                        el.value = String(value ?? '');
                    }
                });
            });
            this.setCompleted(root, draft.completedSteps || []);
        }

        const step = draft?.step && panels.some((p) => p.step === draft.step)
            ? draft.step
            : panels[0].step;
        this.showStep(root, step, { silent: true });
        if (draft) this.setSaveState(root, 'saved', 'Draft restored');
    }

    private payload(root: HTMLFormElement, reason: SaveReason) {
        const panels = this.panels(root);
        const i = this.currentIndex(root);
        const panel = panels[i];
        const completed = this.getCompleted(root);
        const progress = Math.round((completed.length / panels.length) * 100);

        return {
            id: root.dataset.multipartId || root.id,
            step: panel?.step ?? '',
            reminder: panel?.reminder ?? panel?.step ?? '',
            index: i,
            total: panels.length,
            progress: root.querySelector<HTMLElement>('[data-multipart-progress-bar]')
                ? Number(root.querySelector<HTMLInputElement>('[data-multipart-progress-field]')?.value || progress)
                : progress,
            values: this.serialize(root),
            completedSteps: completed,
            updatedAt: new Date().toISOString(),
            reason,
        };
    }

    private setProgress(root: HTMLFormElement, pct: number): void {
        const bar = root.querySelector<HTMLElement>('[data-multipart-progress-bar]');
        const field = root.querySelector<HTMLInputElement>('[data-multipart-progress-field]');
        if (bar) bar.style.width = `${pct}%`;
        if (field) field.value = String(pct);
    }

    private markCompleted(root: HTMLFormElement, step: string): void {
        const set = new Set(this.getCompleted(root));
        set.add(step);
        this.setCompleted(root, [...set]);
    }

    private getCompleted(root: HTMLFormElement): string[] {
        try {
            return JSON.parse(root.dataset.completedSteps || '[]');
        } catch {
            return [];
        }
    }

    private setCompleted(root: HTMLFormElement, steps: string[]): void {
        root.dataset.completedSteps = JSON.stringify(steps);
    }

    private storageKey(root: HTMLFormElement): string {
        return root.dataset.storageKey || `multipart.${root.dataset.multipartId || root.id}`;
    }

    private writeLocal(root: HTMLFormElement, draft: DraftState): void {
        try {
            localStorage.setItem(this.storageKey(root), JSON.stringify(draft));
        } catch {
            /* quota / private mode */
        }
    }

    private readLocal(root: HTMLFormElement): DraftState | null {
        try {
            const raw = localStorage.getItem(this.storageKey(root));
            return raw ? JSON.parse(raw) as DraftState : null;
        } catch {
            return null;
        }
    }

    private setSaveState(root: HTMLFormElement, state: 'saving' | 'saved' | 'error', label: string): void {
        const pill = root.querySelector<HTMLElement>('[data-multipart-save]');
        const text = root.querySelector<HTMLElement>('[data-multipart-save-label]');
        if (!pill) return;
        pill.hidden = false;
        pill.classList.remove('saving', 'saved', 'error');
        pill.classList.add(state);
        if (text) text.textContent = label;
    }

    private emit(root: HTMLFormElement, type: string, detail: unknown): void {
        root.dispatchEvent(new CustomEvent(type, { bubbles: true, detail }));
    }

    private escape(s: string): string {
        return s.replace(/[&<>"']/g, (c) => (
            { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]!
        ));
    }
}
