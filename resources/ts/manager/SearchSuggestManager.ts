/**
 * SearchSuggestManager — behavior for the search bar + suggestion panel.
 *
 * Markup contract (see components/search.blade.php):
 *   [data-search]           root
 *     [data-search-input]   the <input>
 *     [data-search-panel]   dropdown, toggled via [hidden]
 *       [data-search-item]  activatable rows (links or buttons)
 *
 * Behavior:
 *   - focus / typing opens the panel; Esc or outside click closes it
 *   - ArrowUp/ArrowDown move a [data-active] marker over visible items,
 *     Enter activates the marked item
 *   - "/" anywhere on the page focuses the first search input
 *   - every input dispatches a bubbling "search:query" CustomEvent
 *     ({ detail: { query, root } }) — apps listen to it to filter or
 *     fetch suggestions; this manager owns no data.
 */
export class SearchSuggestManager {
    private roots: HTMLElement[] = [];

    constructor(scope: ParentNode = document) {
        this.roots = Array.from(scope.querySelectorAll<HTMLElement>('[data-search]'));
        this.roots.forEach((root) => this.bind(root));

        if (this.roots.length > 0) {
            document.addEventListener('pointerdown', (e) => this.onOutsidePointer(e));
            document.addEventListener('keydown', (e) => this.onGlobalKey(e));
        }
    }

    private bind(root: HTMLElement): void {
        const input = root.querySelector<HTMLInputElement>('[data-search-input]');
        const panel = root.querySelector<HTMLElement>('[data-search-panel]');
        if (!input) return;

        input.addEventListener('focus', () => this.open(panel));
        input.addEventListener('input', () => {
            this.open(panel);
            root.dispatchEvent(new CustomEvent('search:query', {
                bubbles: true,
                detail: { query: input.value, root },
            }));
        });

        input.addEventListener('keydown', (e) => {
            if (!panel || panel.hidden) return;

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                this.move(panel, e.key === 'ArrowDown' ? 1 : -1);
            } else if (e.key === 'Enter') {
                const active = panel.querySelector<HTMLElement>('[data-search-item][data-active]');
                if (active) {
                    e.preventDefault();
                    active.click();
                }
            } else if (e.key === 'Escape') {
                this.close(panel);
                input.blur();
            }
        });

        root.querySelectorAll<HTMLElement>('[data-search-promo-toggle]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const promo = btn.closest<HTMLElement>('[data-search-promo]');
                if (!promo) return;
                const collapsed = promo.toggleAttribute('data-collapsed');
                btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            });
        });
    }

    private open(panel: HTMLElement | null): void {
        if (panel) panel.hidden = false;
    }

    private close(panel: HTMLElement | null): void {
        if (!panel) return;
        panel.hidden = true;
        this.clearActive(panel);
    }

    private move(panel: HTMLElement, dir: 1 | -1): void {
        const items = Array.from(panel.querySelectorAll<HTMLElement>('[data-search-item]'))
            .filter((el) => el.offsetParent !== null);
        if (items.length === 0) return;

        const current = items.findIndex((el) => el.hasAttribute('data-active'));
        const next = (current + dir + items.length) % items.length;

        this.clearActive(panel);
        items[next].setAttribute('data-active', '');
        items[next].scrollIntoView({ block: 'nearest' });
    }

    private clearActive(panel: HTMLElement): void {
        panel.querySelectorAll('[data-search-item][data-active]')
            .forEach((el) => el.removeAttribute('data-active'));
    }

    private onOutsidePointer(e: PointerEvent): void {
        this.roots.forEach((root) => {
            if (!root.contains(e.target as Node)) {
                this.close(root.querySelector<HTMLElement>('[data-search-panel]'));
            }
        });
    }

    private onGlobalKey(e: KeyboardEvent): void {
        if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) return;
        // Mobile overlay owns "/" — see SubnavSearchManager.
        if (window.matchMedia('(max-width: 1020px)').matches) return;
        const target = e.target as HTMLElement;
        if (target.closest('input, textarea, select, [contenteditable]')) return;

        const input = this.roots[0]?.querySelector<HTMLInputElement>('[data-search-input]');
        if (input) {
            e.preventDefault();
            input.focus();
        }
    }
}
