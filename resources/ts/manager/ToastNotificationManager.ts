type ToastType = 'success' | 'error' | 'warning' | 'info';
type ToastPosition = 'top-right' | 'top-left' | 'bottom-right' | 'bottom-left' | 'top-center' | 'bottom-center';

interface ToastNotificationManagerConfig {
    rootSelector: string;
    toastClass: string;
    toastDuration: number;
    maxToasts: number;
    position: ToastPosition;
}

const defaultConfig: ToastNotificationManagerConfig = {
    rootSelector: '.toast-container',
    toastClass: 'toast-notification',
    toastDuration: 5000,
    maxToasts: 3,
    // Astryx viewport stacks from the top; bottom-right stays available via config.
    position: 'top-right',
};

interface ToastActions {
    label: string;
    htmlElement?: 'button' | 'a';
    href?: string;
    onClick?: () => void;
    icon?: string;
    primary?: boolean;
    type?: 'primary' | 'secondary' | 'danger' | 'warning' | 'info' | 'default';
    template?: string; //css classes and maybe an html template defined here ?
}

interface ToastOptions {
    message: string;
    type: ToastType;
    title?: string;
    duration?: number;
    actions?: ToastActions[];
}

const TOAST_ICONS: Record<ToastType, string> = {
    success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
};

let toastCounter = 0;

class Toast {
    public readonly id: string;
    public readonly el: HTMLElement;

    private timer: ReturnType<typeof setTimeout> | null = null;
    private remainingTime: number;
    private startedAt: number = 0;
    private onDismiss: (id: string) => void;

    constructor(options: ToastOptions, config: ToastNotificationManagerConfig, onDismiss: (id: string) => void) {
        this.id = `toast-${++toastCounter}`;
        // Astryx: error toasts persist until dismissed unless a duration is set.
        const fallback = options.type === 'error' ? 0 : config.toastDuration;
        this.remainingTime = options.duration ?? fallback;
        this.onDismiss = onDismiss;
        this.el = this.render(options, config);
        this.bindEvents(options);
    }

