/**
 * ReactionManager — Twitch-style reaction chips + emote picker.
 *
 * Supports Unicode emoji and custom image/GIF emotes:
 *   data-reaction / data-reaction-label / data-reaction-src / data-reaction-emoji
 *
 * Picker:
 *   [data-reaction-search] filters visible emotes
 *   [data-reaction-tab] switches packs
 *   [data-reaction-pick] toggles a reaction
 *
 * Dispatches bubbling "reactions:change".
 */

interface ReactionOpt {
    key: string;
    label?: string;
    emoji?: string;
    src?: string;
    animated?: boolean;
}

function escAttr(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;');
}

export class ReactionManager {
    private handleDocPointer: (e: PointerEvent) => void;
    private handleKey: (e: KeyboardEvent) => void;

    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-reactions]').forEach((root) => this.bind(root));

        this.handleDocPointer = (e) => {
            const t = e.target as Node;
            document.querySelectorAll<HTMLElement>('.reactions--picker-open').forEach((root) => {
                if (!root.contains(t)) this.closePicker(root);
            });
        };

        this.handleKey = (e) => {
            if (e.key !== 'Escape') return;
            document.querySelectorAll<HTMLElement>('.reactions--picker-open').forEach((root) => {
                this.closePicker(root);
            });
        };

        document.addEventListener('pointerdown', this.handleDocPointer);
        document.addEventListener('keydown', this.handleKey);
    }

    destroy(): void {
        document.removeEventListener('pointerdown', this.handleDocPointer);
        document.removeEventListener('keydown', this.handleKey);
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const tab = t.closest<HTMLElement>('[data-reaction-tab]');
            if (tab && root.contains(tab)) {
                e.preventDefault();
                this.activateTab(root, tab.dataset.reactionTab ?? '');
                return;
            }

            const trigger = t.closest<HTMLElement>('[data-reaction-picker-trigger]');
            if (trigger && root.contains(trigger)) {
                e.preventDefault();
                e.stopPropagation();
                this.togglePicker(root);
                return;
            }

            const pick = t.closest<HTMLElement>('[data-reaction-pick]');
            if (pick && root.contains(pick)) {
                e.preventDefault();
                this.toggleReaction(root, this.readOpt(pick));
                this.closePicker(root);
                return;
            }

            const chip = t.closest<HTMLElement>('[data-reactions-bar] [data-reaction]');
            if (chip && root.contains(chip)) {
                e.preventDefault();
                this.toggleReaction(root, this.readOpt(chip));
            }
        });

        const search = root.querySelector<HTMLInputElement>('[data-reaction-search]');
        search?.addEventListener('input', () => this.filterPicker(root, search.value));
        // Keep typing from closing via outside handlers / bubbling quirks.
        search?.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    private readOpt(el: HTMLElement): ReactionOpt {
        return {
            key: el.dataset.reaction ?? '',
            label: el.dataset.reactionLabel,
            emoji: el.dataset.reactionEmoji,
            src: el.dataset.reactionSrc,
            animated: el.hasAttribute('data-reaction-animated'),
        };
    }

    private togglePicker(root: HTMLElement): void {
        const open = root.classList.contains('reactions--picker-open');
        document.querySelectorAll<HTMLElement>('.reactions--picker-open').forEach((el) => {
            if (el !== root) this.closePicker(el);
        });
        if (open) this.closePicker(root);
        else this.openPicker(root);
    }

    private openPicker(root: HTMLElement): void {
        root.classList.add('reactions--picker-open');
        const picker = root.querySelector<HTMLElement>('[data-reaction-picker]');
        const trigger = root.querySelector<HTMLElement>('[data-reaction-picker-trigger]');
        if (picker) picker.hidden = false;
        trigger?.setAttribute('aria-expanded', 'true');
        this.syncPickerPressed(root);
        queueMicrotask(() => {
            root.querySelector<HTMLInputElement>('[data-reaction-search]')?.focus();
        });
    }

    private closePicker(root: HTMLElement): void {
        root.classList.remove('reactions--picker-open');
        const picker = root.querySelector<HTMLElement>('[data-reaction-picker]');
        const trigger = root.querySelector<HTMLElement>('[data-reaction-picker-trigger]');
        const search = root.querySelector<HTMLInputElement>('[data-reaction-search]');
        if (picker) picker.hidden = true;
        trigger?.setAttribute('aria-expanded', 'false');
        if (search) {
            search.value = '';
            this.filterPicker(root, '');
        }
    }

    private activateTab(root: HTMLElement, id: string): void {
        if (!id) return;
        root.querySelectorAll<HTMLElement>('[data-reaction-tab]').forEach((tab) => {
            const on = tab.dataset.reactionTab === id;
            tab.classList.toggle('is-active', on);
            tab.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        root.querySelectorAll<HTMLElement>('[data-reaction-panel]').forEach((panel) => {
            panel.hidden = panel.dataset.reactionPanel !== id;
        });
        const search = root.querySelector<HTMLInputElement>('[data-reaction-search]');
        this.filterPicker(root, search?.value ?? '');
    }

    private filterPicker(root: HTMLElement, query: string): void {
        const q = query.trim().toLowerCase();
        const panel = root.querySelector<HTMLElement>('[data-reaction-panel]:not([hidden])');
        if (!panel) return;

        let visible = 0;
        panel.querySelectorAll<HTMLElement>('[data-reaction-pick]').forEach((btn) => {
            const label = (btn.dataset.reactionLabel ?? btn.dataset.reaction ?? '').toLowerCase();
            const emoji = (btn.dataset.reactionEmoji ?? '').toLowerCase();
            const match = !q || label.includes(q) || emoji.includes(q) || `:${label}:`.includes(q);
            btn.classList.toggle('is-filtered', !match);
            if (match) visible += 1;
        });

        const empty = panel.querySelector<HTMLElement>('[data-reaction-empty]');
        if (empty) empty.hidden = visible > 0;
    }

    private toggleReaction(root: HTMLElement, opt: ReactionOpt): void {
        if (!opt.key) return;

        const exclusive = root.hasAttribute('data-reactions-exclusive');
        const bar = root.querySelector<HTMLElement>('[data-reactions-bar]');
        if (!bar) return;

        let chip = bar.querySelector<HTMLElement>(`[data-reaction="${CSS.escape(opt.key)}"]`);
        const wasActive = chip?.getAttribute('aria-pressed') === 'true';

        if (exclusive && !wasActive) {
            bar.querySelectorAll<HTMLElement>('[data-reaction][aria-pressed="true"]').forEach((el) => {
                this.setChipActive(el, false);
            });
        }

        if (!chip) {
            chip = this.createChip(opt, 0, false);
            const addBtn = bar.querySelector('[data-reaction-picker-trigger]');
            if (addBtn) bar.insertBefore(chip, addBtn);
            else bar.appendChild(chip);
        }

        this.setChipActive(chip, !wasActive);
        this.syncPickerPressed(root);

        root.dispatchEvent(new CustomEvent('reactions:change', {
            bubbles: true,
            detail: {
                key: opt.key,
                label: opt.label ?? opt.key,
                emoji: opt.emoji ?? null,
                src: opt.src ?? null,
                animated: !!opt.animated,
                active: !wasActive,
                reactions: this.snapshot(root),
            },
        }));
    }

    private setChipActive(chip: HTMLElement, active: boolean): void {
        const countEl = chip.querySelector<HTMLElement>('[data-reaction-count]');
        let count = Number.parseInt(countEl?.textContent ?? '0', 10) || 0;
        const was = chip.getAttribute('aria-pressed') === 'true';

        if (active && !was) count += 1;
        if (!active && was) count -= 1;
        count = Math.max(0, count);

        chip.setAttribute('aria-pressed', active ? 'true' : 'false');
        if (countEl) countEl.textContent = String(count);

        const label = chip.dataset.reactionLabel ?? chip.dataset.reactionEmoji ?? chip.dataset.reaction ?? '';
        chip.setAttribute('aria-label', `${label} ${count}`);

        if (count === 0 && !active) chip.remove();
    }

    private createChip(opt: ReactionOpt, count: number, active: boolean): HTMLElement {
        const label = opt.label ?? opt.key;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'reaction-chip';
        btn.dataset.reaction = opt.key;
        btn.dataset.reactionLabel = label;
        if (opt.emoji) btn.dataset.reactionEmoji = opt.emoji;
        if (opt.src) btn.dataset.reactionSrc = opt.src;
        if (opt.animated || opt.src?.toLowerCase().endsWith('.gif')) {
            btn.setAttribute('data-reaction-animated', '');
        }
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        btn.setAttribute('aria-label', `${label} ${count}`);
        btn.title = `:${label}:`;

        const emote = opt.src
            ? `<img class="reaction-emote-img${opt.animated || opt.src.toLowerCase().endsWith('.gif') ? ' reaction-emote-img--animated' : ''}" src="${escAttr(opt.src)}" alt="" width="20" height="20" loading="lazy" decoding="async" draggable="false">`
            : `<span class="reaction-chip-emoji">${opt.emoji ?? ''}</span>`;

        btn.innerHTML = `<span class="reaction-chip-emote" aria-hidden="true">${emote}</span><span class="reaction-chip-count" data-reaction-count>${count}</span>`;
        return btn;
    }

    private syncPickerPressed(root: HTMLElement): void {
        const active = new Set(
            [...root.querySelectorAll<HTMLElement>('[data-reactions-bar] [data-reaction][aria-pressed="true"]')]
                .map((el) => el.dataset.reaction)
                .filter(Boolean) as string[],
        );
        root.querySelectorAll<HTMLElement>('[data-reaction-pick]').forEach((btn) => {
            const key = btn.dataset.reaction ?? '';
            btn.setAttribute('aria-pressed', active.has(key) ? 'true' : 'false');
        });
    }

    private snapshot(root: HTMLElement): Array<{
        key: string;
        label: string;
        emoji: string | null;
        src: string | null;
        count: number;
        active: boolean;
    }> {
        return [...root.querySelectorAll<HTMLElement>('[data-reactions-bar] [data-reaction]')].map((el) => ({
            key: el.dataset.reaction ?? '',
            label: el.dataset.reactionLabel ?? el.dataset.reaction ?? '',
            emoji: el.dataset.reactionEmoji ?? null,
            src: el.dataset.reactionSrc ?? null,
            count: Number.parseInt(el.querySelector('[data-reaction-count]')?.textContent ?? '0', 10) || 0,
            active: el.getAttribute('aria-pressed') === 'true',
        }));
    }
}
