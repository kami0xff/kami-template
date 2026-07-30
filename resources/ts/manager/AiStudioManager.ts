/**
 * AiStudioManager — CivitAI-style studio.
 *
 * Events:
 *   ai-studio:change | save | use | generate | export
 */

import { AiHistoryStore, estimateGenCost } from '../ai/history-store';

type ResourceType = 'checkpoint' | 'lora' | 'embedding' | 'vae';

const MAX_LORAS = 5;

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function familyFromText(text: string): string {
    const t = text.toLowerCase();
    if (t.includes('1.5')) return 'SD 1.5';
    if (t.includes('pony')) return 'Pony';
    if (t.includes('flux')) return 'Flux';
    if (t.includes('illustrious')) return 'Illustrious';
    if (t.includes('sdxl') || t.includes('xl')) return 'SDXL';
    return text.trim();
}

function loraRowHtml(id: string, name: string, image: string, family: string, weight = 0.8): string {
    return `<div class="ai-lora-item" data-ai-lora data-lora-id="${esc(id)}" data-lora-name="${esc(name)}" data-lora-family="${esc(family)}">
        <img src="${esc(image)}" alt="">
        <div style="min-width:0;">
            <div class="ai-lora-item-title">${esc(name)}</div>
            <div class="ai-config-chip-sub">${esc(family || 'unknown base')}</div>
            <input type="range" class="ai-range" min="-2" max="2" step="0.05" value="${weight}" data-ai-lora-range aria-label="LoRA weight">
        </div>
        <div class="ai-lora-item-controls">
            <input class="ai-lora-weight" type="number" min="-2" max="2" step="0.05" value="${weight}" data-ai-lora-weight>
            <button type="button" class="ai-lora-remove" data-ai-lora-remove aria-label="Remove LoRA">×</button>
        </div>
    </div>`;
}

function historyItemHtml(item: ReturnType<typeof AiHistoryStore.list>[number]): string {
    const thumb = item.image
        ? `<img src="${esc(item.image)}" alt="">`
        : `<img src="" alt="" style="background:#222">`;
    return `<article class="ai-history-item" data-history-id="${esc(item.id)}">
        ${thumb}
        <div>
            <div class="ai-history-item-title">${esc(item.prompt || '(no prompt)')}</div>
            <div class="ai-history-item-meta">${esc(item.checkpoint)} · ${esc(item.mode)} · ${item.cost} tok</div>
            <span class="ai-history-status ai-history-status--${esc(item.status)}">${esc(item.status)}</span>
        </div>
    </article>`;
}

