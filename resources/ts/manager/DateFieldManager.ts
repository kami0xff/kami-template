/**
 * DateFieldManager — popover calendar for <x-form.date> / <x-form.date-range>.
 * Relies on CalendarManager emitting `calendar:change` from nested [data-calendar].
 *
 * Outside-click uses composedPath(): CalendarManager re-renders on day click and
 * detaches the event target, so Node.contains(target) falsely looks "outside"
 * and was closing the range popover after the first date.
 */
export class DateFieldManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-date-field]').forEach((el) => this.bind(el));

        document.addEventListener('click', (e) => {
            const path = e.composedPath();
            document.querySelectorAll<HTMLElement>('[data-date-field][data-open="true"]').forEach((open) => {
                if (!path.includes(open)) this.close(open);
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll<HTMLElement>('[data-date-field][data-open="true"]').forEach((el) => this.close(el));
            }
        });
    }

    private bind(root: HTMLElement): void {
        if (root.dataset.bound === 'true') return;
        root.dataset.bound = 'true';

        const trigger = root.querySelector<HTMLButtonElement>('[data-date-trigger]');
        const clear = root.querySelector<HTMLButtonElement>('[data-date-clear]');

        trigger?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (root.dataset.disabled === 'true') return;
            root.dataset.open === 'true' ? this.close(root) : this.open(root);
        });

        clear?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.clear(root);
        });

        root.addEventListener('calendar:change', ((e: CustomEvent<{ start: string; end: string }>) => {
            this.apply(root, e.detail.start, e.detail.end);
        }) as EventListener);
    }

    private open(root: HTMLElement): void {
        root.dataset.open = 'true';
        const pop = root.querySelector<HTMLElement>('[data-date-popover]');
        const trigger = root.querySelector<HTMLButtonElement>('[data-date-trigger]');
        pop?.removeAttribute('hidden');
        trigger?.setAttribute('aria-expanded', 'true');
    }

    private close(root: HTMLElement): void {
        delete root.dataset.open;
        const pop = root.querySelector<HTMLElement>('[data-date-popover]');
        const trigger = root.querySelector<HTMLButtonElement>('[data-date-trigger]');
        pop?.setAttribute('hidden', '');
        trigger?.setAttribute('aria-expanded', 'false');
    }

    private apply(root: HTMLElement, start: string, end: string): void {
        const mode = root.dataset.mode === 'range' ? 'range' : 'single';
        const label = root.querySelector<HTMLElement>('[data-date-label]');
        const valueEl = root.querySelector<HTMLInputElement>('[data-date-value]');
        const endEl = root.querySelector<HTMLInputElement>('[data-date-value-end]');
        const clear = root.querySelector<HTMLButtonElement>('[data-date-clear]');

        if (valueEl) valueEl.value = start;
        if (endEl) endEl.value = end;

        if (label) {
            if (mode === 'range') {
                if (start && end) {
                    label.textContent = `${start} → ${end}`;
                    label.classList.remove('is-placeholder');
                } else if (start) {
                    label.textContent = `${start} → …`;
                    label.classList.remove('is-placeholder');
                }
            } else if (start) {
                label.textContent = start;
                label.classList.remove('is-placeholder');
            }
        }

        if (clear) clear.hidden = !start;

        // Keep range open until both ends are chosen; close single immediately.
        if (mode === 'single' && start) this.close(root);
        if (mode === 'range' && start && end) this.close(root);
    }

    private clear(root: HTMLElement): void {
        const label = root.querySelector<HTMLElement>('[data-date-label]');
        const valueEl = root.querySelector<HTMLInputElement>('[data-date-value]');
        const endEl = root.querySelector<HTMLInputElement>('[data-date-value-end]');
        const clear = root.querySelector<HTMLButtonElement>('[data-date-clear]');
        const placeholder = root.dataset.placeholder
            || (root.classList.contains('form-date-field--range') ? 'Select date range' : 'Select date');

        if (valueEl) valueEl.value = '';
        if (endEl) endEl.value = '';
        if (label) {
            label.textContent = placeholder;
            label.classList.add('is-placeholder');
        }
        if (clear) clear.hidden = true;

        root.dispatchEvent(new CustomEvent('datefield:clear', { bubbles: true }));
    }
}
