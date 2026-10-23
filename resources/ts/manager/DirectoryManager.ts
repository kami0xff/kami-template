/**
 * DirectoryManager — search box + A–Z bar for <x-directory>.
 *
 * Markup (see components/directory):
 *   [data-directory]
 *     [data-directory-filter]                     search input
 *     [data-directory-letters] [data-directory-letter="A"]   ("" = Main)
 *     [data-directory-section] [data-directory-group] [data-directory-item]
 *     [data-directory-empty]
 *
 * An item's letter is the first character of its link text (# when it isn't
 * A–Z). Letters with no items are disabled; the bar stays hidden without JS.
 */
export class DirectoryManager {
    constructor(root: ParentNode = document) {
        root.querySelectorAll<HTMLElement>('[data-directory]').forEach((el) => new Directory(el));
    }
}

interface Entry {
    el: HTMLElement;
    text: string;
    letter: string;
}

class Directory {
    private letter = '';
    private readonly input: HTMLInputElement | null;
    private readonly entries: Entry[];

    constructor(private readonly root: HTMLElement) {
        this.input = root.querySelector<HTMLInputElement>('[data-directory-filter]');
        this.entries = Array.from(root.querySelectorAll<HTMLElement>('[data-directory-item]'), (el) => {
            const text = (el.querySelector('a')?.textContent ?? el.textContent ?? '').trim().toLowerCase();
            const first = text.charAt(0).toUpperCase();
            return { el, text, letter: /^[A-Z]$/.test(first) ? first : '#' };
        });

        this.input?.addEventListener('input', () => this.apply());
        this.bindLetters();
    }

    private bindLetters(): void {
        const bar = this.root.querySelector<HTMLElement>('[data-directory-letters]');
        if (!bar) return;

        const used = new Set(this.entries.map((e) => e.letter));
        const buttons = Array.from(bar.querySelectorAll<HTMLButtonElement>('[data-directory-letter]'));
        buttons.forEach((b) => {
            const letter = b.dataset.directoryLetter ?? '';
            if (letter && !used.has(letter)) b.disabled = true;
        });
        bar.hidden = false;

        bar.addEventListener('click', (e) => {
            const button = (e.target as Element).closest<HTMLButtonElement>('[data-directory-letter]');
            if (!button || button.disabled) return;
            this.letter = button.dataset.directoryLetter ?? '';
            buttons.forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
            this.apply();
        });
    }

    private apply(): void {
        const q = (this.input?.value ?? '').trim().replace(/^#/, '').replace(/_/g, ' ').toLowerCase();
        for (const entry of this.entries) {
            entry.el.hidden = !((!this.letter || entry.letter === this.letter) && (!q || entry.text.includes(q)));
        }

        let total = 0;
        this.root.querySelectorAll<HTMLElement>('[data-directory-group]').forEach((group) => {
            const shown = group.querySelectorAll('[data-directory-item]:not([hidden])').length;
            group.hidden = shown === 0;
            total += shown;
        });
        this.root.querySelectorAll<HTMLElement>('[data-directory-section]').forEach((section) => {
            section.hidden = !section.querySelector('[data-directory-group]:not([hidden])');
        });
        const empty = this.root.querySelector<HTMLElement>('[data-directory-empty]');
        if (empty) empty.hidden = total > 0;
    }
}
