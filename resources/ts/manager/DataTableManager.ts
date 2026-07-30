/**
 * DataTableManager — search, keyboard nav, and multi-row selection.
 * Attaches to every [data-datatable] root on init.
 */

interface DtInstance {
    root: HTMLElement;
    searchInput: HTMLInputElement | null;
    rows: HTMLTableRowElement[];
    focusedIndex: number;
    selectable: boolean;
    selectAll: HTMLInputElement | null;
    bulkBar: HTMLElement | null;
    bulkCount: HTMLElement | null;
    selected: Set<string>;
}

type BulkDetail = {
    tableId: string;
    action: string;
    ids: string[];
};

export class DataTableManager {
    private tables: Map<string, DtInstance> = new Map();

    constructor() {
        this.init();
    }

    private init(): void {
        document.querySelectorAll<HTMLElement>('[data-datatable]').forEach((root) => {
            this.mount(root);
        });
    }

    /** Mount (or re-mount) a single table root. */
    mount(root: HTMLElement): void {
        const id = root.id || `dt-${Math.random().toString(36).slice(2, 8)}`;
        if (!root.id) root.id = id;

        const selectable = root.hasAttribute('data-dt-selectable');
        const searchInput = root.querySelector<HTMLInputElement>('[data-dt-search]');
        const rows = this.collectRows(root);

        if (selectable) {
            this.ensureRowChecks(rows);
        }

        const instance: DtInstance = {
            root,
            searchInput,
            rows,
            focusedIndex: -1,
            selectable,
            selectAll: root.querySelector<HTMLInputElement>('[data-dt-select-all]'),
            bulkBar: root.querySelector<HTMLElement>('[data-dt-bulk-bar]'),
            bulkCount: root.querySelector<HTMLElement>('[data-dt-bulk-count]'),
            selected: new Set(),
        };

        this.tables.set(id, instance);
        this.bind(instance);
        this.syncSelectionUi(instance);
    }

    private collectRows(root: HTMLElement): HTMLTableRowElement[] {
        return Array.from(
            root.querySelectorAll<HTMLTableRowElement>('tbody tr:not(.dt__empty-row)'),
        );
    }

    /** Inject a checkbox cell when the row was authored without one. */
    private ensureRowChecks(rows: HTMLTableRowElement[]): void {
        rows.forEach((row, index) => {
            if (row.querySelector('[data-dt-row-check]')) return;

            const id = row.dataset.dtRowId || `row-${index + 1}`;
            row.dataset.dtRowId = id;

            const td = document.createElement('td');
            td.className = 'dt__td dt__td--check';
            td.innerHTML = `
                <label class="dt__check">
                    <input type="checkbox" value="${this.escapeAttr(id)}" data-dt-row-check aria-label="Select row">
                    <span class="dt__check-box" aria-hidden="true"></span>
                </label>
            `;
            row.prepend(td);
        });
    }