    private render(options: ToastOptions, config: ToastNotificationManagerConfig): HTMLElement {
        const c = config.toastClass;
        const el = document.createElement('div');
        el.id = this.id;
        el.classList.add(c, `${c}--${options.type}`);
        el.setAttribute('role', options.type === 'error' ? 'alert' : 'status');
        el.setAttribute('aria-live', options.type === 'error' ? 'assertive' : 'polite');
        el.setAttribute('aria-atomic', 'true');

        const actionsHtml = (options.actions ?? []).map(a =>
            `<button type="button" class="${c}-action ${a.primary ? `${c}-action--primary` : ''}" data-toast-action>${a.label}</button>`
        ).join('');

        // Astryx anatomy: content (icon+body) | endContent (actions + dismiss)
        el.innerHTML = `
            <div class="${c}-inner">
                <div class="${c}-content">
                    <div class="${c}-icon" aria-hidden="true">${TOAST_ICONS[options.type]}</div>
                    <div class="${c}-body">
                        ${options.title ? `<div class="${c}-title">${options.title}</div>` : ''}
                        <div class="${c}-message">${options.message}</div>
                    </div>
                </div>
                <div class="${c}-end">
                    ${actionsHtml}
                    <button type="button" class="${c}-close" data-toast-close aria-label="Dismiss notification">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        return el;
    }

    private bindEvents(options: ToastOptions): void {
        this.el.querySelector('[data-toast-close]')?.addEventListener('click', () => this.dismiss());

        if (options.actions) {
            this.el.querySelectorAll('[data-toast-action]').forEach((btn, i) => {
                const action = options.actions![i];
                if (action) {
                    btn.addEventListener('click', () => {
                        action.onClick?.();
                        this.dismiss();
                    });
                }
            });
        }

        this.el.addEventListener('pointerenter', () => this.pauseTimer());
        this.el.addEventListener('pointerleave', () => this.startTimer());
        this.el.addEventListener('focusin', () => this.pauseTimer());
        this.el.addEventListener('focusout', () => this.startTimer());
    }

    startTimer(): void {
        if (!this.remainingTime) return;
        this.clearTimer();
        this.startedAt = Date.now();
        this.timer = setTimeout(() => this.dismiss(), this.remainingTime);
    }

    pauseTimer(): void {
        if (!this.timer) return;
        const elapsed = Date.now() - this.startedAt;
        this.remainingTime = Math.max(0, this.remainingTime - elapsed);
        this.clearTimer();
    }

    private clearTimer(): void {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    }

    dismiss(): void {
        if (this.el.classList.contains('toast-dismissing')) return;
        this.clearTimer();
        this.el.classList.add('toast-dismissing');

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

// TODO connect and listen to real-time toast notification events from the backend
// (promotions, favorite model goes online, news, events, etc.)

export class ToastNotificationManager {
    private config: ToastNotificationManagerConfig;
    private root: HTMLElement;
    private openToasts: Map<string, Toast> = new Map();
    // why a queue of options ? why not a queue of toasts ?
    private queue: ToastOptions[] = [];
    private queueIndicator: HTMLElement;

    constructor(overrides: Partial<ToastNotificationManagerConfig> = {}) {
        this.config = { ...defaultConfig, ...overrides };
        this.root = this.resolveRoot();
        this.queueIndicator = this.createQueueIndicator();
    }

    private resolveRoot(): HTMLElement {
        let root = document.querySelector<HTMLElement>(this.config.rootSelector);

        if (!root) {
            root = document.createElement('div');
            root.classList.add(this.config.rootSelector.replace('.', ''));
            root.dataset.position = this.config.position;
            root.setAttribute('aria-live', 'polite');
            root.setAttribute('aria-atomic', 'false');

            const overlay = document.getElementById('overlay-root');
            (overlay ?? document.body).appendChild(root);
        }

        return root;
    }

    private createQueueIndicator(): HTMLElement {
        const el = document.createElement('div');
        el.classList.add('toast-queue-indicator');
        el.addEventListener('click', () => this.closeAll());
        this.root.appendChild(el);
        return el;
    }

    private updateQueueIndicator(): void {
        const count = this.queue.length;

        if (count > 0) {
            this.queueIndicator.textContent = `+${count} more`;
            this.queueIndicator.classList.add('toast-queue-indicator--visible');
        } else {
            this.queueIndicator.classList.remove('toast-queue-indicator--visible');
        }
    }

    create(message: string, type: ToastType, options: Partial<ToastOptions> = {}): string {
        const toastOptions: ToastOptions = { message, type, ...options };

        if (this.openToasts.size >= this.config.maxToasts) {
            this.queue.push(toastOptions);
            this.updateQueueIndicator();
            return '';
        }

        return this.show(toastOptions);
    }

    success(message: string, options: Partial<ToastOptions> = {}): string {
        return this.create(message, 'success', options);
    }

    error(message: string, options: Partial<ToastOptions> = {}): string {
        return this.create(message, 'error', options);
    }

    warning(message: string, options: Partial<ToastOptions> = {}): string {
        return this.create(message, 'warning', options);
    }

    info(message: string, options: Partial<ToastOptions> = {}): string {
        return this.create(message, 'info', options);
    }

    private show(options: ToastOptions): string {
        const toast = new Toast(options, this.config, (id) => this.handleDismiss(id));
        this.openToasts.set(toast.id, toast);
        this.root.insertBefore(toast.el, this.queueIndicator);
        toast.startTimer();
        return toast.id;
    }

    private handleDismiss(id: string): void {
        this.openToasts.delete(id);
        this.drainQueue();
    }

    private drainQueue(): void {
        while (this.queue.length > 0 && this.openToasts.size < this.config.maxToasts) {
            const next = this.queue.shift()!;
            this.show(next);
        }
        this.updateQueueIndicator();
    }

    remove(id: string): void {
        const toast = this.openToasts.get(id);
        if (!toast) return;
        toast.dismiss();
    }

    closeAll(): void {
        this.queue = [];
        this.updateQueueIndicator();
        const toasts = [...this.openToasts.values()];
        toasts.forEach(toast => toast.dismiss());
    }

    consumeFlash(): void {
        const flash = (window as any).__APP__?.flash?.toasts;
        if (!Array.isArray(flash)) return;

        flash.forEach((item: { type: ToastType; message: string; title?: string; duration?: number }) => {
            this.create(item.message, item.type, {
                title: item.title,
                duration: item.duration,
            });
        });
    }

    destroy(): void {
        this.closeAll();
        this.root.remove();
    }
}
