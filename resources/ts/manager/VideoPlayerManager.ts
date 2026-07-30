/**
 * VideoPlayerManager — custom controls for [data-video-player].
 * Native HTML5 video only (mp4/webm). HLS can be layered later.
 */

interface VpInstance {
    root: HTMLElement;
    media: HTMLVideoElement;
    hideTimer: number | null;
    scrubbing: boolean;
}

const HIDE_MS = 2200;

export class VideoPlayerManager {
    private players = new Map<string, VpInstance>();

    constructor() {
        document.querySelectorAll<HTMLElement>('[data-video-player]').forEach((root) => {
            this.mount(root);
        });
    }

    mount(root: HTMLElement): void {
        const media = root.querySelector<HTMLVideoElement>('[data-vp-media]');
        if (!media) return;

        const id = root.id || `vp-${Math.random().toString(36).slice(2, 8)}`;
        if (!root.id) root.id = id;

        const instance: VpInstance = {
            root,
            media,
            hideTimer: null,
            scrubbing: false,
        };

        this.players.set(id, instance);
        this.bind(instance);
        this.sync(instance);
    }

    private bind(vp: VpInstance): void {
        const { root, media } = vp;

        const playBtn = root.querySelector<HTMLElement>('[data-vp-play]');
        const bigPlay = root.querySelector<HTMLElement>('[data-vp-bigplay]');
        const muteBtn = root.querySelector<HTMLElement>('[data-vp-mute]');
        const volume = root.querySelector<HTMLInputElement>('[data-vp-volume]');
        const seek = root.querySelector<HTMLInputElement>('[data-vp-seek]');
        const fsBtn = root.querySelector<HTMLElement>('[data-vp-fs]');
        const pipBtn = root.querySelector<HTMLElement>('[data-vp-pip]');
        const rateToggle = root.querySelector<HTMLElement>('[data-vp-rate-toggle]');
        const ratePanel = root.querySelector<HTMLElement>('[data-vp-rate-panel]');

        const togglePlay = () => {
            if (media.paused) void media.play().catch(() => undefined);
            else media.pause();
        };

        playBtn?.addEventListener('click', togglePlay);
        bigPlay?.addEventListener('click', togglePlay);
        media.addEventListener('click', togglePlay);

        media.addEventListener('play', () => this.sync(vp));
        media.addEventListener('pause', () => this.sync(vp));
        media.addEventListener('timeupdate', () => this.onTime(vp));
        media.addEventListener('progress', () => this.onBuffer(vp));
        media.addEventListener('loadedmetadata', () => this.sync(vp));
        media.addEventListener('volumechange', () => this.sync(vp));
        media.addEventListener('ratechange', () => this.sync(vp));
        media.addEventListener('waiting', () => root.classList.add('is-waiting'));
        media.addEventListener('playing', () => root.classList.remove('is-waiting'));

        muteBtn?.addEventListener('click', () => {
            media.muted = !media.muted;
            if (!media.muted && media.volume === 0) media.volume = 0.8;
        });

        volume?.addEventListener('input', () => {
            media.volume = Number(volume.value);
            media.muted = media.volume === 0;
        });

        const seekTo = (value: number) => {
            if (!Number.isFinite(media.duration) || media.duration <= 0) return;
            media.currentTime = (value / 1000) * media.duration;
        };

        seek?.addEventListener('pointerdown', () => {
            vp.scrubbing = true;
            root.classList.add('is-scrubbing');
        });
        seek?.addEventListener('input', () => {
            if (!seek) return;
            seekTo(Number(seek.value));
            this.onTime(vp);
        });
        const endScrub = () => {
            vp.scrubbing = false;
            root.classList.remove('is-scrubbing');
            this.armHide(vp);
        };
        seek?.addEventListener('pointerup', endScrub);
        seek?.addEventListener('change', endScrub);

        fsBtn?.addEventListener('click', () => this.toggleFullscreen(vp));
        document.addEventListener('fullscreenchange', () => this.sync(vp));

        if (pipBtn) {
            const supported = 'pictureInPictureEnabled' in document;
            if (!supported) pipBtn.hidden = true;
            else {
                pipBtn.addEventListener('click', async () => {
                    try {
                        if (document.pictureInPictureElement === media) {
                            await document.exitPictureInPicture();
                        } else {
                            await media.requestPictureInPicture();
                        }
                    } catch {
                        /* ignore */
                    }
                });
            }
        }

        rateToggle?.addEventListener('click', () => {
            if (!ratePanel) return;
            const open = ratePanel.hasAttribute('hidden');
            ratePanel.toggleAttribute('hidden', !open);
            rateToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        root.querySelectorAll<HTMLElement>('[data-vp-rate]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const rate = Number(btn.getAttribute('data-vp-rate'));
                if (!Number.isFinite(rate)) return;
                media.playbackRate = rate;
                ratePanel?.setAttribute('hidden', '');
                rateToggle?.setAttribute('aria-expanded', 'false');
                this.sync(vp);
            });
        });

