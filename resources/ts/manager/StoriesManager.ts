/**
 * StoriesManager — Instagram-style story rings + fullscreen viewer.
 *
 * Markup:
 *   <section data-stories>
 *     <button data-story-ring data-story-group='{"id":…,"items":[…]}'>…</button>
 *   </section>
 *   <section data-stories-pinned>…</section>
 *
 * API:
 *   stories.open(groups, startIndex?)
 *   stories.openFromRing(button)
 *   stories.close()
 *   stories.next() / stories.prev()
 *
 * Events:
 *   stories:open / stories:close / stories:item / stories:add / stories:reply
 */

export type StoryMediaType = 'image' | 'video';

export interface StoryItem {
    id: string;
    type: StoryMediaType;
    src: string;
    poster?: string;
    durationMs?: number;
    caption?: string;
    time?: string;
}

export interface StoryGroup {
    id: string;
    name: string;
    avatar?: string | null;
    cover?: string | null;
    state?: 'unseen' | 'seen' | 'live' | 'add' | 'pinned' | 'close';
    label?: string | null;
    self?: boolean;
    items: StoryItem[];
}

const DEFAULT_IMAGE_MS = 5000;
const ICONS = {
    close: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>`,
    mute: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="M16 9.5a3.5 3.5 0 0 1 0 5M18.5 7a7 7 0 0 1 0 10"/></svg>`,
    unmute: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="m16 9 6 6M22 9l-6 6"/></svg>`,
    pause: `<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>`,
    more: `<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>`,
    send: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 12h12M13 6l6 6-6 6"/></svg>`,
};

export class StoriesManager {
    private overlay: HTMLElement | null = null;
    private groups: StoryGroup[] = [];
    private groupIndex = 0;
    private itemIndex = 0;
    private raf = 0;
    private startedAt = 0;
    private elapsedBeforePause = 0;
    private durationMs = DEFAULT_IMAGE_MS;
    private paused = false;
    private muted = true;
    private holding = false;
    private seen = new Set<string>();
    private readonly storageKey = 'kami.stories.seen';

    constructor() {
        this.loadSeen();
        this.hydrateSeenRings();
        this.bindTrays();
        document.addEventListener('keydown', (e) => this.onKey(e));
    }

    /** Open viewer with an explicit group list. */
    open(groups: StoryGroup[], startIndex = 0, startItem = 0): void {
        const playable = groups.filter((g) => (g.items?.length ?? 0) > 0 && g.state !== 'add');
        if (!playable.length) return;
        this.groups = playable;
        this.groupIndex = Math.min(Math.max(0, startIndex), playable.length - 1);
        // Map startIndex if caller passed index into original array
        if (groups !== playable) {
            const target = groups[startIndex];
            const mapped = target ? playable.findIndex((g) => g.id === target.id) : -1;
            if (mapped >= 0) this.groupIndex = mapped;
        }
        this.itemIndex = Math.max(0, startItem);
        this.mount();
        this.renderItem(true);
        this.emit('stories:open', { groups: this.groups, groupIndex: this.groupIndex });
    }

    openFromRing(ring: HTMLElement): void {
        const group = parseGroup(ring);
        if (!group) {
            window.toasts?.error?.('Could not open this story.');
            return;
        }

        if (group.state === 'add' || (group.self && (!group.items || group.items.length === 0))) {
            ring.dispatchEvent(
                new CustomEvent('stories:add', { bubbles: true, detail: { group } }),
            );
            window.toasts?.info?.('Add to your story');
            return;
        }

        if (!group.items?.length) {
            window.toasts?.info?.('No stories yet');
            return;
        }

        // Collect siblings in the same tray (active feed OR pinned row)
        const tray = ring.closest('[data-stories-tray]');
        const rings = tray
            ? Array.from(tray.querySelectorAll<HTMLElement>('[data-story-ring]'))
            : [ring];
        const groups = rings
            .map(parseGroup)
            .filter((g): g is StoryGroup => !!g && (g.items?.length ?? 0) > 0 && g.state !== 'add');
        const start = Math.max(0, groups.findIndex((g) => g.id === group.id));
        this.open(groups.length ? groups : [group], start >= 0 ? start : 0);
    }

    close(): void {
        this.stopTimer();
        if (this.overlay) {
            const node = this.overlay;
            this.overlay = null;
            node.classList.remove('is-open');
            window.setTimeout(() => node.remove(), 180);
        }
        document.body.classList.remove('stories-open');
        this.emit('stories:close', {});
    }

