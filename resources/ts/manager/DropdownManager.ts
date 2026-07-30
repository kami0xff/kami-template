interface DropdownManagerConfig {
    openClass: string;
}
const defaultConfig: DropdownManagerConfig = {
    openClass: 'sidebar-dropdown-open',
};

export class Dropdown {
    private isOpen: boolean = false;
    private readonly el: HTMLElement;
    private trigger: HTMLElement | null;
    private content: HTMLElement | null;
    private config: DropdownManagerConfig;

    constructor(el: HTMLElement, config: DropdownManagerConfig) {
        this.el = el;
        this.config = config;
        this.trigger = el.querySelector('[data-dropdown-trigger]');
        this.content = el.querySelector('[data-dropdown-content]');
    }

    getElement(): HTMLElement {
        return this.el;
    }

    open(): void {
        if (this.isOpen || !this.trigger || !this.content) return;
        this.isOpen = true;
        this.trigger.setAttribute('aria-expanded', 'true');
        this.content.classList.add(this.config.openClass);
    }

    close(): void {
        if (!this.isOpen || !this.trigger || !this.content) return;
        this.isOpen = false;
        this.trigger.setAttribute('aria-expanded', 'false');
        this.content.classList.remove(this.config.openClass);
    }

    toggle(): void {
        this.isOpen ? this.close() : this.open();
    }

    getIsOpen(): boolean {
        return this.isOpen;
    }
}

export class DropdownManager {
    private config: DropdownManagerConfig;
    private dropdowns: Map<string, Dropdown> = new Map();
    private handleClick: (e: Event) => void;
    private handleKeyDown: (e: KeyboardEvent) => void;

    constructor(private root: HTMLElement, config: DropdownManagerConfig = defaultConfig) {
        this.config = config;

        this.root.querySelectorAll<HTMLElement>('[data-dropdown]').forEach(el => {
            const id = el.dataset.dropdown;
            if (!id) return;
            this.dropdowns.set(id, new Dropdown(el, this.config));
        });

        this.handleClick = (e: Event) => {
            const trigger = (e.target as HTMLElement).closest<HTMLElement>('[data-dropdown-trigger]');
            if (!trigger) return;
            const container = trigger.closest<HTMLElement>('[data-dropdown]');
            if (!container) return;
            const id = container.dataset.dropdown;
            if (!id) return;
            const clicked = this.dropdowns.get(id);
            if (!clicked) return;

            clicked.toggle();
        };

        this.handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape') this.closeAll();
        };

        this.root.addEventListener('click', this.handleClick);
        document.addEventListener('keydown', this.handleKeyDown);
    }

    get(id: string): Dropdown | undefined {
        return this.dropdowns.get(id);
    }

    closeAll(): void {
        this.dropdowns.forEach(dropdown => dropdown.close());
    }

    destroy(): void {
        this.closeAll();
        this.root.removeEventListener('click', this.handleClick);
        document.removeEventListener('keydown', this.handleKeyDown);
        this.dropdowns.clear();
    }
}