/**
 * MegaMenuManager — mega menus under the app chrome.
 *
 * Markup (see components/mega-menu):
 *   [data-mega-menu]
 *     [data-mega-menu-trigger]
 *     [data-mega-menu-panel]
 *
 * Options on root:
 *   data-mega-menu-hover  — open on hover/focus
 *   data-mega-menu-dim    — toggle body.mega-menu-open + shared backdrop
 *
 * Sets --mega-menu-top / --mega-menu-left on :root so the panel + backdrop
 * sit under the sticky chrome and to the RIGHT of the desktop sidebar
 * (never painted over/under the sidebar column).
 */

const OPEN_CLASS = 'mega-menu--open';
const BODY_CLASS = 'mega-menu-open';
const HOVER_CLOSE_MS = 220;
const DESKTOP_MQ = '(min-width: 1021px)';

export class MegaMenuManager {
    private menus: MegaMenu[] = [];
    private backdrop: HTMLButtonElement | null = null;
    private handleDocPointer: (e: PointerEvent) => void;
    private handleKey: (e: KeyboardEvent) => void;
    private handleResize: () => void;
    private handleScroll: () => void;
    private layoutObserver: ResizeObserver | null = null;

    constructor() {
        this.ensureBackdrop();

        document.querySelectorAll<HTMLElement>('[data-mega-menu]').forEach((el) => {
            this.menus.push(new MegaMenu(el, this));
        });

        this.handleDocPointer = (e) => {
            const t = e.target as Node;
            const hit = this.menus.find((m) => m.contains(t));
            if (!hit) this.closeAll();
        };

        this.handleKey = (e) => {
            if (e.key === 'Escape') this.closeAll();
        };

        this.handleResize = () => this.repositionOpen();
        this.handleScroll = () => this.repositionOpen();

        document.addEventListener('pointerdown', this.handleDocPointer);
        document.addEventListener('keydown', this.handleKey);
        window.addEventListener('resize', this.handleResize);
        window.addEventListener('scroll', this.handleScroll, { passive: true });

        // Keep left edge in sync when the sidebar rail expands/collapses.
        if (typeof ResizeObserver !== 'undefined') {
            this.layoutObserver = new ResizeObserver(() => this.repositionOpen());
            const layout = document.querySelector('.app-layout--with-sidebar');
            const sidebar = layout?.querySelector('.sidebar');
            const main = layout?.querySelector('.main-content');
            if (sidebar) this.layoutObserver.observe(sidebar);
            if (main) this.layoutObserver.observe(main);
        }
    }

    openOnly(menu: MegaMenu): void {
        this.menus.forEach((m) => {
            if (m !== menu) m.close();
        });
        menu.open();
        this.syncBackdrop();
    }

    closeAll(): void {
        this.menus.forEach((m) => m.close());
        this.syncBackdrop();
    }

    notifyClosed(): void {
        this.syncBackdrop();
    }

    destroy(): void {
        document.removeEventListener('pointerdown', this.handleDocPointer);
        document.removeEventListener('keydown', this.handleKey);
        window.removeEventListener('resize', this.handleResize);
        window.removeEventListener('scroll', this.handleScroll);
        this.layoutObserver?.disconnect();
        this.layoutObserver = null;
        this.closeAll();
        this.backdrop?.remove();
        this.backdrop = null;
        this.menus = [];
    }

    private repositionOpen(): void {
        this.menus.filter((m) => m.isOpen()).forEach((m) => m.reposition());
    }

    private ensureBackdrop(): void {
        let el = document.querySelector<HTMLButtonElement>('.mega-menu-backdrop');
        if (!el) {
            el = document.createElement('button');
            el.type = 'button';
            el.className = 'mega-menu-backdrop';
            el.setAttribute('aria-label', 'Close menu');
            el.addEventListener('click', () => this.closeAll());
            document.body.appendChild(el);
        }
        this.backdrop = el;
    }

    private syncBackdrop(): void {
        const anyDimOpen = this.menus.some(
            (m) => m.isOpen() && m.el.hasAttribute('data-mega-menu-dim'),
        );
        document.body.classList.toggle(BODY_CLASS, anyDimOpen);
    }
}

class MegaMenu {
    readonly el: HTMLElement;
    private trigger: HTMLElement | null;
    private panel: HTMLElement | null;
    private subnav: HTMLElement | null;
    private manager: MegaMenuManager;
    private hover: boolean;
    private closeTimer: ReturnType<typeof setTimeout> | undefined;

