interface BannerManagerConfig {
    rootSelector: string;
    bottomRootSelector: string;
    bannerClass: string;
    maxBanners: number;
}

const defaultConfig: BannerManagerConfig = {
    rootSelector: '.banner-top-root',
    bottomRootSelector: '.banner-bottom-root',
    bannerClass: 'banner-top',
    maxBanners: 1,
};

type BannerType = 'primary' | 'secondary' | 'danger' | 'warning' | 'success' | 'info' | 'default';

export interface BannerAction {
    label: string;
    htmlElement: 'button' | 'a';
    href?: string;
    onClick?: () => void;
    icon?: string;
    type?: 'primary' | 'secondary' | 'danger' | 'warning' | 'success' | 'info' | 'default';
}

export interface BannerOptions {
    type: BannerType;
    content: string;
    template?: string;
    timer?: number;
    href?: string;
    icon?: string;
    background?: string;
    actions?: BannerAction[];
    position?: 'top' | 'bottom';
    /** Promo badge label when using the `promo` template. */
    badge?: string;
}

const defaultOptions: BannerOptions = {
    type: 'default',
    content: '',
    timer: 5000,
};

// ─── Templates ──────────────────────────────────────────────────────

type BannerTemplate = (options: BannerOptions, c: string) => string;

function escapeHtml(str: string): string {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

const ICON = {
    info: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`,
    warning: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
    success: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
    danger: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
    promo: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.59 13.41 11 22.01 2 13.01l9.59-9.59a2 2 0 0 1 2.82 0l6.18 6.18a2 2 0 0 1 0 2.81z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>`,
    megaphone: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 11 18-5v12L3 13v-2z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>`,
    spark: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8"/></svg>`,
};

function iconHtml(o: BannerOptions, c: string, fallback: string): string {
    if (o.icon) {
        return `<span class="${c}-icon">${o.icon}</span>`;
    }
    return `<span class="${c}-icon ${c}-icon--svg">${fallback}</span>`;
}

const BANNER_TEMPLATES: Record<string, BannerTemplate> = {
    default: (o, c) => `
        ${o.icon ? `<span class="${c}-icon">${o.icon}</span>` : ''}
        <span class="${c}-content">${o.content}</span>
    `,

    promo: (o, c) => `
        ${iconHtml(o, c, ICON.promo)}
        <span class="${c}-content">${o.content}</span>
        <span class="${c}-badge">${escapeHtml(String(o.badge ?? 'DEAL'))}</span>
    `,

    maintenance: (o, c) => `
        ${iconHtml(o, c, ICON.warning)}
        <span class="${c}-content">${o.content}</span>
    `,

    outage: (o, c) => `
        ${iconHtml(o, c, ICON.danger)}
        <span class="${c}-content">${o.content}</span>
    `,

    resolved: (o, c) => `
        ${iconHtml(o, c, ICON.success)}
        <span class="${c}-content">${o.content}</span>
    `,

    event: (o, c) => `
        ${iconHtml(o, c, ICON.spark)}
        <span class="${c}-content">${o.content}</span>
    `,

    announcement: (o, c) => `
        ${iconHtml(o, c, ICON.megaphone)}
        <span class="${c}-content">${o.content}</span>
    `,

    signup: (o, c) => `
        ${iconHtml(o, c, ICON.spark)}
        <span class="${c}-content">${o.content}</span>
    `,
};

// ─── Banner (single instance) ───────────────────────────────────────

let bannerCounter = 0;

class Banner {
    public readonly id: string;
    public readonly el: HTMLElement;

    private timer: ReturnType<typeof setTimeout> | null = null;
    private remainingTime: number;
    private startedAt: number = 0;
    private progressEl: HTMLElement | null = null;
    private onDismiss: (id: string) => void;

    constructor(
        private readonly root: HTMLElement,
        private readonly options: BannerOptions,
        private readonly bannerClass: string,
        onDismiss: (id: string) => void,
    ) {
        this.id = `banner-${++bannerCounter}`;
        this.remainingTime = options.timer ?? 0;
        this.onDismiss = onDismiss;
        this.el = this.render();
        this.progressEl = this.el.querySelector(`.${bannerClass}-progress`);
        this.bindEvents();
    }

    private render(): HTMLElement {
        const c = this.bannerClass;
        const o = this.options;
        const el = document.createElement('div');

        el.id = this.id;
        el.classList.add(c, `${c}--${o.type}`);
        if (o.template) {
            el.classList.add(`${c}--${o.template}`);
        }
        if (o.position === 'bottom') {
            el.classList.add(`${c}--bottom`);
        }

        if (o.background) {
            el.style.setProperty('--banner-bg', o.background);
        }

        if (o.timer) {
            el.style.setProperty('--banner-life', `${o.timer}ms`);
        }

        const templateFn = BANNER_TEMPLATES[o.template ?? 'default'] ?? BANNER_TEMPLATES.default;
        const bodyHtml = templateFn(o, c);

        const actionsHtml = (o.actions ?? []).map(action => {
            const tag = action.htmlElement;
            const classes = `${c}-action ${action.type ? `${c}-action--${action.type}` : ''}`;
            const href = tag === 'a' && action.href ? ` href="${escapeHtml(action.href)}"` : '';
            const iconHtmlInner = action.icon ? `<span class="${c}-action-icon">${action.icon}</span>` : '';
            return `<${tag} class="${classes}"${href} data-banner-action>${iconHtmlInner}${escapeHtml(action.label)}</${tag}>`;
        }).join('');

        const wrapperTag = o.href ? 'a' : 'div';
        const hrefAttr = o.href ? ` href="${escapeHtml(o.href)}"` : '';

        el.innerHTML = `
            <${wrapperTag} class="${c}-body"${hrefAttr}>
                ${bodyHtml}
                ${actionsHtml ? `<span class="${c}-actions">${actionsHtml}</span>` : ''}
            </${wrapperTag}>
            <button class="${c}-close" data-banner-close aria-label="Close" type="button">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
            ${o.timer ? `<div class="${c}-progress"></div>` : ''}
        `;

        this.root.appendChild(el);
        return el;
    }

    private bindEvents(): void {
        this.el.querySelector('[data-banner-close]')?.addEventListener('click', () => this.dismiss());

        const actions = this.options.actions ?? [];
        this.el.querySelectorAll('[data-banner-action]').forEach((btn, i) => {
            const action = actions[i];
            if (action?.onClick) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    action.onClick!();
                    this.dismiss();
                });
            }
        });

        this.el.addEventListener('pointerenter', () => this.pauseTimer());
        this.el.addEventListener('pointerleave', () => this.startTimer());
    }

    startTimer(): void {
        if (!this.remainingTime) return;
        this.clearTimer();
        this.startedAt = Date.now();
        this.timer = setTimeout(() => this.dismiss(), this.remainingTime);

        if (this.progressEl) {
            this.progressEl.style.animationPlayState = 'running';
        }
    }

    pauseTimer(): void {
        if (!this.timer) return;
        const elapsed = Date.now() - this.startedAt;
        this.remainingTime = Math.max(0, this.remainingTime - elapsed);
        this.clearTimer();

        if (this.progressEl) {
            this.progressEl.style.animationPlayState = 'paused';
        }
    }

    private clearTimer(): void {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    }

    dismiss(): void {
        if (this.el.classList.contains(`${this.bannerClass}--dismissing`)) return;
        this.clearTimer();
        this.el.classList.add(`${this.bannerClass}--dismissing`);

        this.el.addEventListener('animationend', () => {
            this.el.remove();
            this.onDismiss(this.id);
        }, { once: true });

        setTimeout(() => {
            if (this.el.parentNode) {
                this.el.remove();
                this.onDismiss(this.id);
            }
        }, 400);
    }
}

