/**
 * PopoverManager — anchored panels (popovers, dropdown menus, hover cards).
 *
 * Markup contract:
 *   <div class="popover" data-popover [data-popover-hover]>
 *       <button data-popover-trigger>…</button>
 *       <div class="popover-panel popover-panel--bottom-start" data-popover-panel>…</div>
 *   </div>
 *
 * - click trigger toggles (default), or hover with `data-popover-hover`
 * - outside pointerdown and Escape close everything
 * - aria-expanded is kept in sync on the trigger
 */
interface PopoverManagerConfig {
    openClass: string;
    hoverCloseDelay: number;
}

const defaultConfig: PopoverManagerConfig = {
    openClass: 'popover--open',
    hoverCloseDelay: 150,
};

class Popover {
    readonly el: HTMLElement;
    private trigger: HTMLElement | null;
    private config: PopoverManagerConfig;
    private closeTimer: ReturnType<typeof setTimeout> | undefined;

    constructor(el: HTMLElement, config: PopoverManagerConfig) {
        this.el = el;
        this.config = config;
        this.trigger = el.querySelector<HTMLElement>('[data-popover-trigger]');

        if (el.hasAttribute('data-popover-hover')) {
            el.addEventListener('mouseenter', () => {
                clearTimeout(this.closeTimer);
                this.open();
            });
            el.addEventListener('mouseleave', () => {
                this.closeTimer = setTimeout(() => this.close(), this.config.hoverCloseDelay);
            });
            // Keyboard equivalent for hover popovers
            this.trigger?.addEventListener('focus', () => this.open());
            this.trigger?.addEventListener('blur', () => this.close());
        } else {
            this.trigger?.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggle();
            });
        }
    }

    isOpen(): boolean {
        return this.el.classList.contains(this.config.openClass);
    }

    open(): void {
        this.el.classList.add(this.config.openClass);
        this.trigger?.setAttribute('aria-expanded', 'true');
    }

    close(): void {
        this.el.classList.remove(this.config.openClass);
        this.trigger?.setAttribute('aria-expanded', 'false');
    }

    toggle(): void {
        this.isOpen() ? this.close() : this.open();
    }
}

export class PopoverManager {
    private config: PopoverManagerConfig;
    private popovers: Popover[] = [];

    constructor(overrides: Partial<PopoverManagerConfig> = {}) {
        this.config = { ...defaultConfig, ...overrides };

        document.querySelectorAll<HTMLElement>('[data-popover]').forEach((el) => {
            this.popovers.push(new Popover(el, this.config));
        });

        document.addEventListener('pointerdown', (e) => {
            const target = e.target as HTMLElement | null;
            if (target?.closest('[data-popover]')) return;
            this.closeAll();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeAll();
        });
    }

    /** Register popovers added to the DOM after initial load. */
    register(el: HTMLElement): void {
        this.popovers.push(new Popover(el, this.config));
    }

    closeAll(): void {
        this.popovers.forEach((p) => p.close());
    }
}