    next(): void {
        const group = this.groups[this.groupIndex];
        if (!group) return;
        if (this.itemIndex < group.items.length - 1) {
            this.itemIndex += 1;
            this.renderItem(true);
            return;
        }
        if (this.groupIndex < this.groups.length - 1) {
            this.groupIndex += 1;
            this.itemIndex = 0;
            this.renderItem(true);
            return;
        }
        this.close();
    }

    prev(): void {
        if (this.itemIndex > 0) {
            this.itemIndex -= 1;
            this.renderItem(true);
            return;
        }
        if (this.groupIndex > 0) {
            this.groupIndex -= 1;
            const prev = this.groups[this.groupIndex];
            this.itemIndex = Math.max(0, (prev?.items.length ?? 1) - 1);
            this.renderItem(true);
            return;
        }
        // Restart current
        this.renderItem(true);
    }

    pause(): void {
        if (this.paused) return;
        this.paused = true;
        this.elapsedBeforePause += performance.now() - this.startedAt;
        this.stopRaf();
        this.overlay?.classList.add('stories-viewer-paused');
        const video = this.overlay?.querySelector<HTMLVideoElement>('video.stories-viewer-media');
        video?.pause();
    }

    resume(): void {
        if (!this.paused) return;
        this.paused = false;
        this.overlay?.classList.remove('stories-viewer-paused');
        this.startedAt = performance.now();
        const video = this.overlay?.querySelector<HTMLVideoElement>('video.stories-viewer-media');
        if (video) void video.play().catch(() => undefined);
        this.tick();
    }

    private bindTrays(): void {
        document.addEventListener('click', (e) => {
            const ring = (e.target as HTMLElement | null)?.closest<HTMLElement>('[data-story-ring]');
            if (!ring) return;
            e.preventDefault();
            this.openFromRing(ring);
        });
    }

    private mount(): void {
        this.stopTimer();
        this.overlay?.remove();
        this.overlay = null;

        const overlay = document.createElement('div');
        overlay.className = 'stories-viewer';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', 'Stories');
        overlay.innerHTML = `
            <div class="stories-viewer-frame" data-stories-frame>
                <div class="stories-viewer-progress" data-stories-progress></div>
                <div class="stories-viewer-gradient" aria-hidden="true"></div>
                <div class="stories-viewer-top">
                    <div class="stories-viewer-user">
                        <span class="stories-viewer-avatar" data-stories-avatar></span>
                        <div class="stories-viewer-meta">
                            <span class="stories-viewer-name" data-stories-name></span>
                            <span class="stories-viewer-time" data-stories-time></span>
                        </div>
                    </div>
                    <div class="stories-viewer-actions">
                        <button type="button" class="stories-viewer-btn" data-stories-mute aria-label="Mute">${ICONS.unmute}</button>
                        <button type="button" class="stories-viewer-btn" data-stories-close aria-label="Close">${ICONS.close}</button>
                    </div>
                </div>
                <p class="stories-viewer-caption" data-stories-caption></p>
                <button type="button" class="stories-viewer-tap stories-viewer-tap--prev" data-stories-prev aria-label="Previous"></button>
                <button type="button" class="stories-viewer-tap stories-viewer-tap--next" data-stories-next aria-label="Next"></button>
                <form class="stories-viewer-reply" data-stories-reply>
                    <input type="text" placeholder="Reply…" autocomplete="off" maxlength="280" />
                    <button type="submit" class="stories-viewer-btn" aria-label="Send">${ICONS.send}</button>
                </form>
            </div>
        `;

        const frame = overlay.querySelector<HTMLElement>('[data-stories-frame]')!;

        overlay.querySelector('[data-stories-close]')?.addEventListener('click', () => this.close());
        overlay.querySelector('[data-stories-prev]')?.addEventListener('click', () => this.prev());
        overlay.querySelector('[data-stories-next]')?.addEventListener('click', () => this.next());
        overlay.querySelector('[data-stories-mute]')?.addEventListener('click', () => this.toggleMute());

        overlay.querySelector('[data-stories-reply]')?.addEventListener('submit', (ev) => {
            ev.preventDefault();
            const input = overlay.querySelector<HTMLInputElement>('[data-stories-reply] input');
            const text = input?.value.trim() ?? '';
            if (!text) return;
            const group = this.groups[this.groupIndex];
            const item = group?.items[this.itemIndex];
            this.emit('stories:reply', { text, group, item });
            window.toasts?.success?.(`Reply sent to ${group?.name ?? 'story'}`);
            if (input) input.value = '';
        });

        // Hold to pause
        const onDown = () => {
            this.holding = true;
            this.pause();
        };
        const onUp = () => {
            if (!this.holding) return;
            this.holding = false;
            this.resume();
        };
        frame.addEventListener('pointerdown', (e) => {
            const t = e.target as HTMLElement;
            if (t.closest('.stories-viewer-btn, .stories-viewer-reply, .stories-viewer-tap')) return;
            onDown();
        });
        window.addEventListener('pointerup', onUp);
        window.addEventListener('pointercancel', onUp);

        // Swipe between groups
        let startX = 0;
        let startY = 0;
        frame.addEventListener('pointerdown', (e) => {
            startX = e.clientX;
            startY = e.clientY;
        });
        frame.addEventListener('pointerup', (e) => {
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            if (Math.abs(dx) < 56 || Math.abs(dx) < Math.abs(dy)) return;
            if (dx < 0) {
                if (this.groupIndex < this.groups.length - 1) {
                    this.groupIndex += 1;
                    this.itemIndex = 0;
                    this.renderItem(true);
                } else {
                    this.close();
                }
            } else if (this.groupIndex > 0) {
                this.groupIndex -= 1;
                this.itemIndex = 0;
                this.renderItem(true);
            }
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) this.close();
        });

