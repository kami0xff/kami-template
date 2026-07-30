import { DropdownManager } from './DropdownManager';
import { PanelManager } from './PanelManager';
import { ModalManager } from './ModalManager';

interface SidebarConfig {
    sidebarSelector: string;
}

const defaultConfig: SidebarConfig = {
    sidebarSelector: '.sidebar',
};

// TODO manage subnavbars
// TODO manage pinable and draggable panels section

export class SidebarManager {
    private config: SidebarConfig;
    private dropdownManager: DropdownManager;
    private panelManager: PanelManager;
    private modalManager: ModalManager;
    private root: HTMLElement;
    private handleAction: (e: Event) => void;

    constructor(
        panelManager: PanelManager,
        modalManager: ModalManager,
        config: Partial<SidebarConfig> = {},
    ) {
        this.config = { ...defaultConfig, ...config };
        this.panelManager = panelManager;
        this.modalManager = modalManager;
        const el = document.querySelector<HTMLElement>(this.config.sidebarSelector);
        if (!el) throw new Error('Sidebar not found');
        this.root = el;
        this.dropdownManager = new DropdownManager(this.root);

        this.handleAction = (e: Event) => {
            const actionEl = (e.target as HTMLElement).closest<HTMLElement>('[data-action]');
            if (!actionEl) return;
            const action = actionEl.dataset.action ?? '';
            const target = actionEl.dataset.target ?? '';
            if (!action) return;

            switch (action) {
                case 'panel':
                    this.panelManager.toggle(target);
                    break;
                case 'modal':
                    this.modalManager.toggle(target);
                    break;
                case 'sub-sidebar':
                    // Handled globally by SubSidebarManager (panels live
                    // outside the sidebar element, as siblings).
                    break;
                default:
                    console.warn(`[SidebarManager] Unknown action: "${action}" target: "${target}"`);
            }
        };

        this.root.addEventListener('click', this.handleAction);
    }

    getDropdowns(): DropdownManager {
        return this.dropdownManager;
    }

    getPanels(): PanelManager {
        return this.panelManager;
    }

    getModals(): ModalManager {
        return this.modalManager;
    }

    destroy(): void {
        this.root.removeEventListener('click', this.handleAction);
        this.dropdownManager.destroy();
    }
}