        root.addEventListener('mousemove', () => this.reveal(vp));
        root.addEventListener('mouseleave', () => this.armHide(vp));
        root.addEventListener('focusin', () => this.reveal(vp));

        root.addEventListener('keydown', (e) => this.onKey(e, vp));

        // Click outside rate menu
        document.addEventListener('pointerdown', (e) => {
            if (!ratePanel || ratePanel.hasAttribute('hidden')) return;
            if (root.contains(e.target as Node) && (e.target as HTMLElement).closest('[data-vp-rate-menu]')) {
                return;
            }
            ratePanel.setAttribute('hidden', '');
            rateToggle?.setAttribute('aria-expanded', 'false');
        });
    }

    private onKey(e: KeyboardEvent, vp: VpInstance): void {
        if (e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement) return;
        const { media, root } = vp;
        this.reveal(vp);

        switch (e.key) {
            case ' ':
            case 'k':
            case 'K':
                e.preventDefault();
                if (media.paused) void media.play().catch(() => undefined);
                else media.pause();
                break;
            case 'ArrowRight':
                e.preventDefault();
                media.currentTime = Math.min(media.duration || 0, media.currentTime + 5);
                break;
            case 'ArrowLeft':
                e.preventDefault();
                media.currentTime = Math.max(0, media.currentTime - 5);
                break;
            case 'ArrowUp':
                e.preventDefault();
                media.volume = Math.min(1, media.volume + 0.05);
                media.muted = false;
                break;
            case 'ArrowDown':
                e.preventDefault();
                media.volume = Math.max(0, media.volume - 0.05);
                break;
            case 'm':
            case 'M':
                media.muted = !media.muted;
                break;
            case 'f':
            case 'F':
                void this.toggleFullscreen(vp);
                break;
            case 'Escape':
                root.querySelector<HTMLElement>('[data-vp-rate-panel]')?.setAttribute('hidden', '');
                break;
            default:
                break;
        }
    }

    private async toggleFullscreen(vp: VpInstance): Promise<void> {
        const { root } = vp;
        try {
            if (document.fullscreenElement === root) {
                await document.exitFullscreen();
            } else {
                await root.requestFullscreen();
            }
        } catch {
            /* ignore */
        }
    }

    private reveal(vp: VpInstance): void {
        vp.root.classList.add('is-idle');
        this.armHide(vp);
    }

    private armHide(vp: VpInstance): void {
        if (vp.hideTimer) window.clearTimeout(vp.hideTimer);
        if (vp.media.paused || vp.scrubbing) return;
        vp.hideTimer = window.setTimeout(() => {
            if (!vp.media.paused && !vp.scrubbing) {
                vp.root.classList.remove('is-idle');
            }
        }, HIDE_MS);
    }

    private onTime(vp: VpInstance): void {
        const { media, root, scrubbing } = vp;
        const seek = root.querySelector<HTMLInputElement>('[data-vp-seek]');
        const played = root.querySelector<HTMLElement>('[data-vp-played]');
        const current = root.querySelector<HTMLElement>('[data-vp-current]');
        const duration = root.querySelector<HTMLElement>('[data-vp-duration]');

        const pct = media.duration > 0 ? (media.currentTime / media.duration) * 1000 : 0;
        if (seek && !scrubbing) seek.value = String(Math.round(pct));
        if (played) played.style.width = `${pct / 10}%`;
        if (current) current.textContent = this.formatTime(media.currentTime);
        if (duration && !root.querySelector('[data-vp-live]')) {
            duration.textContent = this.formatTime(media.duration || 0);
        }
    }

    private onBuffer(vp: VpInstance): void {
        const { media, root } = vp;
        const buffer = root.querySelector<HTMLElement>('[data-vp-buffer]');
        if (!buffer || !media.duration) return;
        let end = 0;
        for (let i = 0; i < media.buffered.length; i++) {
            end = Math.max(end, media.buffered.end(i));
        }
        buffer.style.width = `${(end / media.duration) * 100}%`;
    }

    private sync(vp: VpInstance): void {
        const { root, media } = vp;
        root.classList.toggle('is-playing', !media.paused);
        root.classList.toggle('is-paused', media.paused);
        root.classList.toggle('is-ready', media.readyState >= 2);
        root.classList.toggle('is-fullscreen', document.fullscreenElement === root);
        if (media.paused) root.classList.add('is-idle');

        const playIcon = root.querySelector<HTMLElement>('[data-vp-icon-play]');
        const pauseIcon = root.querySelector<HTMLElement>('[data-vp-icon-pause]');
        playIcon?.toggleAttribute('hidden', !media.paused);
        pauseIcon?.toggleAttribute('hidden', media.paused);

        const playBtn = root.querySelector<HTMLElement>('[data-vp-play]');
        playBtn?.setAttribute('aria-label', media.paused ? 'Play' : 'Pause');

        const volIcon = root.querySelector<HTMLElement>('[data-vp-icon-vol]');
        const mutedIcon = root.querySelector<HTMLElement>('[data-vp-icon-muted]');
        const muted = media.muted || media.volume === 0;
        volIcon?.toggleAttribute('hidden', muted);
        mutedIcon?.toggleAttribute('hidden', !muted);

        const volume = root.querySelector<HTMLInputElement>('[data-vp-volume]');
        if (volume) volume.value = String(muted ? 0 : media.volume);

        const fsIcon = root.querySelector<HTMLElement>('[data-vp-icon-fs]');
        const fsExit = root.querySelector<HTMLElement>('[data-vp-icon-fs-exit]');
        const fs = document.fullscreenElement === root;
        fsIcon?.toggleAttribute('hidden', fs);
        fsExit?.toggleAttribute('hidden', !fs);

        const rateLabel = root.querySelector<HTMLElement>('[data-vp-rate-label]');
        if (rateLabel) {
            const rate = media.playbackRate;
            rateLabel.textContent = rate === 1 ? '1×' : `${rate}×`;
        }
        root.querySelectorAll<HTMLElement>('[data-vp-rate]').forEach((btn) => {
            const rate = Number(btn.getAttribute('data-vp-rate'));
            btn.classList.toggle('is-active', rate === media.playbackRate);
        });

        this.onTime(vp);
        this.onBuffer(vp);
    }

    private formatTime(seconds: number): string {
        if (!Number.isFinite(seconds) || seconds < 0) return '0:00';
        const s = Math.floor(seconds % 60);
        const m = Math.floor((seconds / 60) % 60);
        const h = Math.floor(seconds / 3600);
        const pad = (n: number) => String(n).padStart(2, '0');
        return h > 0 ? `${h}:${pad(m)}:${pad(s)}` : `${m}:${pad(s)}`;
    }
}
