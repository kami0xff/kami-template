/**
 * CalendarManager — month-grid date picker with single or range selection.
 *
 * Markup contract (see components/form/calendar.blade.php):
 *   <div class="calendar" data-calendar data-mode="single|range" data-name="date">
 *       (hidden inputs are created/updated by the manager)
 *   </div>
 *
 * Emits a bubbling 'calendar:change' CustomEvent with { start, end } in
 * YYYY-MM-DD, and mirrors values into hidden inputs `name` / `name_end`.
 */
const WEEKDAYS = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

function iso(date: Date): string {
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${date.getFullYear()}-${m}-${d}`;
}

class Calendar {
    private el: HTMLElement;
    private mode: 'single' | 'range';
    private view: Date;
    private start: Date | null = null;
    private end: Date | null = null;
    private inputStart: HTMLInputElement;
    private inputEnd: HTMLInputElement;

    constructor(el: HTMLElement) {
        this.el = el;
        this.mode = el.dataset.mode === 'range' ? 'range' : 'single';
        this.view = new Date();
        this.view.setDate(1);

        const name = el.dataset.name ?? 'date';
        this.inputStart = this.hiddenInput(name);
        this.inputEnd = this.hiddenInput(`${name}_end`);

        this.render();
    }

    private hiddenInput(name: string): HTMLInputElement {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        this.el.appendChild(input);
        return input;
    }

    private select(day: Date): void {
        if (this.mode === 'single') {
            this.start = day;
            this.end = null;
        } else if (!this.start || (this.start && this.end)) {
            this.start = day;
            this.end = null;
        } else if (day < this.start) {
            this.end = this.start;
            this.start = day;
        } else {
            this.end = day;
        }

        this.inputStart.value = this.start ? iso(this.start) : '';
        this.inputEnd.value = this.end ? iso(this.end) : '';
        this.el.dispatchEvent(new CustomEvent('calendar:change', {
            bubbles: true,
            detail: { start: this.inputStart.value, end: this.inputEnd.value },
        }));
        this.render();
    }

    private dayClasses(day: Date, inMonth: boolean): string {
        const classes = ['calendar-day'];
        if (!inMonth) classes.push('calendar-day--muted');
        if (iso(day) === iso(new Date())) classes.push('calendar-day--today');

        const time = day.getTime();
        const isStart = this.start && iso(day) === iso(this.start);
        const isEnd = this.end && iso(day) === iso(this.end);
        if (isStart || isEnd) classes.push('calendar-day--selected');
        if (isStart && this.end) classes.push('calendar-day--range-start');
        if (isEnd) classes.push('calendar-day--range-end');
        if (this.start && this.end && time > this.start.getTime() && time < this.end.getTime()) {
            classes.push('calendar-day--in-range');
        }
        return classes.join(' ');
    }

    private render(): void {
        this.el.querySelector('.calendar-inner')?.remove();

        const inner = document.createElement('div');
        inner.className = 'calendar-inner';

        const title = this.view.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
        const head = document.createElement('div');
        head.className = 'calendar-head';
        head.innerHTML = `
            <button type="button" class="calendar-nav" data-cal-prev aria-label="Previous month">&lsaquo;</button>
            <span class="calendar-title">${title}</span>
            <button type="button" class="calendar-nav" data-cal-next aria-label="Next month">&rsaquo;</button>`;
        head.querySelector('[data-cal-prev]')?.addEventListener('click', () => {
            this.view.setMonth(this.view.getMonth() - 1);
            this.render();
        });
        head.querySelector('[data-cal-next]')?.addEventListener('click', () => {
            this.view.setMonth(this.view.getMonth() + 1);
            this.render();
        });
        inner.appendChild(head);

        const grid = document.createElement('div');
        grid.className = 'calendar-grid';
        WEEKDAYS.forEach((wd) => {
            const cell = document.createElement('span');
            cell.className = 'calendar-weekday';
            cell.textContent = wd;
            grid.appendChild(cell);
        });

        // Monday-first offset of the 1st of the viewed month
        const firstOffset = (this.view.getDay() + 6) % 7;
        const cursor = new Date(this.view);
        cursor.setDate(1 - firstOffset);

        for (let i = 0; i < 42; i++) {
            const day = new Date(cursor);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = this.dayClasses(day, day.getMonth() === this.view.getMonth());
            btn.textContent = String(day.getDate());
            btn.addEventListener('click', () => this.select(day));
            grid.appendChild(btn);
            cursor.setDate(cursor.getDate() + 1);
        }

        inner.appendChild(grid);
        this.el.appendChild(inner);
    }
}

export class CalendarManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-calendar]').forEach((el) => new Calendar(el));
    }
}
