interface PanelConfig {
    panelSelector: string;
    triggerSelector: string;
    activeClass: string;
}

const defaultConfig: PanelConfig = {
    panelSelector: '.panel',
    // Triggers that own open/close — outside-dismiss must ignore these so
    // pointerdown doesn't close before the click handler toggles.
    triggerSelector: '.nav-actions, [data-action="panel"], [data-panel-trigger]',
    activeClass: 'panel-active',
};

// TODO draggable panels (small window engine)
// TODO event bus integration
// TODO child panel nesting (max 1 level)

export class PanelManager {
    private config: PanelConfig;
    private multiPanelMode: boolean;
    private openPanels: Set<string> = new Set();
    private history: string[] = [];
    private handlePointerDown: (e: PointerEvent) => void;
    private handleKeyDown: (e: KeyboardEvent) => void;

    constructor(
        overrides: Partial<PanelConfig> = {},
        options: { multiPanelMode?: boolean; childPanelMode?: boolean } = {},
    ) {
        this.config = { ...defaultConfig, ...overrides };
        this.multiPanelMode = options.multiPanelMode ?? false;

        this.handlePointerDown = (e: PointerEvent) => {
            const path = e.composedPath().filter((n): n is Element => n instanceof Element);
            if (path.some((el) => el.matches?.(this.config.panelSelector))) return;

            // Let the trigger's own toggle/open handler run — do not close here.
            // Also treat aria-controls → open panel as a trigger (avatar menus etc.).
            if (path.some((el) => {
                if (el.matches?.(this.config.triggerSelector)) return true;
                const controls = el.getAttribute?.('aria-controls');
                return !!controls && this.openPanels.has(controls);
            })) {
                return;
            }

            if (this.openPanels.size > 0) this.closeAll();
        };

        this.handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape') this.closeAll();
        };

        document.addEventListener('pointerdown', this.handlePointerDown);
        document.addEventListener('keydown', this.handleKeyDown);
    }

    open(id: string): void {
        const panel = document.getElementById(id);
        if (!panel) return;
        if (this.openPanels.has(id)) return;
        if (!this.multiPanelMode) this.closeAll();
        panel.classList.add(this.config.activeClass);
        this.openPanels.add(id);
        this.syncTriggers(id, true);
    }

    close(id: string): void {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.classList.remove(this.config.activeClass);
        this.openPanels.delete(id);
        this.syncTriggers(id, false);
    }

    toggle(id: string): void {
        this.openPanels.has(id) ? this.close(id) : this.open(id);
    }

    closeAll(): void {
        const toClose = [...this.openPanels];
        toClose.forEach(id => this.close(id));
    }

    isOpen(id: string): boolean {
        return this.openPanels.has(id);
    }

    getAllOpenPanels(): string[] {
        return [...this.openPanels];
    }

    getConfig(): PanelConfig {
        return { ...this.config };
    }

    getHistory(): string[] {
        return [...this.history];
    }

    destroy(): void {
        document.removeEventListener('pointerdown', this.handlePointerDown);
        document.removeEventListener('keydown', this.handleKeyDown);
        this.closeAll();
    }

    private syncTriggers(id: string, open: boolean): void {
        document
            .querySelectorAll<HTMLElement>(`[aria-controls="${id}"], [data-action="panel"][data-target="${id}"]`)
            .forEach((el) => {
                el.setAttribute('aria-expanded', open ? 'true' : 'false');
                el.toggleAttribute('data-open', open);
            });
    }
}
