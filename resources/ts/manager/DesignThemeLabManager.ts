/**
 * DesignThemeLabManager — design-page only brand preview.
 * Overrides CSS variables on :root at runtime (sessionStorage).
 * Does not change the design-system source tokens.
 */

export type ThemeLabColors = {
    primary: string;
    money?: string;
    live?: string;
};

type ThemeLabState = {
    preset: string;
    colors: ThemeLabColors;
    collapsed: boolean;
};

const STORAGE_KEY = 'kami.design.theme-lab';

const DEFAULTS: ThemeLabColors = {
    primary: '#fa5365',
    money: '#70b042',
    live: '#70b042',
};

const PRESETS: Record<string, ThemeLabColors | null> = {
    default: null,
    livestream: { primary: '#e44545', money: '#70b042', live: '#70b042' },
    social: { primary: '#ff0f7b', money: '#e8b923', live: '#70b042' },
    realestate: { primary: '#2a9d8f', money: '#e9c46a', live: '#2a9d8f' },
    ecommerce: { primary: '#4361ee', money: '#f4a261', live: '#2a9d8f' },
    azure: { primary: '#3aa0ff', money: '#e8b923', live: '#3aa0ff' },
    coral: { primary: '#ff6b4a', money: '#e8b923', live: '#70b042' },
};

const OVERRIDE_KEYS = [
    '--main1',
    '--main1-strong',
    '--main1-deep',
    '--main2',
    '--color-primary',
    '--color-accent',
    '--accent-primary',
    '--color-money',
    '--color-live',
    '--color-notification',
    '--text-active-link',
] as const;

function normalizeHex(value: string): string | null {
    const v = value.trim();
    const short = /^#([0-9a-f]{3})$/i.exec(v);
    if (short) {
        const [r, g, b] = short[1].split('');
        return `#${r}${r}${g}${g}${b}${b}`.toLowerCase();
    }
    const full = /^#([0-9a-f]{6})$/i.exec(v);
    return full ? `#${full[1].toLowerCase()}` : null;
}

export class DesignThemeLabManager {
    private root: HTMLElement;
    private state: ThemeLabState;

    constructor(root: HTMLElement) {
        this.root = root;
        this.state = this.load();
        this.bind();
        this.apply(this.state.colors, this.state.preset);
        this.syncUi();
    }

    private load(): ThemeLabState {
        try {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (raw) {
                const parsed = JSON.parse(raw) as Partial<ThemeLabState>;
                return {
                    preset: parsed.preset ?? 'default',
                    colors: {
                        primary: normalizeHex(parsed.colors?.primary ?? '') ?? DEFAULTS.primary,
                        money: normalizeHex(parsed.colors?.money ?? '') ?? DEFAULTS.money,
                        live: normalizeHex(parsed.colors?.live ?? '') ?? DEFAULTS.live,
                    },
                    collapsed: parsed.collapsed ?? true,
                };
            }
        } catch {
            /* ignore */
        }
        return {
            preset: 'default',
            colors: { ...DEFAULTS },
            collapsed: true,
        };
    }

    private save(): void {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(this.state));
    }

    private bind(): void {
        this.root.addEventListener('click', (e) => {
            const t = (e.target as HTMLElement | null)?.closest<HTMLElement>('[data-theme-lab-action]');
            if (!t) return;
            const action = t.dataset.themeLabAction;
            if (action === 'toggle') {
                this.state.collapsed = !this.state.collapsed;
                this.save();
                this.syncUi();
                return;
            }
            if (action === 'reset') {
                this.applyPreset('default');
                return;
            }
            if (action === 'preset') {
                const id = t.dataset.themeLabPreset ?? 'default';
                this.applyPreset(id);
            }
        });

        this.root.querySelectorAll<HTMLInputElement>('[data-theme-lab-color]').forEach((input) => {
            const key = input.dataset.themeLabColor as keyof ThemeLabColors;
            const syncFrom = (value: string) => {
                const hex = normalizeHex(value);
                if (!hex) return;
                this.state.colors[key] = hex;
                this.state.preset = 'custom';
                this.apply(this.state.colors, 'custom');
                this.syncUi();
                this.save();
            };
            input.addEventListener('input', () => syncFrom(input.value));
            input.addEventListener('change', () => syncFrom(input.value));
        });
    }

    private applyPreset(id: string): void {
        const preset = PRESETS[id];
        if (id === 'default' || preset == null) {
            this.state.preset = 'default';
            this.state.colors = { ...DEFAULTS };
            this.clearOverrides();
            document.documentElement.dataset.themeLab = 'default';
            this.syncUi();
            this.save();
            return;
        }
        this.state.preset = id;
        this.state.colors = { ...DEFAULTS, ...preset };
        this.apply(this.state.colors, id);
        this.syncUi();
        this.save();
    }

    private apply(colors: ThemeLabColors, presetId: string): void {
        if (presetId === 'default') {
            this.clearOverrides();
            document.documentElement.dataset.themeLab = 'default';
            return;
        }

        const primary = colors.primary;
        const money = colors.money ?? DEFAULTS.money!;
        const live = colors.live ?? DEFAULTS.live!;
        const root = document.documentElement.style;

        root.setProperty('--main1', primary);
        root.setProperty('--main1-strong', `color-mix(in srgb, ${primary} 72%, #000)`);
        root.setProperty('--main1-deep', `color-mix(in srgb, ${primary} 48%, #000)`);
        root.setProperty('--main2', money);
        root.setProperty('--color-primary', primary);
        root.setProperty('--color-accent', primary);
        root.setProperty('--accent-primary', primary);
        root.setProperty('--color-money', money);
        root.setProperty('--color-live', live);
        root.setProperty('--color-notification', primary);
        root.setProperty('--text-active-link', primary);

        document.documentElement.dataset.themeLab = presetId;
    }

    private clearOverrides(): void {
        const root = document.documentElement.style;
        OVERRIDE_KEYS.forEach((k) => root.removeProperty(k));
    }

    private syncUi(): void {
        const dock = this.root.querySelector<HTMLElement>('[data-theme-lab-dock]');
        if (dock) dock.dataset.collapsed = this.state.collapsed ? 'true' : 'false';

        this.root.querySelectorAll<HTMLElement>('[data-theme-lab-preset]').forEach((btn) => {
            btn.classList.toggle('is-active', btn.dataset.themeLabPreset === this.state.preset);
        });

        const badge = this.root.querySelector('[data-theme-lab-badge]');
        if (badge) {
            badge.textContent = this.state.preset === 'default' ? 'Default' : this.state.preset;
        }

        (['primary', 'money', 'live'] as const).forEach((key) => {
            const hex = this.state.colors[key] ?? DEFAULTS[key]!;
            this.root.querySelectorAll<HTMLInputElement>(`[data-theme-lab-color="${key}"]`).forEach((el) => {
                el.value = hex;
            });
        });
    }
}

export function bootDesignThemeLab(): DesignThemeLabManager | null {
    const root = document.querySelector<HTMLElement>('[data-theme-lab]');
    if (!root) return null;
    return new DesignThemeLabManager(root);
}
