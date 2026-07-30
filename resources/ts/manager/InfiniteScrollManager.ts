/**
 * InfiniteScrollManager — sentinel-driven feed loading.
 *
 * Markup (see components/infinite-scroll/index.blade.php):
 *   [data-infinite-scroll]
 *     [data-infinite-scroll-items]
 *     [data-infinite-scroll-status]
 *     [data-infinite-scroll-end]
 *     [data-infinite-scroll-error] + [data-infinite-scroll-retry]
 *     [data-infinite-scroll-more]
 *     [data-infinite-scroll-sentinel]
 *
 * Attrs: data-page, data-has-more, data-mode=auto|button, data-url, data-root-margin,
 *        data-demo (design kit fake pages)
 *
 * Events (bubble from root):
 *   infinite-scroll:load { page, root, done({ html?, hasMore? }), fail(message?) }
 */
export type InfiniteScrollDonePayload = {
    html?: string;
    hasMore?: boolean;
};

type InfiniteRoot = HTMLElement & {
    __infiniteBound?: boolean;
    __infiniteIo?: IntersectionObserver | null;
};

function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export class InfiniteScrollManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-infinite-scroll]').forEach((root) => this.mount(root));
    }

    mount(root: HTMLElement): void {
        const el = root as InfiniteRoot;
        if (el.__infiniteBound) return;
        el.__infiniteBound = true;

        this.syncUi(el);

        el.querySelector('[data-infinite-scroll-more]')?.addEventListener('click', () => this.load(el));
        el.querySelector('[data-infinite-scroll-retry]')?.addEventListener('click', () => this.load(el));

        const mode = this.effectiveMode(el);
        if (mode === 'auto' && 'IntersectionObserver' in window) {
            el.classList.add('is-auto-ready');
            const margin = el.dataset.rootMargin || '240px 0px';
            const scrollRoot = this.resolveScrollRoot(el);
            const io = new IntersectionObserver(
                (entries) => {
                    if (!entries.some((e) => e.isIntersecting)) return;
                    this.load(el);
                },
                { root: scrollRoot, rootMargin: margin, threshold: 0 },
            );
            const sentinel = el.querySelector<HTMLElement>('[data-infinite-scroll-sentinel]');
            if (sentinel) io.observe(sentinel);
            el.__infiniteIo = io;
        }
    }

    /** Nearest overflow scroller, or viewport when unset / data-scroll-root="viewport". */
    private resolveScrollRoot(el: HTMLElement): Element | null {
        const sel = el.dataset.scrollRoot;
        if (sel === 'viewport') return null;
        if (sel) return document.querySelector(sel);

        let node: HTMLElement | null = el.parentElement;
        while (node && node !== document.documentElement) {
            const style = window.getComputedStyle(node);
            const oy = style.overflowY;
            if (oy === 'auto' || oy === 'scroll' || oy === 'overlay') return node;
            node = node.parentElement;
        }
        return null;
    }

    /** Public: trigger a load (e.g. after filters change). */
    load(root: HTMLElement): void {
        if (!this.hasMore(root) || root.classList.contains('is-loading')) return;

        const page = this.page(root) + 1;
        this.setLoading(root, true);
        this.clearError(root);

        const done = (payload: InfiniteScrollDonePayload = {}) => this.complete(root, page, payload);
        const fail = (message?: string) => this.fail(root, message);

        const handled = root.dispatchEvent(
            new CustomEvent('infinite-scroll:load', {
                bubbles: true,
                cancelable: true,
                detail: { page, root, done, fail },
            }),
        );

        // preventDefault → listener owns the request lifecycle
        if (!handled) return;

        if (root.dataset.demo != null) {
            this.loadDemo(root, page, done, fail);
            return;
        }

        const url = root.dataset.url;
        if (url) {
            void this.fetchPage(root, url, page, done, fail);
        }
        // else: wait for an event listener to call done() / fail()
    }

    private async fetchPage(
        _root: HTMLElement,
        urlPattern: string,
        page: number,
        done: (p: InfiniteScrollDonePayload) => void,
        fail: (message?: string) => void,
    ): Promise<void> {
        const url = urlPattern.replaceAll('{page}', String(page));
        try {
            const res = await fetch(url, {
                headers: {
                    Accept: 'application/json, text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const type = res.headers.get('content-type') || '';
            if (type.includes('application/json')) {
                const data = (await res.json()) as { html?: string; hasMore?: boolean; message?: string };
                if (typeof data.html !== 'string' && data.hasMore === false) {
                    done({ hasMore: false });
                    return;
                }
                done({ html: data.html ?? '', hasMore: data.hasMore });
                return;
            }

            const html = await res.text();
            done({ html, hasMore: html.trim().length > 0 });
        } catch (err) {
            fail(err instanceof Error ? err.message : 'Request failed');
        }
    }

    private loadDemo(
        root: HTMLElement,
        page: number,
        done: (p: InfiniteScrollDonePayload) => void,
        fail: (message?: string) => void,
    ): void {
        const max = Number(root.dataset.demoMax || 4);
        window.setTimeout(() => {
            if (root.dataset.demoFail === '1' && page === 2) {
                fail('Demo error');
                root.dataset.demoFail = '0';
                return;
            }
            const start = (page - 1) * 4 + 1;
            const cards = Array.from({ length: 4 }, (_, i) => {
                const n = start + i;
                return `<article class="infinite-scroll-demo-card" aria-setsize="-1" aria-posinset="${n}">
                    <strong>Feed item ${n}</strong>
                    <span>Loaded on page ${page}</span>
                </article>`;
            }).join('');
            done({ html: cards, hasMore: page < max });
        }, 650);
    }

    private complete(root: HTMLElement, page: number, payload: InfiniteScrollDonePayload): void {
        if (payload.html) {
            const items = root.querySelector<HTMLElement>('[data-infinite-scroll-items]');
            if (items) items.insertAdjacentHTML('beforeend', payload.html);
        }

        root.dataset.page = String(page);
        const hasMore = payload.hasMore ?? this.hasMore(root);
        root.dataset.hasMore = hasMore ? '1' : '0';
        this.setLoading(root, false);
        this.syncUi(root);

        if (hasMore && this.effectiveMode(root) === 'auto') {
            requestAnimationFrame(() => {
                const sentinel = root.querySelector<HTMLElement>('[data-infinite-scroll-sentinel]');
                if (!sentinel) return;
                const scrollRoot = this.resolveScrollRoot(root);
                const rootRect = scrollRoot
                    ? scrollRoot.getBoundingClientRect()
                    : { top: 0, bottom: window.innerHeight };
                const rect = sentinel.getBoundingClientRect();
                if (rect.top <= rootRect.bottom + 240) this.load(root);
            });
        }
    }

    private fail(root: HTMLElement, message?: string): void {
        this.setLoading(root, false);
        root.classList.add('is-error');
        const err = root.querySelector<HTMLElement>('[data-infinite-scroll-error]');
        const text = root.querySelector<HTMLElement>('[data-infinite-scroll-error-text]');
        if (text && message) text.textContent = message;
        else if (text) text.textContent = root.dataset.errorText || 'Couldn’t load more. Try again.';
        if (err) err.hidden = false;
        const more = root.querySelector<HTMLButtonElement>('[data-infinite-scroll-more]');
        if (more) more.hidden = false;
    }

    private clearError(root: HTMLElement): void {
        root.classList.remove('is-error');
        const err = root.querySelector<HTMLElement>('[data-infinite-scroll-error]');
        if (err) err.hidden = true;
    }

    private setLoading(root: HTMLElement, on: boolean): void {
        root.classList.toggle('is-loading', on);
        root.setAttribute('aria-busy', on ? 'true' : 'false');
        const status = root.querySelector<HTMLElement>('[data-infinite-scroll-status]');
        if (status) status.hidden = !on;
        const more = root.querySelector<HTMLButtonElement>('[data-infinite-scroll-more]');
        if (more) more.disabled = on;
    }

    private syncUi(root: HTMLElement): void {
        const more = this.hasMore(root);
        root.classList.toggle('is-end', !more);
        const end = root.querySelector<HTMLElement>('[data-infinite-scroll-end]');
        const btn = root.querySelector<HTMLButtonElement>('[data-infinite-scroll-more]');
        if (end) end.hidden = more;
        if (btn) {
            btn.hidden = !more;
            btn.disabled = false;
        }
        if (!more) this.clearError(root);
    }

    private page(root: HTMLElement): number {
        return Math.max(1, Number(root.dataset.page || 1));
    }

    private hasMore(root: HTMLElement): boolean {
        return root.dataset.hasMore !== '0';
    }

    private effectiveMode(root: HTMLElement): 'auto' | 'button' {
        if (prefersReducedMotion()) return 'button';
        return root.dataset.mode === 'button' ? 'button' : 'auto';
    }
}
