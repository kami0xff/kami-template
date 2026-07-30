/**
 * AiConfigManager — library UI for saved AI configs.
 *
 * Mount: [data-ai-configs]
 * Create form: [data-ai-config-form]
 */

import { AiConfigStore, type AiSavedConfig } from '../ai/config-store';

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function cardHtml(config: AiSavedConfig): string {
    const avatar = config.avatar
        ? `<img src="${esc(config.avatar)}" alt="">`
        : `<div class="ai-config-card-avatar">${esc(config.name.slice(0, 2).toUpperCase())}</div>`;

    const loras =
        config.loras.length > 0
            ? config.loras.map((l) => `<span class="ai-dock-chip">${esc(l.name)} · ${l.weight}</span>`).join('')
            : `<span class="ai-dock-chip">No LoRAs</span>`;

    return `<article class="ai-config-card" data-ai-config-card data-config-id="${esc(config.id)}">
        <div class="ai-config-card-top">
            ${avatar}
            <div>
                <h3 class="ai-config-card-title">${esc(config.name)}</h3>
                <p class="ai-config-card-sub">${esc(config.subtitle || config.checkpoint.name)}</p>
            </div>
        </div>
        <div class="ai-config-card-meta">
            <span class="ai-dock-chip"><strong>${esc(config.checkpoint.name)}</strong></span>
            ${loras}
        </div>
        <div class="ai-config-card-actions">
            <button type="button" class="btn btn-primary btn-sm" data-ai-config-open>Open in dock</button>
            <button type="button" class="btn btn-muted btn-sm" data-ai-config-delete>Delete</button>
        </div>
    </article>`;
}

export class AiConfigManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-ai-configs]').forEach((root) => {
            this.bind(root);
            this.render(root);
        });

        document.addEventListener('ai-config:saved', () => {
            document.querySelectorAll<HTMLElement>('[data-ai-configs]').forEach((root) => this.render(root));
        });
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;
            const card = t.closest<HTMLElement>('[data-ai-config-card]');
            const id = card?.dataset.configId;

            if (t.closest('[data-ai-config-open]') && id) {
                window.chats?.openAi(id);
                return;
            }

            if (t.closest('[data-ai-config-delete]') && id) {
                if (!confirm('Delete this saved config?')) return;
                AiConfigStore.remove(id);
                this.render(root);
                window.toasts?.success?.('Config deleted');
                return;
            }

            if (t.closest('[data-ai-config-new-toggle]')) {
                const form = root.querySelector<HTMLElement>('[data-ai-config-form]');
                if (form) form.hidden = !form.hidden;
            }
        });

        root.querySelector<HTMLFormElement>('[data-ai-config-form]')?.addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.target as HTMLFormElement;
            const data = new FormData(form);
            const name = String(data.get('name') || '').trim();
            if (!name) return;

            const saved = AiConfigStore.save({
                name,
                avatar: String(data.get('avatar') || '') || null,
                subtitle: String(data.get('subtitle') || '') || null,
                checkpoint: {
                    id: String(data.get('checkpoint_id') || '') || null,
                    name: String(data.get('checkpoint_name') || 'Custom checkpoint'),
                    meta: String(data.get('checkpoint_meta') || '') || null,
                },
                loras: [],
                settings: {
                    prompt: String(data.get('prompt') || ''),
                    steps: 28,
                    cfg: 5.5,
                },
            });

            form.reset();
            form.hidden = true;
            this.render(root);
            document.dispatchEvent(new CustomEvent('ai-config:saved', { detail: { config: saved } }));
            window.toasts?.success?.(`Created “${saved.name}”`);
            window.chats?.openAi(saved);
        });
    }

    private render(root: HTMLElement): void {
        const grid = root.querySelector('[data-ai-configs-grid]');
        if (!grid) return;
        const list = AiConfigStore.list();
        grid.innerHTML = list.length
            ? list.map(cardHtml).join('')
            : `<p class="ai-studio-empty">No saved configs yet. Create one or save from the studio.</p>`;
    }
}
