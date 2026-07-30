/**
 * Saved AI model configs (presets / characters / girlfriends).
 * Demo persistence via localStorage — swap for your API later.
 */

export interface AiConfigCheckpoint {
    id: string | null;
    name: string;
    meta?: string | null;
    image?: string | null;
}

export interface AiConfigLora {
    id: string;
    name: string;
    weight: number;
    image?: string | null;
}

export interface AiSavedConfig {
    id: string;
    name: string;
    avatar?: string | null;
    subtitle?: string | null;
    checkpoint: AiConfigCheckpoint;
    loras: AiConfigLora[];
    settings: Record<string, unknown>;
    createdAt: string;
    updatedAt: string;
}

const STORAGE_KEY = 'kami.ai.configs.v1';

const SEED: AiSavedConfig[] = [
    {
        id: 'cfg-luna',
        name: 'Luna',
        avatar: 'https://i.pravatar.cc/128?img=5',
        subtitle: 'AI girlfriend · RealVisXL',
        checkpoint: {
            id: 'ckpt-realvis',
            name: 'RealVisXL V5.0',
            meta: 'v5.0 · SDXL 1.0',
            image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=160&h=200&fit=crop',
        },
        loras: [
            { id: 'lora-detail', name: 'Add More Details', weight: 0.8 },
            { id: 'lora-cine', name: 'Cinematic Lighting', weight: 0.55 },
        ],
        settings: {
            sampler: 'euler_a',
            scheduler: 'normal',
            steps: 28,
            cfg: 5.5,
            width: 832,
            height: 1216,
            prompt: 'soft rim light, warm skin, intimate portrait',
            negative_prompt: 'blurry, lowres, watermark',
        },
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
    },
    {
        id: 'cfg-nova',
        name: 'Nova',
        avatar: 'https://i.pravatar.cc/128?img=9',
        subtitle: 'Style pack · Juggernaut',
        checkpoint: {
            id: 'ckpt-jugger',
            name: 'Juggernaut XL',
            meta: 'XI · SDXL 1.0',
            image: 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=160&h=200&fit=crop',
        },
        loras: [{ id: 'lora-film', name: 'Analog Film Grain', weight: 0.7 }],
        settings: {
            sampler: 'dpmpp_2m',
            steps: 32,
            cfg: 6,
            width: 1024,
            height: 1024,
        },
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString(),
    },
];

function readAll(): AiSavedConfig[] {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
            writeAll(SEED);
            return [...SEED];
        }
        const parsed = JSON.parse(raw) as AiSavedConfig[];
        return Array.isArray(parsed) ? parsed : [...SEED];
    } catch {
        return [...SEED];
    }
}

function writeAll(configs: AiSavedConfig[]): void {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(configs));
    } catch {
        /* ignore quota */
    }
}

function uid(prefix = 'cfg'): string {
    return `${prefix}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 7)}`;
}

export const AiConfigStore = {
    list(): AiSavedConfig[] {
        return readAll().sort((a, b) => b.updatedAt.localeCompare(a.updatedAt));
    },

    get(id: string): AiSavedConfig | null {
        return readAll().find((c) => c.id === id) ?? null;
    },

    save(input: Partial<AiSavedConfig> & { name: string; checkpoint: AiConfigCheckpoint }): AiSavedConfig {
        const all = readAll();
        const now = new Date().toISOString();
        const existing = input.id ? all.find((c) => c.id === input.id) : null;

        const next: AiSavedConfig = {
            id: existing?.id ?? input.id ?? uid(),
            name: input.name.trim() || 'Untitled config',
            avatar: input.avatar ?? existing?.avatar ?? null,
            subtitle: input.subtitle ?? existing?.subtitle ?? null,
            checkpoint: input.checkpoint,
            loras: input.loras ?? existing?.loras ?? [],
            settings: input.settings ?? existing?.settings ?? {},
            createdAt: existing?.createdAt ?? now,
            updatedAt: now,
        };

        const idx = all.findIndex((c) => c.id === next.id);
        if (idx >= 0) all[idx] = next;
        else all.unshift(next);
        writeAll(all);
        return next;
    },

    remove(id: string): boolean {
        const all = readAll();
        const next = all.filter((c) => c.id !== id);
        if (next.length === all.length) return false;
        writeAll(next);
        return true;
    },

    fromStudioPayload(payload: {
        name?: string;
        avatar?: string | null;
        subtitle?: string | null;
        config: {
            checkpoint?: { id?: string | null; name?: string | null; meta?: string | null };
            loras?: { id?: string | null; name?: string | null; weight?: number }[];
            settings?: Record<string, unknown>;
        };
        id?: string;
    }): AiSavedConfig {
        const ck = payload.config.checkpoint ?? {};
        return this.save({
            id: payload.id,
            name: payload.name || (ck.name as string) || 'Studio config',
            avatar: payload.avatar ?? null,
            subtitle: payload.subtitle ?? (ck.meta as string) ?? null,
            checkpoint: {
                id: ck.id ?? null,
                name: (ck.name as string) || 'Checkpoint',
                meta: (ck.meta as string) ?? null,
            },
            loras: (payload.config.loras ?? []).map((l) => ({
                id: String(l.id ?? uid('lora')),
                name: String(l.name ?? 'LoRA'),
                weight: Number(l.weight ?? 0.8),
            })),
            settings: payload.config.settings ?? {},
        });
    },
};