    constructor(el: HTMLElement, manager: MegaMenuManager) {
        this.el = el;
        this.manager = manager;
        this.trigger = el.querySelector<HTMLElement>('[data-mega-menu-trigger]');
        this.panel = el.querySelector<HTMLElement>('[data-mega-menu-panel]');
        this.subnav = el.closest<HTMLElement>('.subnavbar');
        this.hover = el.hasAttribute('data-mega-menu-hover');

        const control =
            this.trigger?.querySelector<HTMLElement>('button, a, [tabindex]') ?? this.trigger;

        if (this.hover) {
            this.bindHover(control);
        } else {
            control?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (this.isOpen()) {
                    this.close();
                    this.manager.notifyClosed();
                } else {
                    this.manager.openOnly(this);
                }
            });
        }
    }

    contains(node: Node): boolean {
        if (this.el.contains(node) || this.panel?.contains(node)) return true;
        // Subheader is a travel zone while a hover mega is open.
        if (this.hover && this.isOpen() && this.subnav?.contains(node)) return true;
        return false;
    }

    isOpen(): boolean {
        return this.el.classList.contains(OPEN_CLASS);
    }

    open(): void {
        this.reposition();
        this.el.classList.add(OPEN_CLASS);
        this.syncExpanded(true);
    }

    close(): void {
        clearTimeout(this.closeTimer);
        this.el.classList.remove(OPEN_CLASS);
        this.syncExpanded(false);
    }

    reposition(): void {
        const nav = document.querySelector('.navbar');
        let top = 0;

        if (this.subnav) {
            top = this.subnav.getBoundingClientRect().bottom;
        } else {
            const anchor =
                this.trigger?.querySelector<HTMLElement>('button, a, .subnavbar-link') ??
                this.trigger ??
                this.el;
            top = anchor.getBoundingClientRect().bottom;
        }

        if (top < 0) {
            top = nav?.getBoundingClientRect().bottom ?? 0;
            if (this.subnav && top >= 0) top += this.subnav.getBoundingClientRect().height;
            top = Math.max(0, top);
        }

        const left = this.contentLeft();

        const topValue = `${Math.round(top)}px`;
        const leftValue = `${Math.round(left)}px`;
        // On :root so the shared backdrop can share the same edges.
        document.documentElement.style.setProperty('--mega-menu-top', topValue);
        document.documentElement.style.setProperty('--mega-menu-left', leftValue);
        this.el.style.setProperty('--mega-menu-top', topValue);
        this.el.style.setProperty('--mega-menu-left', leftValue);
    }

    /** Flush to the desktop sidebar’s right edge (no column-gap). */
    private contentLeft(): number {
        if (!window.matchMedia(DESKTOP_MQ).matches) return 0;

        const layout = document.querySelector('.app-layout--with-sidebar');
        if (!layout) return 0;

        const sidebar = layout.querySelector('.sidebar');
        if (sidebar) {
            // Floor + 1px overlap kills subpixel hairlines beside the sidebar.
            const right = sidebar.getBoundingClientRect().right;
            return right > 0 ? Math.max(0, Math.floor(right) - 1) : 0;
        }

        const main = layout.querySelector('.main-content');
        if (main) {
            const left = main.getBoundingClientRect().left;
            return left > 0 ? left : 0;
        }

        return 0;
    }

    private bindHover(control: HTMLElement | null): void {
        const cancelClose = () => clearTimeout(this.closeTimer);

        const scheduleClose = () => {
            cancelClose();
            this.closeTimer = setTimeout(() => {
                this.close();
                this.manager.notifyClosed();
            }, HOVER_CLOSE_MS);
        };

        const inZone = (target: EventTarget | null): boolean => {
            if (!(target instanceof Node)) return false;
            if (this.trigger?.contains(target) || this.panel?.contains(target)) return true;
            // Crossing the sticky subheader (between trigger and panel)
            // must not count as leaving — that path caused the open/close flash.
            if (this.subnav?.contains(target)) return true;
            return false;
        };

        const onLeave = (e: MouseEvent) => {
            if (!this.isOpen()) return;
            if (inZone(e.relatedTarget)) return;
            scheduleClose();
        };

        // Open only from the trigger — not from entering the subheader elsewhere.
        this.trigger?.addEventListener('mouseenter', () => {
            cancelClose();
            this.manager.openOnly(this);
        });
        this.trigger?.addEventListener('mouseleave', onLeave);

        this.panel?.addEventListener('mouseenter', cancelClose);
        this.panel?.addEventListener('mouseleave', onLeave);

        // Leaving the whole subheader toward page content closes the menu.
        this.subnav?.addEventListener('mouseleave', onLeave);

        control?.addEventListener('focus', () => {
            cancelClose();
            this.manager.openOnly(this);
        });
        control?.addEventListener('blur', (e) => {
            if (inZone((e as FocusEvent).relatedTarget)) return;
            scheduleClose();
        });
    }

    private syncExpanded(open: boolean): void {
        const value = open ? 'true' : 'false';
        this.trigger?.setAttribute('aria-expanded', value);
        const control = this.trigger?.querySelector<HTMLElement>('button, a') ?? this.trigger;
        control?.setAttribute('aria-expanded', value);
    }
}