// ─── Manager ────────────────────────────────────────────────────────

export class BannerManager {
    private config: BannerManagerConfig;
    private root: HTMLElement;
    private bottomRoot: HTMLElement;
    private openBanners: Map<string, Banner> = new Map();
    private queue: BannerOptions[] = [];
    private queueIndicator: HTMLElement;

    constructor(overrides: Partial<BannerManagerConfig> = {}) {
        this.config = { ...defaultConfig, ...overrides };
        this.root = this.resolveRoot(this.config.rootSelector, 'top');
        this.bottomRoot = this.resolveRoot(this.config.bottomRootSelector, 'bottom');
        this.queueIndicator = this.createQueueIndicator();
    }

    /** Ensure a mount point exists (creates one when the page has no app-shell). */
    private resolveRoot(selector: string, position: 'top' | 'bottom'): HTMLElement {
        let root = document.querySelector<HTMLElement>(selector);

        if (!root) {
            root = document.createElement('div');
            root.className = selector.replace(/^\./, '');

            if (position === 'bottom') {
                document.body.appendChild(root);
            } else {
                const layout = document.querySelector('.app-layout, .page-wrapper');
                const main = layout?.querySelector('.main-content');
                if (layout && main) {
                    layout.insertBefore(root, main);
                } else if (layout) {
                    layout.prepend(root);
                } else {
                    document.body.prepend(root);
                }
            }
        }

        return root;
    }

