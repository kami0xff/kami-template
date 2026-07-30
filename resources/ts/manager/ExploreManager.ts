/**
 * ExploreManager — mixed-media discovery feed.
 *
 * Markup (see components/explore/*):
 *   [data-explore]
 *     [data-explore-type] chips (aria-pressed)
 *     [data-explore-cat] chips (* = all)
 *     [data-explore-feed] > [data-explore-card][data-type][data-categories]
 *     [data-explore-sentinel] infinite-scroll trigger
 *
 * Events (bubble from root):
 *   explore:filter  { types: string[], categories: string[] }
 *   explore:need-more { types, categories, root }
 *
 * Client filter: card visible if type is on AND (no cats selected OR intersects).
 * Prefs persist to data-explore-storage localStorage key when set.
 */
export class ExploreManager {
    private roots: HTMLElement[] = [];
    private loading = new WeakSet<HTMLElement>();

    constructor(scope: ParentNode = document) {
        this.roots = Array.from(scope.querySelectorAll<HTMLElement>('[data-explore]'));
        this.roots.forEach((root) => this.bind(root));
    }

    private bind(root: HTMLElement): void {
        this.restore(root);

        root.querySelectorAll<HTMLElement>('[data-explore-type]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const on = btn.getAttribute('aria-pressed') !== 'true';
                this.setPressed(btn, on);
                this.sync(root);
            });
        });

        root.querySelector('[data-explore-types-all]')?.addEventListener('click', () => {
            root.querySelectorAll<HTMLElement>('[data-explore-type]').forEach((b) => this.setPressed(b, true));
            this.sync(root);
        });

        root.querySelector('[data-explore-types-none]')?.addEventListener('click', () => {
            root.querySelectorAll<HTMLElement>('[data-explore-type]').forEach((b) => this.setPressed(b, false));
            this.sync(root);
        });

        root.querySelectorAll<HTMLElement>('[data-explore-cat]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.exploreCat ?? '';
                if (id === '*') {
                    root.querySelectorAll<HTMLElement>('[data-explore-cat]').forEach((b) => {
                        this.setPressed(b, b.dataset.exploreCat === '*');
                    });
                } else {
                    const on = btn.getAttribute('aria-pressed') !== 'true';
                    this.setPressed(btn, on);
                    // any specific cat → turn off "All"
                    const all = root.querySelector<HTMLElement>('[data-explore-cat="*"]');
                    if (all) this.setPressed(all, false);
                    // if nothing specific left on → All on
                    const any = Array.from(root.querySelectorAll<HTMLElement>('[data-explore-cat]'))
                        .some((b) => b.dataset.exploreCat !== '*' && b.getAttribute('aria-pressed') === 'true');
                    if (!any && all) this.setPressed(all, true);
                }
                this.sync(root);
            });
        });

        const feed = root.querySelector<HTMLElement>('[data-explore-feed]');
        const layout = root.dataset.exploreLayout;
        if (feed && layout && !feed.className.includes('explore-feed--')) {
            feed.classList.add(`explore-feed--${layout}`);
        }

        const sentinel = root.querySelector<HTMLElement>('[data-explore-sentinel]');
        if (sentinel && feed && 'IntersectionObserver' in window) {
            const io = new IntersectionObserver(
                (entries) => {
                    if (!entries.some((e) => e.isIntersecting)) return;
                    if (this.loading.has(root)) return;
                    const state = this.state(root);
                    if (state.types.length === 0) return;
                    this.loading.add(root);
                    const status = root.querySelector<HTMLElement>('[data-explore-status]');
                    if (status) {
                        status.hidden = false;
                        status.textContent = 'Loading more…';
                    }
                    root.dispatchEvent(new CustomEvent('explore:need-more', {
                        bubbles: true,
                        detail: {
                            ...state,
                            root,
                            done: () => {
                                this.loading.delete(root);
                                if (status) status.hidden = true;
                            },
                        },
                    }));
                    window.setTimeout(() => this.loading.delete(root), 2500);
                },
                { root: feed, rootMargin: '200px 0px' },
            );
            io.observe(sentinel);
        }

        // Autoplay gif/video cards when visible inside the scrollport
        if (feed && 'IntersectionObserver' in window) {
            const mediaIo = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        const video = entry.target as HTMLVideoElement;
                        if (entry.isIntersecting) {
                            void video.play().catch(() => undefined);
                        } else {
                            video.pause();
                        }
                    });
                },
                { root: feed, threshold: 0.55 },
            );
            root.querySelectorAll<HTMLVideoElement>('[data-explore-video]').forEach((v) => mediaIo.observe(v));
        }

        this.bindPages(root);
        this.sync(root);
    }

    /** Horizontal gallery / manga page scroller + desktop arrows. */
    private bindPages(root: HTMLElement): void {
        root.querySelectorAll<HTMLElement>('[data-explore-pages]').forEach((scroller) => {
            if (scroller.dataset.exploreBound === '1') return;
            scroller.dataset.exploreBound = '1';

            const card = scroller.closest<HTMLElement>('[data-explore-card]');
            const update = () => this.updatePageIndex(scroller, card);

            scroller.addEventListener('scroll', () => update(), { passive: true });
            update();

            if (card) {
                card.classList.add('is-hint');
                window.setTimeout(() => card.classList.remove('is-hint'), 2200);

                card.querySelector('[data-explore-page-prev]')?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.stepPage(scroller, -1);
                });
                card.querySelector('[data-explore-page-next]')?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.stepPage(scroller, 1);
                });
            }

            // Pointer drag for desktop horizontal paging (without trapping vertical wheel)
            this.bindPageDrag(scroller, update);
        });
    }

    private stepPage(scroller: HTMLElement, dir: -1 | 1): void {
        const w = Math.max(scroller.clientWidth, 1);
        const total = scroller.querySelectorAll('[data-explore-page]').length;
        const current = Math.round(scroller.scrollLeft / w);
        const next = Math.min(total - 1, Math.max(0, current + dir));
        scroller.scrollTo({ left: next * w, behavior: 'smooth' });
    }

    private bindPageDrag(scroller: HTMLElement, update: () => void): void {
        let active = false;
        let startX = 0;
        let startScroll = 0;
        let moved = false;

        const onDown = (e: PointerEvent) => {
            if (e.pointerType === 'touch') return; // native touch scroll / pan-y to feed
            if (e.button !== 0) return;
            active = true;
            moved = false;
            startX = e.clientX;
            startScroll = scroller.scrollLeft;
            scroller.setPointerCapture(e.pointerId);
        };

        const onMove = (e: PointerEvent) => {
            if (!active) return;
            const dx = e.clientX - startX;
            if (Math.abs(dx) > 4) moved = true;
            scroller.scrollLeft = startScroll - dx;
            update();
        };

        const onUp = (e: PointerEvent) => {
            if (!active) return;
            active = false;
            try {
                scroller.releasePointerCapture(e.pointerId);
            } catch {
                /* ignore */
            }
            if (!moved) return;
            // Snap to nearest page after drag
            const w = Math.max(scroller.clientWidth, 1);
            const idx = Math.round(scroller.scrollLeft / w);
            scroller.scrollTo({ left: idx * w, behavior: 'smooth' });
            update();
        };

        scroller.addEventListener('pointerdown', onDown);
        scroller.addEventListener('pointermove', onMove);
        scroller.addEventListener('pointerup', onUp);
        scroller.addEventListener('pointercancel', onUp);
    }

    private updatePageIndex(scroller: HTMLElement, card: HTMLElement | null): void {
        const pages = scroller.querySelectorAll('[data-explore-page]');
        const total = pages.length;
        if (total === 0) return;
        const w = Math.max(scroller.clientWidth, 1);
        const index = Math.min(total, Math.max(1, Math.round(scroller.scrollLeft / w) + 1));
        const label = card?.querySelector('[data-explore-page-label]');
        const stat = card?.querySelector('[data-explore-page-stat]');
        const text = `${index} / ${total}`;
        if (label) label.textContent = text;
        if (stat) stat.textContent = text;
        card?.querySelectorAll<HTMLElement>('[data-explore-dot]').forEach((dot) => {
            const i = Number(dot.dataset.exploreDot ?? -1);
            dot.classList.toggle('is-active', i === index - 1);
        });

        const prev = card?.querySelector<HTMLButtonElement>('[data-explore-page-prev]');
        const next = card?.querySelector<HTMLButtonElement>('[data-explore-page-next]');
        if (prev) {
            const disabled = index <= 1;
            prev.disabled = disabled;
            prev.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        }
        if (next) {
            const disabled = index >= total;
            next.disabled = disabled;
            next.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        }
    }

    private setPressed(btn: HTMLElement, on: boolean): void {
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        btn.classList.toggle('is-on', on);
        btn.classList.toggle('is-off', !on);
    }

    private state(root: HTMLElement): { types: string[]; categories: string[] } {
        const types = Array.from(root.querySelectorAll<HTMLElement>('[data-explore-type]'))
            .filter((b) => b.getAttribute('aria-pressed') === 'true')
            .map((b) => b.dataset.exploreType ?? '')
            .filter(Boolean);

        const allOn = root.querySelector<HTMLElement>('[data-explore-cat="*"]')?.getAttribute('aria-pressed') === 'true';
        const categories = allOn
            ? []
            : Array.from(root.querySelectorAll<HTMLElement>('[data-explore-cat]'))
                .filter((b) => b.dataset.exploreCat !== '*' && b.getAttribute('aria-pressed') === 'true')
                .map((b) => b.dataset.exploreCat ?? '')
                .filter(Boolean);

        return { types, categories };
    }

    private sync(root: HTMLElement): void {
        const { types, categories } = this.state(root);
        const typeSet = new Set(types);
        const catSet = new Set(categories);
        let visible = 0;

        root.querySelectorAll<HTMLElement>('[data-explore-card]').forEach((card) => {
            const t = card.dataset.type ?? '';
            const cats = (card.dataset.categories ?? '').split(',').map((s) => s.trim()).filter(Boolean);
            const typeOk = typeSet.has(t);
            const catOk = catSet.size === 0 || cats.some((c) => catSet.has(c));
            const show = typeOk && catOk;
            card.hidden = !show;
            if (show) visible += 1;
        });

        const empty = root.querySelector<HTMLElement>('[data-explore-empty]');
        if (empty) empty.hidden = visible > 0;

        // Categories require at least one media type when flagged
        const catsRow = root.querySelector<HTMLElement>('[data-explore-cats-row]');
        if (catsRow?.hasAttribute('data-require-types')) {
            const disabled = types.length === 0;
            catsRow.dataset.disabled = disabled ? 'true' : 'false';
            const hint = root.querySelector<HTMLElement>('[data-explore-cats-hint]');
            if (hint) hint.hidden = !disabled;
        }

        this.persist(root, { types, categories });

        root.dispatchEvent(new CustomEvent('explore:filter', {
            bubbles: true,
            detail: { types, categories, visible },
        }));
    }

    private persist(root: HTMLElement, state: { types: string[]; categories: string[] }): void {
        const key = root.dataset.exploreStorage;
        if (!key) return;
        try {
            localStorage.setItem(key, JSON.stringify(state));
        } catch {
            /* ignore quota */
        }
    }

    private restore(root: HTMLElement): void {
        const key = root.dataset.exploreStorage;
        if (!key) return;
        try {
            const raw = localStorage.getItem(key);
            if (!raw) return;
            const parsed = JSON.parse(raw) as { types?: string[]; categories?: string[] };
            if (Array.isArray(parsed.types)) {
                const set = new Set(parsed.types);
                root.querySelectorAll<HTMLElement>('[data-explore-type]').forEach((b) => {
                    this.setPressed(b, set.has(b.dataset.exploreType ?? ''));
                });
            }
            if (Array.isArray(parsed.categories)) {
                const set = new Set(parsed.categories);
                const all = root.querySelector<HTMLElement>('[data-explore-cat="*"]');
                if (set.size === 0) {
                    root.querySelectorAll<HTMLElement>('[data-explore-cat]').forEach((b) => {
                        this.setPressed(b, b.dataset.exploreCat === '*');
                    });
                } else {
                    if (all) this.setPressed(all, false);
                    root.querySelectorAll<HTMLElement>('[data-explore-cat]').forEach((b) => {
                        if (b.dataset.exploreCat === '*') return;
                        this.setPressed(b, set.has(b.dataset.exploreCat ?? ''));
                    });
                }
            }
        } catch {
            /* ignore */
        }
    }

    /** Demo helper: append HTML cards into the feed (before sentinel). */
    appendCards(root: HTMLElement, html: string): void {
        const feed = root.querySelector<HTMLElement>('[data-explore-feed]');
        const sentinel = root.querySelector<HTMLElement>('[data-explore-sentinel]');
        if (!feed) return;
        const wrap = document.createElement('div');
        wrap.innerHTML = html;
        const nodes = Array.from(wrap.children);
        nodes.forEach((n) => {
            if (sentinel) feed.insertBefore(n, sentinel);
            else feed.appendChild(n);
            const video = n.querySelector?.('[data-explore-video]') as HTMLVideoElement | null;
            if (video && 'IntersectionObserver' in window) {
                // re-bound lightly: play when later synced via existing observer is enough for demo
            }
        });
        const status = root.querySelector<HTMLElement>('[data-explore-status]');
        if (status) status.hidden = true;
        this.bindPages(root);
        this.sync(root);
    }
}
