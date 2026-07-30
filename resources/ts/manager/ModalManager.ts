interface ModalManagerConfig {
    modalSelector: string;
    triggerSelector: string;
    activeClass: string;
}

const defaultConfig: ModalManagerConfig = {
    modalSelector: '.modal',
    triggerSelector: '[data-action="modal"]',
    activeClass: 'modal-active',
};

// TODO focus trapping (trap tab inside open modal)
// TODO body scroll lock when modal is open
// TODO focus restoration (return focus to trigger on close)

export class ModalManager {
    private config: ModalManagerConfig;
    private openModals: Set<string> = new Set();
    private handlePointerDown: (e: PointerEvent) => void;
    private handleKeyDown: (e: KeyboardEvent) => void;

    constructor(overrides: Partial<ModalManagerConfig> = {}) {
        this.config = { ...defaultConfig, ...overrides };

        this.handlePointerDown = (e: PointerEvent) => {
            const path = e.composedPath().filter((n): n is Element => n instanceof Element);
            // Backdrop is the overlay itself — only ignore clicks inside the dialog card
            // or on an open/close trigger (so toggle doesn't race with dismiss).
            if (path.some((el) => el.matches?.(this.config.modalSelector))) return;
            if (path.some((el) => el.matches?.(this.config.triggerSelector))) return;
            if (this.openModals.size > 0) this.closeAll();
        };

        this.handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape') this.closeAll();
        };

        document.addEventListener('pointerdown', this.handlePointerDown);
        document.addEventListener('keydown', this.handleKeyDown);
    }

    open(id: string): void {
        const modal = document.getElementById(id);
        if (!modal) return;
        this.closeAll();
        modal.classList.add(this.config.activeClass);
        this.openModals.add(id);
    }

    close(id: string): void {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove(this.config.activeClass);
        this.openModals.delete(id);
    }

    toggle(id: string): void {
        this.openModals.has(id) ? this.close(id) : this.open(id);
    }

    closeAll(): void {
        const toClose = [...this.openModals];
        toClose.forEach(id => this.close(id));
    }

    isOpen(id: string): boolean {
        return this.openModals.has(id);
    }

    currentOpenModal(): string | undefined {
        return this.openModals.values().next().value;
    }

    destroy(): void {
        this.closeAll();
        document.removeEventListener('pointerdown', this.handlePointerDown);
        document.removeEventListener('keydown', this.handleKeyDown);
    }
}