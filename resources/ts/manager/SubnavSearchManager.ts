/**
 * SubnavSearchManager — mobile/collapsed navbar search overlay.
 *
 * Below the layout breakpoint the center search is hidden. A magnifying-glass
 * control (on the subnavbar, or navbar when no subnav exists) opens the search
 * layer over the subheader. Close via ✕, Escape, or outside click on the dimmed
 * page (toggle itself excluded).
 *
 * Markup:
 *   [data-nav-search]            .nav-center search host
 *   [data-subnav-search-toggle]  open buttons
 *   [data-subnav-search-close]   close button inside the host
 *
 * Body class: `subnav-search-open`
 */

const MQ = '(max-width: 1020px)';
const OPEN_CLASS = 'subnav-search-open';

export class SubnavSearchManager {
    private media: MediaQueryList;
    private handleDocPointer: (e: PointerEvent) => void;
    private handleKey: (e: KeyboardEvent) => void;
    private handleMedia: () => void;

    constructor() {
        this.media = window.matchMedia(MQ);

        this.handleDocPointer = (e) => {
            if (!document.body.classList.contains(OPEN_CLASS)) return;
            const t = e.target as Node;
            const host = this.host();
            if (host?.contains(t)) return;
            if ((e.target as HTMLElement | null)?.closest?.('[data-subnav-search-toggle]')) return;
            this.close();
        };

        this.handleKey = (e) => {
            if (e.key === 'Escape' && document.body.classList.contains(OPEN_CLASS)) {
                e.preventDefault();
                this.close();
                return;
            }

            // "/" opens mobile overlay when the desktop bar is collapsed.
            if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) return;
            if (!this.media.matches) return;
            const target = e.target as HTMLElement;
            if (target.closest('input, textarea, select, [contenteditable]')) return;
            e.preventDefault();
            this.open();
        };

        this.handleMedia = () => {
            if (!this.media.matches) this.close();
        };

        document.querySelectorAll<HTMLElement>('[data-subnav-search-toggle]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggle();
            });
        });

        document.querySelectorAll<HTMLElement>('[data-subnav-search-close]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.close();
            });
        });

        document.addEventListener('pointerdown', this.handleDocPointer);
        document.addEventListener('keydown', this.handleKey);
        this.media.addEventListener('change', this.handleMedia);
    }

    open(): void {
        if (!this.media.matches) return;
        const host = this.host();
        if (!host) return;

        document.body.classList.add(OPEN_CLASS);
        this.syncToggle(true);

        const closeBtn = host.querySelector<HTMLElement>('[data-subnav-search-close]');
        if (closeBtn) closeBtn.hidden = false;

        queueMicrotask(() => {
            const input = host.querySelector<HTMLInputElement>('[data-search-input]');
            input?.focus();
        });
    }

    close(): void {
        document.body.classList.remove(OPEN_CLASS);
        this.syncToggle(false);

        const host = this.host();
        const closeBtn = host?.querySelector<HTMLElement>('[data-subnav-search-close]');
        if (closeBtn) closeBtn.hidden = true;

        const input = host?.querySelector<HTMLInputElement>('[data-search-input]');
        input?.blur();
    }

    toggle(): void {
        if (document.body.classList.contains(OPEN_CLASS)) this.close();
        else this.open();
    }

    destroy(): void {
        document.removeEventListener('pointerdown', this.handleDocPointer);
        document.removeEventListener('keydown', this.handleKey);
        this.media.removeEventListener('change', this.handleMedia);
        this.close();
    }

    private host(): HTMLElement | null {
        return document.querySelector<HTMLElement>('[data-nav-search]');
    }

    private syncToggle(open: boolean): void {
        document.querySelectorAll<HTMLElement>('[data-subnav-search-toggle]').forEach((btn) => {
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            btn.setAttribute('aria-label', open ? 'Close search' : 'Open search');
        });
    }
}
