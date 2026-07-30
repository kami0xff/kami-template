import { NotificationApi, type NotificationSettings } from '../api/NotificationApi';

interface NotificationManagerConfig {
    switchInputSelector: string;
    switchIdSelector: string;
    modelSelector: string;
}

const defaultConfig: NotificationManagerConfig = {
    switchInputSelector: '.form-toggle__input',
    switchIdSelector: '[data-switch-id]',
    modelSelector: '[data-model]',
};

export class NotificationManager {
    private config: NotificationManagerConfig;
    private handleChange: (e: Event) => void;

    constructor(overrides: Partial<NotificationManagerConfig> = {}) {
        this.config = { ...defaultConfig, ...overrides };

        this.handleChange = (e: Event) => {
            const input = e.target as HTMLInputElement;
            if (!input.matches(this.config.switchInputSelector)) return;

            const switchContainer = input.closest<HTMLElement>(this.config.switchIdSelector);
            const modelContainer = input.closest<HTMLElement>(this.config.modelSelector);
            if (!switchContainer || !modelContainer) return;

            const switchId = switchContainer.dataset.switchId;
            const modelUsername = modelContainer.dataset.model;
            if (!switchId || !modelUsername) return;

            if (switchId === 'main') {
                this.propagateMainSwitch(modelContainer, input.checked);
            }

            this.syncToServer(modelContainer, modelUsername);
        };

        document.addEventListener('change', this.handleChange);
    }

    private propagateMainSwitch(modelContainer: HTMLElement, isChecked: boolean): void {
        const childSwitches = modelContainer.querySelectorAll<HTMLInputElement>(
            `${this.config.switchIdSelector}:not([data-switch-id="main"]) ${this.config.switchInputSelector}`,
        );

        childSwitches.forEach((el) => {
            el.checked = isChecked;
        });
    }

    private collectSettings(modelContainer: HTMLElement): NotificationSettings {
        const get = (id: string): boolean => {
            const container = modelContainer.querySelector<HTMLElement>(`[data-switch-id="${id}"]`);
            const input = container?.querySelector<HTMLInputElement>(this.config.switchInputSelector);

            return input?.checked ?? false;
        };

        return {
            isEmail: get('email'),
            isPush: get('web'),
            isTelegram: get('telegram'),
        };

    }

    private async syncToServer(modelContainer: HTMLElement, modelUsername: string): Promise<void> {
        const settings = this.collectSettings(modelContainer);

        try {
            await NotificationApi.updateSettings(modelUsername, settings);
        } catch {
            this.revertAll(modelContainer);
        }
    }

    private revertAll(modelContainer: HTMLElement): void {
        modelContainer.querySelectorAll<HTMLInputElement>(
            `${this.config.switchIdSelector} ${this.config.switchInputSelector}`,
        ).forEach((el) => {
            el.checked = !el.checked;
        });
    }

    destroy(): void {
        document.removeEventListener('change', this.handleChange);
    }
}