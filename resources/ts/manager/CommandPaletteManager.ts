/**
 * CommandPaletteManager — ⌘K / Ctrl+K action search.
 *
 * Works against a palette rendered by <x-command-palette>:
 *   <div class="command-overlay" data-command-palette> with .command-input,
 *   .command-list > .command-item[data-keywords], .command-empty
 *
 * Filters items by text + data-keywords, arrow keys move the active item,
 * Enter activates it (click/navigate), Escape closes.
 */
export class CommandPaletteManager {
    private overlay: HTMLElement | null;
    private input: HTMLInputElement | null = null;
    private items: HTMLElement[] = [];
    private activeIndex = 0;

    constructor() {
        this.overlay = document.querySelector<HTMLElement>('[data-command-palette]');
        if (!this.overlay) return;

        this.input = this.overlay.querySelector<HTMLInputElement>('.command-input');
        this.items = Array.from(this.overlay.querySelectorAll<HTMLElement>('.command-item'));

        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                this.toggle();
            }
            if (!this.isOpen()) return;
            if (e.key === 'Escape') this.close();
            if (e.key === 'ArrowDown') { e.preventDefault(); this.move(1); }
            if (e.key === 'ArrowUp') { e.preventDefault(); this.move(-1); }
            if (e.key === 'Enter') {
                e.preventDefault();
                this.visibleItems()[this.activeIndex]?.click();
                this.close();
            }
        });

        this.overlay.addEventListener('pointerdown', (e) => {
            if (e.target === this.overlay) this.close();
        });

        this.input?.addEventListener('input', () => this.filter());
        this.items.forEach((item) => item.addEventListener('click', () => this.close()));
    }

    isOpen(): boolean {
        return this.overlay?.classList.contains('command-open') ?? false;
    }

    open(): void {
        if (!this.overlay) return;
        this.overlay.classList.add('command-open');
        if (this.input) this.input.value = '';
        this.filter();
        this.input?.focus();
    }

    close(): void {
        this.overlay?.classList.remove('command-open');
    }

    toggle(): void {
        this.isOpen() ? this.close() : this.open();
    }

    private visibleItems(): HTMLElement[] {
        return this.items.filter((item) => !item.hidden);
    }

    private filter(): void {
        const query = (this.input?.value ?? '').trim().toLowerCase();
        this.items.forEach((item) => {
            const haystack = `${item.textContent} ${item.dataset.keywords ?? ''}`.toLowerCase();
            item.hidden = query !== '' && !haystack.includes(query);
        });
        this.activeIndex = 0;
        this.paint();
        this.overlay
            ?.querySelector<HTMLElement>('.command-empty')
            ?.toggleAttribute('hidden', this.visibleItems().length > 0);
    }

    private move(dir: number): void {
        const visible = this.visibleItems();
        if (!visible.length) return;
        this.activeIndex = (this.activeIndex + dir + visible.length) % visible.length;
        this.paint();
        visible[this.activeIndex]?.scrollIntoView({ block: 'nearest' });
    }

    private paint(): void {
        const visible = this.visibleItems();
        this.items.forEach((item) => item.classList.remove('command-item--active'));
        visible[this.activeIndex]?.classList.add('command-item--active');
    }
}
