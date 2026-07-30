/**
 * SubSidebarManager — contextual second-level sidebar (drill-in).
 *
 * Panels:   <section class="sub-sidebar" data-sub-sidebar="categories">…</section>
 * Triggers: <button data-action="sub-sidebar" data-target="categories">…</button>
 *
 * Behavior:
 *  - one panel open at a time; clicking the open trigger again closes it
 *  - [data-sub-sidebar-close] inside a panel closes (back button)
 *  - Escape and outside clicks close
 *  - trigger aria-expanded stays in sync (drives the active styling)
 *  - panel top/height/left track the main .sidebar box (subnav, sticky, rail)
 */

const OPEN_CLASS = 'sub-sidebar--open';
const MOBILE_MQ = '(max-width: 1020px)';

export class SubSidebarManager {
    private panels = new Map<string, HTMLElement>();
    private openId: string | null = null;
    private handleClick: (e: MouseEvent) => void;
    private handleKeydown: (e: KeyboardEvent) => void;
    private scheduleSync: () => void;
    private syncScheduled = false;
    private resizeObserver: ResizeObserver | null = null;

    constructor() {
        document.querySelectorAll<HTMLElement>('[data-sub-sidebar]').forEach(el => {
            const id = el.dataset.subSidebar;
            if (id) this.panels.set(id, el);
        });

        this.handleClick = (e: MouseEvent) => {
            const target = e.target as HTMLElement;

            const trigger = target.closest<HTMLElement>('[data-action="sub-sidebar"]');
            if (trigger) {
                const id = trigger.dataset.target ?? '';
                if (id) this.toggle(id);
                return;
            }

            if (target.closest('[data-sub-sidebar-close]')) {
                this.close();
                return;
            }

            // Outside click — clicks inside the open panel keep it open.
            if (this.openId && !target.closest('[data-sub-sidebar]')) {
                this.close();
            }
        };

        this.handleKeydown = (e: KeyboardEvent) => {
            if (e.key === 'Escape') this.close();
        };

        this.scheduleSync = () => {
            if (this.syncScheduled) return;
            this.syncScheduled = true;
            requestAnimationFrame(() => {
                this.syncScheduled = false;
                this.syncGeometry();
            });
        };

        document.addEventListener('click', this.handleClick);
        document.addEventListener('keydown', this.handleKeydown);
        window.addEventListener('scroll', this.scheduleSync, { passive: true, capture: true });
        window.addEventListener('resize', this.scheduleSync);

        const sidebar = this.sidebarEl();
        if (sidebar && typeof ResizeObserver !== 'undefined') {
            this.resizeObserver = new ResizeObserver(() => this.scheduleSync());
            this.resizeObserver.observe(sidebar);
        }

        this.syncGeometry();
    }

    open(id: string): void {
        const panel = this.panels.get(id);
        if (!panel || this.openId === id) return;
        if (this.openId) this.close();
        this.syncGeometry();
        panel.classList.add(OPEN_CLASS);
        this.openId = id;
        this.syncTriggers(id, true);
    }

    close(): void {
        if (!this.openId) return;
        this.panels.get(this.openId)?.classList.remove(OPEN_CLASS);
        this.syncTriggers(this.openId, false);
        this.openId = null;
    }

    toggle(id: string): void {
        this.openId === id ? this.close() : this.open(id);
    }

    getOpenId(): string | null {
        return this.openId;
    }

    private sidebarEl(): HTMLElement | null {
        return document.querySelector<HTMLElement>('.app-layout--with-sidebar > .sidebar, aside.sidebar, .sidebar');
    }

    /**
     * Dock every sub-sidebar to the main sidebar’s visible box so top + height
     * stay aligned through subnav, banners, sticky stick, and rail collapse.
     */
    private syncGeometry(): void {
        if (window.matchMedia(MOBILE_MQ).matches) {
            this.panels.forEach((panel) => {
                panel.style.top = '';
                panel.style.left = '';
                panel.style.height = '';
                panel.style.bottom = '';
            });
            return;
        }

        const sidebar = this.sidebarEl();
        if (!sidebar) return;

        const rect = sidebar.getBoundingClientRect();
        const top = `${Math.round(rect.top)}px`;
        const left = `${Math.round(rect.right)}px`;
        const height = `${Math.round(rect.height)}px`;

        this.panels.forEach((panel) => {
            panel.style.top = top;
            panel.style.left = left;
            panel.style.height = height;
            panel.style.bottom = 'auto';
        });
    }

    private syncTriggers(id: string, expanded: boolean): void {
        document
            .querySelectorAll<HTMLElement>(`[data-action="sub-sidebar"][data-target="${id}"]`)
            .forEach(t => t.setAttribute('aria-expanded', String(expanded)));
    }

    destroy(): void {
        this.close();
        document.removeEventListener('click', this.handleClick);
        document.removeEventListener('keydown', this.handleKeydown);
        window.removeEventListener('scroll', this.scheduleSync, true);
        window.removeEventListener('resize', this.scheduleSync);
        this.resizeObserver?.disconnect();
        this.resizeObserver = null;
        this.panels.forEach((panel) => {
            panel.style.top = '';
            panel.style.left = '';
            panel.style.height = '';
            panel.style.bottom = '';
        });
        this.panels.clear();
    }
}