export class AiStudioManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-ai-studio]').forEach((root) => {
            this.bind(root);
            this.filter(root);
            this.syncLoraEmpty(root);
            this.syncUi(root);
            this.renderHistory(root);
            const selected = root.querySelector<HTMLElement>('[data-ai-resource].is-selected');
            if (selected) this.pickResource(root, selected);
        });

        document.addEventListener('ai-history:changed', () => {
            document.querySelectorAll<HTMLElement>('[data-ai-studio]').forEach((root) => this.renderHistory(root));
        });
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const tab = t.closest<HTMLElement>('[data-ai-studio-tab]');
            if (tab && root.contains(tab)) {
                root.querySelectorAll('.ai-studio-tab').forEach((el) => el.classList.remove('is-active'));
                tab.classList.add('is-active');
                root.dataset.tab = tab.dataset.aiStudioTab ?? 'checkpoint';
                this.filter(root);
                return;
            }

            const baseFilter = t.closest<HTMLElement>('[data-ai-base-filter]');
            if (baseFilter && root.contains(baseFilter)) {
                root.querySelectorAll('[data-ai-base-filter]').forEach((el) => el.classList.remove('is-active'));
                baseFilter.classList.add('is-active');
                root.dataset.baseFilter = baseFilter.dataset.aiBaseFilter ?? '';
                this.filter(root);
                return;
            }

            const modeBtn = t.closest<HTMLElement>('[data-ai-mode]');
            if (modeBtn && root.contains(modeBtn)) {
                root.querySelectorAll('.ai-mode-tab').forEach((el) => el.classList.remove('is-active'));
                modeBtn.classList.add('is-active');
                const mode = modeBtn.dataset.aiMode ?? 'txt2img';
                const modeInput = root.querySelector<HTMLInputElement>('[data-ai-setting="mode"]');
                if (modeInput) modeInput.value = mode;
                this.syncUi(root);
                this.emit(root, 'ai-studio:change');
                return;
            }

            const resource = t.closest<HTMLElement>('[data-ai-resource]');
            if (resource && root.contains(resource)) {
                this.pickResource(root, resource);
                return;
            }

            const remove = t.closest<HTMLElement>('[data-ai-lora-remove]');
            if (remove && root.contains(remove)) {
                remove.closest('[data-ai-lora]')?.remove();
                this.syncLoraEmpty(root);
                this.syncUi(root);
                this.emit(root, 'ai-studio:change');
                return;
            }

            const aspect = t.closest<HTMLElement>('[data-ai-aspect]');
            if (aspect && root.contains(aspect) && aspect.hasAttribute('data-ai-aspect')) {
                const [w, h] = (aspect.dataset.aiAspect ?? '').split('x');
                aspect.closest('[data-ai-aspects]')?.querySelectorAll('.ai-aspect').forEach((el) => el.classList.remove('is-active'));
                aspect.classList.add('is-active');
                const width = this.settingControl(root, 'width') as HTMLInputElement | null;
                const height = this.settingControl(root, 'height') as HTMLInputElement | null;
                if (width && w) width.value = w;
                if (height && h) height.value = h;
                this.syncUi(root);
                this.emit(root, 'ai-studio:change');
                return;
            }

            if (t.closest('[data-ai-studio-generate]') && root.contains(t.closest('[data-ai-studio-generate]')!)) {
                this.generate(root);
                return;
            }
            if (t.closest('[data-ai-studio-save]') && root.contains(t.closest('[data-ai-studio-save]')!)) {
                this.emit(root, 'ai-studio:save');
                return;
            }
            if (t.closest('[data-ai-studio-use]') && root.contains(t.closest('[data-ai-studio-use]')!)) {
                this.emit(root, 'ai-studio:use');
                return;
            }
            if (t.closest('[data-ai-studio-export]') && root.contains(t.closest('[data-ai-studio-export]')!)) {
                const config = this.collect(root);
                void navigator.clipboard?.writeText(JSON.stringify(config, null, 2));
                root.dispatchEvent(new CustomEvent('ai-studio:export', { bubbles: true, detail: { config } }));
                return;
            }
            if (t.closest('[data-ai-history-clear]') && root.contains(t.closest('[data-ai-history-clear]')!)) {
                AiHistoryStore.clear();
                this.renderHistory(root);
                document.dispatchEvent(new CustomEvent('ai-history:changed'));
            }
        });

        root.querySelector<HTMLInputElement>('[data-ai-studio-search]')?.addEventListener('input', () => this.filter(root));

        const drop = root.querySelector<HTMLElement>('[data-ai-source-drop]');
        drop?.addEventListener('click', () => drop.querySelector<HTMLInputElement>('input[type="file"]')?.click());
        drop?.querySelector<HTMLInputElement>('input[type="file"]')?.addEventListener('change', (e) => {
            const file = (e.target as HTMLInputElement).files?.[0];
            const preview = root.querySelector<HTMLImageElement>('[data-ai-source-preview]');
            const label = root.querySelector('[data-ai-source-label]');
            if (file && preview) {
                preview.src = URL.createObjectURL(file);
                preview.hidden = false;
                if (label) label.textContent = file.name;
            }
        });

        root.addEventListener('input', (e) => {
            const t = e.target as HTMLElement;
            if (t.matches('[data-ai-lora-range], [data-ai-lora-weight]')) {
                const item = t.closest<HTMLElement>('[data-ai-lora]');
                if (!item) return;
                const value = (t as HTMLInputElement).value;
                item.querySelectorAll<HTMLInputElement>('[data-ai-lora-range], [data-ai-lora-weight]').forEach((el) => {
                    el.value = value;
                });
            }
            if (t.matches('[data-ai-range]')) {
                const key = (t as HTMLElement).dataset.aiSetting;
                if (key) {
                    const display = root.querySelector(`[data-ai-setting-display="${key}"]`);
                    if (display) display.textContent = (t as HTMLInputElement).value;
                }
            }
            if (t.closest('[data-ai-settings], [data-ai-lora-stack]')) {
                this.syncUi(root);
                this.emit(root, 'ai-studio:change');
            }
        });

        root.addEventListener('change', (e) => {
            const t = e.target as HTMLElement;
            if (t.closest('[data-ai-settings]')) {
                this.syncUi(root);
                this.emit(root, 'ai-studio:change');
            }
        });
    }

    private pickResource(root: HTMLElement, el: HTMLElement): void {
        const type = (el.dataset.resourceType || 'checkpoint') as ResourceType;
        const id = el.dataset.resourceId ?? '';
        const name = el.dataset.resourceName ?? 'Untitled';
        const image = el.dataset.resourceImage ?? '';
        const version = el.dataset.resourceVersion ?? '';
        const base = el.dataset.resourceBase ?? '';
        const family = el.dataset.resourceFamily || familyFromText(`${base} ${version}`);

        if (type === 'checkpoint') {
            root.querySelectorAll('[data-ai-resource][data-resource-type="checkpoint"]').forEach((r) => {
                r.classList.toggle('is-selected', r === el);
            });
            const nameEl = root.querySelector('[data-ai-checkpoint-name]');
            const metaEl = root.querySelector('[data-ai-checkpoint-meta]');
            const imgEl = root.querySelector<HTMLImageElement>('[data-ai-checkpoint-image]');
            if (nameEl) nameEl.textContent = name;
            if (metaEl) metaEl.textContent = [version, base].filter(Boolean).join(' · ');
            if (imgEl) {
                imgEl.src = image;
                imgEl.hidden = !image;
            }
            root.dataset.checkpointId = id;
            root.dataset.checkpointBase = family;
        } else if (type === 'lora') {
            const stack = root.querySelector('[data-ai-lora-stack]');
            if (!stack) return;
            if (stack.querySelector(`[data-lora-id="${CSS.escape(id)}"]`)) return;
            const count = stack.querySelectorAll('[data-ai-lora]').length;
            if (count >= MAX_LORAS) {
                window.toasts?.warning?.(`Max ${MAX_LORAS} LoRAs (CivitAI-style limit)`);
                return;
            }
            if (!this.canAddLora(root, family)) return;
            root.querySelector('[data-ai-lora-empty]')?.remove();
            stack.insertAdjacentHTML('beforeend', loraRowHtml(id, name, image, family));
            this.syncLoraEmpty(root);
        } else if (type === 'vae') {
            const vae = this.settingControl(root, 'vae') as HTMLSelectElement | null;
            if (vae) {
                const opt = Array.from(vae.options).find((o) => o.text.includes(name) || o.value === id);
                if (opt) vae.value = opt.value;
            }
            root.dataset.vaeId = id;
            el.classList.add('is-selected');
        } else if (type === 'embedding') {
            const prompt = this.settingControl(root, 'prompt') as HTMLTextAreaElement | null;
            if (prompt && !prompt.value.includes(name)) {
                prompt.value = `${prompt.value.trim()}${prompt.value.trim() ? ', ' : ''}(${name}:1.0)`;
            }
        }

        this.syncUi(root);
        this.emit(root, 'ai-studio:change');
    }

    private canAddLora(root: HTMLElement, loraFamily: string): boolean {
        const advanced = this.settingValue(root, 'advanced_mode');
        const ckBase = root.dataset.checkpointBase || '';
        if (!ckBase || !loraFamily) return true;
        if (advanced === true || advanced === '1') return true;
        if (familyFromText(ckBase) === familyFromText(loraFamily)) return true;
        window.toasts?.warning?.(
            `LoRA base “${loraFamily}” doesn’t match checkpoint “${ckBase}”. Enable Advanced Mode to force.`,
        );
        return false;
    }

    private filter(root: HTMLElement): void {
        const tab =
            root.dataset.tab ||
            root.querySelector<HTMLElement>('.ai-studio-tab.is-active')?.dataset.aiStudioTab ||
            'checkpoint';
        const q = (root.querySelector<HTMLInputElement>('[data-ai-studio-search]')?.value ?? '').trim().toLowerCase();
        const baseFilter = root.dataset.baseFilter ?? '';

        root.querySelectorAll<HTMLElement>('[data-ai-resource]').forEach((card) => {
            const type = card.dataset.resourceType ?? '';
            const family = card.dataset.resourceFamily ?? '';
            const hay = `${card.dataset.resourceName ?? ''} ${card.dataset.resourceBase ?? ''} ${card.dataset.resourceVersion ?? ''}`.toLowerCase();
            const typeOk = type === tab;
            const searchOk = !q || hay.includes(q);
            const baseOk = !baseFilter || family === baseFilter || (card.dataset.resourceBase ?? '').includes(baseFilter);
            card.hidden = !(typeOk && searchOk && baseOk);
        });
    }

    private syncLoraEmpty(root: HTMLElement): void {
        const stack = root.querySelector('[data-ai-lora-stack]');
        if (!stack) return;
        const has = !!stack.querySelector('[data-ai-lora]');
        const empty = stack.querySelector<HTMLElement>('[data-ai-lora-empty]');
        if (!has && !empty) {
            stack.insertAdjacentHTML(
                'beforeend',
                '<p class="ai-studio-empty" data-ai-lora-empty>Add LoRAs from the library. Adjust weights per item.</p>',
            );
        } else if (has && empty) empty.remove();
    }

    private syncUi(root: HTMLElement): void {
        const mode = String(this.settingValue(root, 'mode') ?? 'txt2img');
        const imgFields = root.querySelector<HTMLElement>('[data-ai-img2img-fields]');
        if (imgFields) imgFields.hidden = !(mode === 'img2img' || mode === 'inpaint');

        const hires = this.settingValue(root, 'hires');
        const refiner = this.settingValue(root, 'refiner');
        const controlnet = this.settingValue(root, 'controlnet');
        const hiresFields = root.querySelector<HTMLElement>('[data-ai-hires-fields]');
        const refinerFields = root.querySelector<HTMLElement>('[data-ai-refiner-fields]');
        const cnFields = root.querySelector<HTMLElement>('[data-ai-controlnet-fields]');
        if (hiresFields) hiresFields.hidden = !(hires === true || hires === '1');
        if (refinerFields) refinerFields.hidden = !(refiner === true || refiner === '1');
        if (cnFields) cnFields.hidden = !(controlnet === true || controlnet === '1');

        this.syncCompat(root);
        this.syncCost(root);
    }

    private syncCompat(root: HTMLElement): void {
        const box = root.querySelector<HTMLElement>('[data-ai-compat]');
        if (!box) return;
        const advanced = this.settingValue(root, 'advanced_mode');
        const ckBase = root.dataset.checkpointBase || '';
        const mismatches: string[] = [];
        root.querySelectorAll<HTMLElement>('[data-ai-lora]').forEach((item) => {
            const fam = item.dataset.loraFamily || '';
            if (ckBase && fam && familyFromText(ckBase) !== familyFromText(fam)) {
                mismatches.push(`${item.dataset.loraName} (${fam})`);
            }
        });

        if (!mismatches.length) {
            box.hidden = true;
            box.textContent = '';
            return;
        }

        box.hidden = false;
        if (advanced === true || advanced === '1') {
            box.classList.remove('ai-compat--error');
            box.textContent = `Advanced Mode: allowing mismatched LoRAs — ${mismatches.join(', ')}`;
        } else {
            box.classList.add('ai-compat--error');
            box.textContent = `Base mismatch: ${mismatches.join(', ')} vs checkpoint ${ckBase}. Remove them or enable Advanced Mode.`;
        }
    }

    private syncCost(root: HTMLElement): void {
        const config = this.collect(root);
        const settings = (config.settings as Record<string, unknown>) ?? {};
        const loras = (config.loras as unknown[])?.length ?? 0;
        const cost = estimateGenCost(settings, loras);
        const el = root.querySelector('[data-ai-cost-value]');
        if (el) el.textContent = String(cost);
        root.dataset.estCost = String(cost);
    }

    private generate(root: HTMLElement): void {
        const config = this.collect(root);
        const settings = (config.settings as Record<string, unknown>) ?? {};
        const checkpoint = String((config.checkpoint as { name?: string })?.name ?? 'Checkpoint');
        const prompt = String(settings.prompt ?? '');
        const mode = String(settings.mode ?? 'txt2img');
        const loras = (config.loras as unknown[])?.length ?? 0;
        const cost = estimateGenCost(settings, loras);

        const advanced = settings.advanced_mode;
        const ckBase = root.dataset.checkpointBase || '';
        let blocked = false;
        if (!(advanced === true || advanced === '1' || advanced === 'true')) {
            root.querySelectorAll<HTMLElement>('[data-ai-lora]').forEach((item) => {
                const fam = item.dataset.loraFamily || '';
                if (ckBase && fam && familyFromText(ckBase) !== familyFromText(fam)) blocked = true;
            });
        }
        if (blocked) {
            window.toasts?.error?.('Fix base mismatches or enable Advanced Mode before generating.');
            return;
        }

        const item = AiHistoryStore.add({
            status: 'queued',
            mode,
            prompt,
            checkpoint,
            cost,
            image: null,
        });
        this.renderHistory(root);
        root.dispatchEvent(new CustomEvent('ai-studio:generate', { bubbles: true, detail: { config, cost, id: item.id } }));
        window.toasts?.info?.(`Queued · ${cost} tokens`);

        AiHistoryStore.update(item.id, { status: 'running' });
        this.renderHistory(root);

        window.setTimeout(() => {
            AiHistoryStore.update(item.id, {
                status: 'done',
                image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=520&fit=crop',
            });
            this.renderHistory(root);
            document.dispatchEvent(new CustomEvent('ai-history:changed'));
            window.toasts?.success?.('Generation complete (demo)');
        }, mode === 'video' ? 2400 : 1400);
    }

    private renderHistory(root: HTMLElement): void {
        const list = root.querySelector('[data-ai-history-list]');
        if (!list) return;
        const items = AiHistoryStore.list();
        list.innerHTML = items.length
            ? items.map(historyItemHtml).join('')
            : '<p class="ai-studio-empty">No generations yet. Hit Generate in the studio.</p>';
    }

    private settingControl(root: HTMLElement, key: string): HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement | null {
        const el = root.querySelector<HTMLElement>(`[data-ai-setting="${key}"]`);
        if (!el) return null;
        if (el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement) return el;
        return el.querySelector('input, select, textarea');
    }

    private settingValue(root: HTMLElement, key: string): string | boolean | number | null {
        const el = this.settingControl(root, key);
        if (!el) return null;
        if (el instanceof HTMLInputElement && el.type === 'checkbox') return el.checked;
        if (el instanceof HTMLInputElement && el.type === 'file') return el.files?.[0]?.name ?? null;
        if (el instanceof HTMLInputElement && (el.type === 'number' || el.type === 'range')) {
            const n = Number(el.value);
            return Number.isFinite(n) ? n : el.value;
        }
        return el.value;
    }

    collect(root: HTMLElement): Record<string, unknown> {
        const settings: Record<string, unknown> = {};
        root.querySelectorAll<HTMLElement>('[data-ai-setting]').forEach((el) => {
            const key = el.dataset.aiSetting;
            if (!key || settings[key] !== undefined) return;
            settings[key] = this.settingValue(root, key);
        });

        const loras = Array.from(root.querySelectorAll<HTMLElement>('[data-ai-lora]')).map((item) => ({
            id: item.dataset.loraId,
            name: item.dataset.loraName,
            family: item.dataset.loraFamily,
            weight: Number(item.querySelector<HTMLInputElement>('[data-ai-lora-weight]')?.value ?? 0.8),
        }));

        return {
            checkpoint: {
                id: root.dataset.checkpointId ?? null,
                name: root.querySelector('[data-ai-checkpoint-name]')?.textContent ?? null,
                meta: root.querySelector('[data-ai-checkpoint-meta]')?.textContent ?? null,
                base: root.dataset.checkpointBase ?? null,
            },
            vaeId: root.dataset.vaeId ?? settings.vae ?? null,
            loras,
            settings,
            cost: Number(root.dataset.estCost ?? estimateGenCost(settings, loras.length)),
        };
    }

    private emit(root: HTMLElement, name: 'ai-studio:change' | 'ai-studio:save' | 'ai-studio:use'): void {
        root.dispatchEvent(new CustomEvent(name, { bubbles: true, detail: { config: this.collect(root) } }));
    }
}