        document.body.appendChild(overlay);
        document.body.classList.add('stories-open');
        this.overlay = overlay;
        requestAnimationFrame(() => overlay.classList.add('is-open'));
    }

    private renderItem(restart: boolean): void {
        if (!this.overlay) return;
        const group = this.groups[this.groupIndex];
        const item = group?.items[this.itemIndex];
        if (!group || !item) {
            this.close();
            return;
        }

        this.markSeen(group.id);
        this.updateRingStates(group.id);

        // Header
        const avatar = this.overlay.querySelector('[data-stories-avatar]');
        const name = this.overlay.querySelector('[data-stories-name]');
        const time = this.overlay.querySelector('[data-stories-time]');
        const caption = this.overlay.querySelector('[data-stories-caption]');
        if (avatar) {
            const src = group.avatar || group.cover;
            avatar.innerHTML = src
                ? `<img src="${esc(src)}" alt="">`
                : `<span class="stories-ring-avatar-fallback">${esc(initials(group.name))}</span>`;
        }
        if (name) name.textContent = group.name;
        if (time) time.textContent = item.time ?? '';
        if (caption) caption.textContent = item.caption ?? '';

        // Progress segments
        const progress = this.overlay.querySelector('[data-stories-progress]');
        if (progress) {
            progress.innerHTML = group.items
                .map((_, i) => {
                    const cls =
                        i < this.itemIndex ? 'is-done' : i === this.itemIndex ? 'is-active' : '';
                    return `<span class="stories-viewer-seg ${cls}" data-seg="${i}"><i></i></span>`;
                })
                .join('');
        }

        // Media
        const frame = this.overlay.querySelector('[data-stories-frame]');
        frame?.querySelector('.stories-viewer-media')?.remove();
        const media =
            item.type === 'video'
                ? Object.assign(document.createElement('video'), {
                      className: 'stories-viewer-media',
                      src: item.src,
                      playsInline: true,
                      muted: this.muted,
                      autoplay: true,
                      poster: item.poster ?? '',
                  })
                : Object.assign(document.createElement('img'), {
                      className: 'stories-viewer-media',
                      src: item.src,
                      alt: item.caption ?? '',
                  });
        frame?.insertBefore(media, frame.firstChild);

        this.paused = false;
        this.holding = false;
        this.elapsedBeforePause = 0;
        this.overlay.classList.remove('stories-viewer-paused');
        this.syncMuteBtn();

        if (item.type === 'video' && media instanceof HTMLVideoElement) {
            const start = () => {
                const dur = (media.duration && Number.isFinite(media.duration) ? media.duration : 0) * 1000;
                this.durationMs = item.durationMs ?? (dur > 0 ? dur : DEFAULT_IMAGE_MS);
                if (restart) this.beginTimer();
            };
            if (media.readyState >= 1) start();
            else media.addEventListener('loadedmetadata', start, { once: true });
            media.addEventListener('ended', () => this.next(), { once: true });
            void media.play().catch(() => undefined);
        } else {
            this.durationMs = item.durationMs ?? DEFAULT_IMAGE_MS;
            if (restart) this.beginTimer();
        }

        this.emit('stories:item', { group, item, groupIndex: this.groupIndex, itemIndex: this.itemIndex });
    }

    private beginTimer(): void {
        this.stopTimer();
        this.startedAt = performance.now();
        this.elapsedBeforePause = 0;
        this.tick();
    }

    private tick = (): void => {
        if (!this.overlay || this.paused) return;
        const elapsed = this.elapsedBeforePause + (performance.now() - this.startedAt);
        const p = Math.min(1, elapsed / this.durationMs);
        const fill = this.overlay.querySelector<HTMLElement>(
            `.stories-viewer-seg[data-seg="${this.itemIndex}"] > i`,
        );
        if (fill) fill.style.width = `${p * 100}%`;
        if (p >= 1) {
            this.next();
            return;
        }
        this.raf = requestAnimationFrame(this.tick);
    };

    private stopTimer(): void {
        this.stopRaf();
    }

    private stopRaf(): void {
        if (this.raf) cancelAnimationFrame(this.raf);
        this.raf = 0;
    }

    private toggleMute(): void {
        this.muted = !this.muted;
        const video = this.overlay?.querySelector<HTMLVideoElement>('video.stories-viewer-media');
        if (video) video.muted = this.muted;
        this.syncMuteBtn();
    }

    private syncMuteBtn(): void {
        const btn = this.overlay?.querySelector('[data-stories-mute]');
        if (!btn) return;
        btn.innerHTML = this.muted ? ICONS.unmute : ICONS.mute;
        btn.setAttribute('aria-label', this.muted ? 'Unmute' : 'Mute');
    }

    private onKey(e: KeyboardEvent): void {
        if (!this.overlay) return;
        if (e.key === 'Escape') this.close();
        if (e.key === 'ArrowRight') this.next();
        if (e.key === 'ArrowLeft') this.prev();
        if (e.key === ' ') {
            e.preventDefault();
            this.paused ? this.resume() : this.pause();
        }
    }

    private markSeen(groupId: string): void {
        this.seen.add(groupId);
        try {
            localStorage.setItem(this.storageKey, JSON.stringify([...this.seen]));
        } catch {
            /* private mode */
        }
    }

    private loadSeen(): void {
        try {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) return;
            const arr = JSON.parse(raw) as string[];
            if (Array.isArray(arr)) this.seen = new Set(arr);
        } catch {
            /* ignore */
        }
    }

    private updateRingStates(groupId: string): void {
        document.querySelectorAll<HTMLElement>(`[data-story-ring][data-story-id="${cssEscape(groupId)}"]`).forEach((ring) => {
            if (ring.dataset.storyState === 'live' || ring.dataset.storyState === 'add' || ring.dataset.storyState === 'pinned') {
                return;
            }
            ring.classList.remove('stories-ring--unseen', 'stories-ring--close', 'stories-ring--new');
            ring.classList.add('stories-ring--seen');
            ring.dataset.storyState = 'seen';
            ring.dataset.storyNew = '0';
        });
    }

    private hydrateSeenRings(): void {
        this.seen.forEach((id) => this.updateRingStates(id));
    }

    private emit(name: string, detail: unknown): void {
        document.dispatchEvent(new CustomEvent(name, { detail }));
    }
}

function parseGroup(el: HTMLElement): StoryGroup | null {
    const raw = el.getAttribute('data-story-group');
    if (!raw) return null;
    try {
        const data = JSON.parse(raw) as StoryGroup;
        if (!data.id) data.id = el.dataset.storyId ?? 'story';
        if (!Array.isArray(data.items)) data.items = [];
        return data;
    } catch {
        // Recover from accidental HTML double-encoding (&quot; / &amp;quot;)
        try {
            const textarea = document.createElement('textarea');
            textarea.innerHTML = raw;
            const data = JSON.parse(textarea.value) as StoryGroup;
            if (!data.id) data.id = el.dataset.storyId ?? 'story';
            if (!Array.isArray(data.items)) data.items = [];
            return data;
        } catch {
            return null;
        }
    }
}

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function initials(name: string): string {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('');
}

function cssEscape(value: string): string {
    if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') return CSS.escape(value);
    return value.replace(/"/g, '\\"');
}
