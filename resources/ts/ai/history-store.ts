/**
 * Generation history / queue (demo localStorage).
 */

export type GenStatus = 'queued' | 'running' | 'done' | 'failed';

export interface GenHistoryItem {
    id: string;
    status: GenStatus;
    mode: string;
    prompt: string;
    checkpoint: string;
    cost: number;
    image?: string | null;
    createdAt: string;
    error?: string | null;
}

const KEY = 'kami.ai.history.v1';

function read(): GenHistoryItem[] {
    try {
        const raw = localStorage.getItem(KEY);
        if (!raw) return [];
        const parsed = JSON.parse(raw) as GenHistoryItem[];
        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

function write(items: GenHistoryItem[]): void {
    try {
        localStorage.setItem(KEY, JSON.stringify(items.slice(0, 40)));
    } catch {
        /* ignore */
    }
}

export const AiHistoryStore = {
    list(): GenHistoryItem[] {
        return read().sort((a, b) => b.createdAt.localeCompare(a.createdAt));
    },

    add(partial: Omit<GenHistoryItem, 'id' | 'createdAt'> & { id?: string }): GenHistoryItem {
        const item: GenHistoryItem = {
            ...partial,
            id: partial.id ?? `gen-${Date.now().toString(36)}`,
            createdAt: new Date().toISOString(),
        };
        const all = read();
        all.unshift(item);
        write(all);
        return item;
    },

    update(id: string, patch: Partial<GenHistoryItem>): GenHistoryItem | null {
        const all = read();
        const idx = all.findIndex((x) => x.id === id);
        if (idx < 0) return null;
        all[idx] = { ...all[idx], ...patch };
        write(all);
        return all[idx];
    },

    clear(): void {
        write([]);
    },
};

/** Rough token cost for the demo wallet integration. */
export function estimateGenCost(settings: Record<string, unknown>, loraCount: number): number {
    const steps = Number(settings.steps ?? 28);
    const batch = Number(settings.batch_size ?? 1);
    const mode = String(settings.mode ?? 'txt2img');
    let cost = 5;
    cost += Math.round(steps / 10);
    cost += loraCount * 2;
    if (settings.hires) cost += 5;
    if (settings.controlnet) cost += 4;
    if (mode === 'img2img' || mode === 'inpaint') cost += 3;
    if (mode === 'video') cost += 20;
    return Math.max(1, cost * Math.max(1, batch));
}
