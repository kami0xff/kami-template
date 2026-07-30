interface TabManagerConfig {
    rootSelector: string;
    triggerSelector: string;
    panelSelector: string;
    activeClass: string;
}

const defaultConfig: TabManagerConfig = {
    rootSelector: '.tab-root',
    triggerSelector: '[data-tab-trigger-id]',
    panelSelector: '[data-tab-panel-id]',
    activeClass: 'tab-active',
};

export class TabManager {
    private config: TabManagerConfig;
    private root: HTMLElement;
    private activeTab: string = '';
    private triggers: Map<string, HTMLElement> = new Map();
    private panels: Map<string, HTMLElement> = new Map();
    private handleClick: (e: Event) => void;

    constructor(root: HTMLElement, overrides: Partial<TabManagerConfig> = {}) {
        this.config = { ...defaultConfig, ...overrides };
        this.root = root;

        this.indexElements();

        const initialTab = this.resolveInitialTab();
        if (initialTab) {
            this.activeTab = initialTab;
            this.syncUI();
        }

        this.handleClick = (e: Event) => {
            const trigger = (e.target as HTMLElement).closest<HTMLElement>(this.config.triggerSelector);
            if (!trigger?.dataset.tabTriggerId) return;
            this.open(trigger.dataset.tabTriggerId);
        };

        this.root.addEventListener('click', this.handleClick);
    }

    open(id: string): void {
        if (id === this.activeTab) return;
        if (!this.triggers.has(id)) return;
        this.activeTab = id;
        this.syncUI();
        this.root.dispatchEvent(new CustomEvent('tab:change', { detail: { id }, bubbles: true }));
    }

    toggle(id: string): void {
        if (this.activeTab === id) return;
        this.open(id);
    }

    isOpen(id: string): boolean {
        return this.activeTab === id;
    }

    getActiveTab(): string {
        return this.activeTab;
    }

    getTrigger(id: string): HTMLElement | undefined {
        return this.triggers.get(id);
    }

    getPanel(id: string): HTMLElement | undefined {
        return this.panels.get(id);
    }

    getTabIds(): string[] {
        return [...this.triggers.keys()];
    }

    destroy(): void {
        this.root.removeEventListener('click', this.handleClick);
        this.triggers.clear();
        this.panels.clear();
    }

    /**
     * Builds the trigger/panel Maps from the DOM.
     * Call again if tabs are added or removed dynamically.
     */
    indexElements(): void {
        this.triggers.clear();
        this.panels.clear();

        this.root.querySelectorAll<HTMLElement>(this.config.triggerSelector).forEach(el => {
            const id = el.dataset.tabTriggerId;
            if (id) this.triggers.set(id, el);
        });

        this.root.querySelectorAll<HTMLElement>(this.config.panelSelector).forEach(el => {
            const id = el.dataset.tabPanelId;
            if (id) this.panels.set(id, el);
        });
    }

    private resolveInitialTab(): string | null {
        for (const [id, el] of this.triggers) {
            if (el.getAttribute('data-active') === 'true' || el.classList.contains(this.config.activeClass)) {
                return id;
            }
        }
        return this.triggers.keys().next().value ?? null;
    }

    private syncUI(): void {
        const { activeClass } = this.config;

        for (const [id, el] of this.triggers) {
            const isActive = id === this.activeTab;
            el.classList.toggle(activeClass, isActive);
            el.setAttribute('aria-selected', String(isActive));
            el.setAttribute('data-active', String(isActive));
        }

        for (const [id, el] of this.panels) {
            const isActive = id === this.activeTab;
            el.classList.toggle(activeClass, isActive);
            el.setAttribute('data-active', String(isActive));
            el.querySelectorAll<HTMLInputElement>('input, select, textarea').forEach(
                input => input.disabled = !isActive
            );
        }

        this.syncHiddenInput();
    }

    /**
     * If the root has data-tab-sync="fieldName", keeps a hidden input
     * in the closest form in sync with the active tab id.
     */
    private syncHiddenInput(): void {
        const fieldName = this.root.dataset.tabSync;
        if (!fieldName) return;

        const form = this.root.closest('form');
        if (!form) return;

        let input = form.querySelector<HTMLInputElement>(`input[name="${fieldName}"]`);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = fieldName;
            form.prepend(input);
        }
        input.value = this.activeTab;
    }
}

//finds all the roots and creates a tab manager for each root
export function initTabs(scope: HTMLElement | Document = document, overrides: Partial<TabManagerConfig> = {}): TabManager[] {
    const selector = overrides.rootSelector ?? defaultConfig.rootSelector;
    return Array.from(scope.querySelectorAll<HTMLElement>(selector)).map(
        root => new TabManager(root, overrides)
    );
}