    private createQueueIndicator(): HTMLElement {
        const el = document.createElement('div');
        el.classList.add('banner-queue-indicator');
        el.addEventListener('click', () => this.closeAll());
        this.root.appendChild(el);
        return el;
    }

    private updateQueueIndicator(): void {
        const count = this.queue.length;

        if (count > 0) {
            this.queueIndicator.textContent = `+${count} more`;
            this.queueIndicator.classList.add('banner-queue-indicator--visible');
        } else {
            this.queueIndicator.classList.remove('banner-queue-indicator--visible');
        }
    }

    create(options: Partial<BannerOptions> = {}): string {
        const bannerOptions: BannerOptions = { ...defaultOptions, ...options };

        // Bottom signup bars are sticky & persistent — don't queue behind top banners
        if (bannerOptions.position === 'bottom') {
            return this.show(bannerOptions);
        }

        if (this.openBanners.size >= this.config.maxBanners) {
            this.queue.push(bannerOptions);
            this.updateQueueIndicator();
            return '';
        }

        return this.show(bannerOptions);
    }

    /** Convenience: sticky bottom signup CTA that opens the guest signup modal. */
    showSignupPromo(content?: string, signupModalId = 'auth-signup'): string {
        return this.create({
            type: 'primary',
            template: 'signup',
            position: 'bottom',
            timer: 0,
            content: content ?? `Inscrivez-vous pour interagir avec les créateurs !`,
            actions: [
                {
                    label: "Je m'inscris",
                    htmlElement: 'button',
                    type: 'primary',
                    onClick: () => {
                        window.modals?.close?.('auth-login');
                        window.modals?.open?.(signupModalId);
                    },
                },
            ],
        });
    }

    private show(options: BannerOptions): string {
        const mount = options.position === 'bottom' ? this.bottomRoot : this.root;
        const banner = new Banner(
            mount,
            options,
            this.config.bannerClass,
            (id) => this.handleDismiss(id),
        );
        this.openBanners.set(banner.id, banner);
        if (options.position === 'bottom') {
            mount.appendChild(banner.el);
        } else {
            mount.insertBefore(banner.el, this.queueIndicator);
        }
        banner.startTimer();
        return banner.id;
    }

    private handleDismiss(id: string): void {
        this.openBanners.delete(id);
        this.drainQueue();
    }

    private drainQueue(): void {
        while (this.queue.length > 0 && this.openBanners.size < this.config.maxBanners) {
            const next = this.queue.shift()!;
            this.show(next);
        }
        this.updateQueueIndicator();
    }

    remove(id: string): void {
        this.openBanners.get(id)?.dismiss();
    }

    closeAll(): void {
        this.queue = [];
        this.updateQueueIndicator();
        [...this.openBanners.values()].forEach(banner => banner.dismiss());
    }

    consumeFlash(): void {
        const flash = (window as any).__APP__?.flash?.banners;
        if (!Array.isArray(flash)) return;

        flash.forEach((item: Partial<BannerOptions>) => {
            this.create(item);
        });
    }

    destroy(): void {
        this.closeAll();
        this.queueIndicator.remove();
    }
}
