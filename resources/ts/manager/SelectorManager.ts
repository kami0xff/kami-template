/**
 * SelectorManager — searchable combobox for <x-form.selector> / <x-form.country>.
 *
 * Markup: [data-selector] with trigger, panel, options, optional search input.
 */
export class SelectorManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-selector]').forEach((el) => this.bind(el));
        document.addEventListener('click', (e) => {
            const target = e.target as Node;
            document.querySelectorAll<HTMLElement>('[data-selector][data-open="true"]').forEach((open) => {
                if (!open.contains(target)) this.close(open);
            });
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll<HTMLElement>('[data-selector][data-open="true"]').forEach((el) => this.close(el));
            }
        });
    }

    private bind(root: HTMLElement): void {
        if (root.dataset.bound === 'true') return;
        root.dataset.bound = 'true';

        const trigger = root.querySelector<HTMLButtonElement>('[data-selector-trigger]');
        const search = root.querySelector<HTMLInputElement>('[data-selector-search]');
        const options = [...root.querySelectorAll<HTMLElement>('[data-selector-option]')];

        trigger?.addEventListener('click', () => {
            if (root.dataset.disabled === 'true') return;
            const open = root.dataset.open === 'true';
            open ? this.close(root) : this.open(root);
        });

        search?.addEventListener('input', () => this.filter(root, search.value));

        options.forEach((opt) => {
            opt.addEventListener('click', () => this.select(root, opt));
        });
    }

    private open(root: HTMLElement): void {
        root.dataset.open = 'true';
        const panel = root.querySelector<HTMLElement>('[data-selector-panel]');
        const trigger = root.querySelector<HTMLButtonElement>('[data-selector-trigger]');
        panel?.removeAttribute('hidden');
        trigger?.setAttribute('aria-expanded', 'true');
        const search = root.querySelector<HTMLInputElement>('[data-selector-search]');
        queueMicrotask(() => search?.focus());
    }

    private close(root: HTMLElement): void {
        delete root.dataset.open;
        const panel = root.querySelector<HTMLElement>('[data-selector-panel]');
        const trigger = root.querySelector<HTMLButtonElement>('[data-selector-trigger]');
        panel?.setAttribute('hidden', '');
        trigger?.setAttribute('aria-expanded', 'false');
        const search = root.querySelector<HTMLInputElement>('[data-selector-search]');
        if (search) {
            search.value = '';
            this.filter(root, '');
        }
    }

    private filter(root: HTMLElement, query: string): void {
        const q = query.trim().toLowerCase();
        const options = [...root.querySelectorAll<HTMLElement>('[data-selector-option]')];
        let visible = 0;
        options.forEach((opt) => {
            const label = (opt.dataset.label ?? opt.textContent ?? '').toLowerCase();
            const value = (opt.dataset.value ?? '').toLowerCase();
            const match = !q || label.includes(q) || value.includes(q);
            opt.hidden = !match;
            if (match) visible += 1;
        });
        const empty = root.querySelector<HTMLElement>('[data-selector-empty]');
        if (empty) empty.hidden = visible > 0;
    }

    private select(root: HTMLElement, opt: HTMLElement): void {
        const value = opt.dataset.value ?? '';
        const label = opt.dataset.label ?? opt.textContent?.trim() ?? value;
        const hidden = root.querySelector<HTMLInputElement>('[data-selector-value]');
        const labelEl = root.querySelector<HTMLElement>('[data-selector-label]');

        if (hidden) hidden.value = value;
        if (labelEl) {
            labelEl.textContent = label;
            labelEl.classList.remove('is-placeholder');
        }

        root.querySelectorAll<HTMLElement>('[data-selector-option]').forEach((o) => {
            const selected = o === opt;
            o.classList.toggle('is-selected', selected);
            o.setAttribute('aria-selected', selected ? 'true' : 'false');
        });

        root.dispatchEvent(new CustomEvent('selector:change', {
            bubbles: true,
            detail: { value, label },
        }));

        this.close(root);
    }
}