    private escapeAttr(value: string): string {
        return value
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    private bind(dt: DtInstance): void {
        if (dt.searchInput) {
            dt.searchInput.addEventListener('input', () => this.onSearch(dt));
        }

        dt.root.addEventListener('keydown', (e) => this.onKeydown(e, dt));

        if (!dt.selectable) return;

        dt.selectAll?.addEventListener('change', () => {
            const on = !!dt.selectAll?.checked;
            this.visibleRows(dt).forEach((row) => this.setRowSelected(dt, row, on));
            this.syncSelectionUi(dt);
        });

        dt.root.addEventListener('change', (e) => {
            const target = e.target as HTMLElement | null;
            if (!(target instanceof HTMLInputElement) || !target.matches('[data-dt-row-check]')) {
                return;
            }
            const row = target.closest('tr');
            if (!row) return;
            this.setRowSelected(dt, row, target.checked);
            this.syncSelectionUi(dt);
        });

        dt.root.querySelector('[data-dt-clear-selection]')?.addEventListener('click', () => {
            this.clearSelection(dt.root.id);
        });

        dt.root.querySelectorAll<HTMLElement>('[data-dt-bulk]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const action = btn.getAttribute('data-dt-bulk') || 'action';
                const ids = this.getSelected(dt.root.id);
                const detail: BulkDetail = { tableId: dt.root.id, action, ids };
                dt.root.dispatchEvent(
                    new CustomEvent('dt:bulk', { bubbles: true, detail }),
                );
            });
        });
    }

    private visibleRows(dt: DtInstance): HTMLTableRowElement[] {
        return dt.rows.filter((r) => r.style.display !== 'none');
    }

    private rowId(row: HTMLTableRowElement): string {
        return row.dataset.dtRowId || row.querySelector<HTMLInputElement>('[data-dt-row-check]')?.value || '';
    }

    private setRowSelected(dt: DtInstance, row: HTMLTableRowElement, on: boolean): void {
        const id = this.rowId(row);
        const check = row.querySelector<HTMLInputElement>('[data-dt-row-check]');
        if (check) check.checked = on;
        row.classList.toggle('dt__row--selected', on);
        if (!id) return;
        if (on) dt.selected.add(id);
        else dt.selected.delete(id);
    }

    private syncSelectionUi(dt: DtInstance): void {
        const visible = this.visibleRows(dt);
        const selectedVisible = visible.filter((r) => r.classList.contains('dt__row--selected'));
        const n = dt.selected.size;

        if (dt.selectAll) {
            const allOn = visible.length > 0 && selectedVisible.length === visible.length;
            const someOn = selectedVisible.length > 0 && !allOn;
            dt.selectAll.checked = allOn;
            dt.selectAll.indeterminate = someOn;
        }

        if (dt.bulkBar) {
            dt.bulkBar.hidden = n === 0;
        }
        if (dt.bulkCount) {
            dt.bulkCount.textContent = `${n} selected`;
        }
    }

    private onSearch(dt: DtInstance): void {
        const query = dt.searchInput!.value.toLowerCase().trim();
        let visibleCount = 0;

        dt.rows.forEach((row) => {
            const searchable = row.dataset.dtSearchable ?? row.textContent ?? '';
            const matches = !query || searchable.toLowerCase().includes(query);
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        const empty = dt.root.querySelector('.dt__empty-row');
        if (empty) {
            (empty as HTMLElement).style.display = visibleCount === 0 ? '' : 'none';
        }

        const info = dt.root.querySelector('.dt__info');
        if (info && query) {
            info.textContent = `${visibleCount} result${visibleCount !== 1 ? 's' : ''}`;
        }

        this.syncSelectionUi(dt);
    }

    private onKeydown(e: KeyboardEvent, dt: DtInstance): void {
        const visible = this.visibleRows(dt);
        if (!visible.length) return;

        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            dt.rows.forEach((r) => r.classList.remove('dt__row--focused'));

            if (e.key === 'ArrowDown') {
                dt.focusedIndex = Math.min(dt.focusedIndex + 1, visible.length - 1);
            } else {
                dt.focusedIndex = Math.max(dt.focusedIndex - 1, 0);
            }

            visible[dt.focusedIndex]?.classList.add('dt__row--focused');
            visible[dt.focusedIndex]?.scrollIntoView({ block: 'nearest' });
            return;
        }

        if (e.key === ' ' && dt.selectable && dt.focusedIndex >= 0) {
            const row = visible[dt.focusedIndex];
            if (!row) return;
            e.preventDefault();
            const on = !row.classList.contains('dt__row--selected');
            this.setRowSelected(dt, row, on);
            this.syncSelectionUi(dt);
        }
    }

    /** Programmatic refresh if rows are added/removed dynamically */
    refresh(tableId: string): void {
        const dt = this.tables.get(tableId);
        if (!dt) return;
        dt.rows = this.collectRows(dt.root);
        if (dt.selectable) this.ensureRowChecks(dt.rows);
        dt.focusedIndex = -1;
        // Drop ids that no longer exist
        const live = new Set(dt.rows.map((r) => this.rowId(r)));
        [...dt.selected].forEach((id) => {
            if (!live.has(id)) dt.selected.delete(id);
        });
        this.syncSelectionUi(dt);
    }

    getSelected(tableId: string): string[] {
        return [...(this.tables.get(tableId)?.selected ?? [])];
    }

    clearSelection(tableId: string): void {
        const dt = this.tables.get(tableId);
        if (!dt) return;
        dt.rows.forEach((row) => this.setRowSelected(dt, row, false));
        this.syncSelectionUi(dt);
    }
}
