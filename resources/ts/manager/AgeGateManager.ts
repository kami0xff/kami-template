/**
 * AgeGateManager — opens consent modal until accepted (localStorage).
 *
 *   <div data-age-gate data-age-gate-id="age-gate" data-storage-key="…" data-auto="1">
 *     <x-modal.modal id="age-gate">…</x-modal.modal>
 *   </div>
 *
 *   window.ageGate.accept('age-gate')
 */
export class AgeGateManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-age-gate]').forEach((wrap) => {
            wrap.hidden = false;
            const id = wrap.dataset.ageGateId || 'age-gate';
            const key = wrap.dataset.storageKey || 'kami.age-gate.ok';
            const auto = wrap.dataset.auto !== '0';
            wrap.querySelector<HTMLElement>('[data-age-enter]')?.addEventListener('click', () => {
                this.accept(id, key);
            });
            if (auto && !this.hasAccepted(key)) {
                queueMicrotask(() => window.modals?.open?.(id));
            }
        });
    }

    hasAccepted(key = 'kami.age-gate.ok'): boolean {
        try {
            return localStorage.getItem(key) === '1';
        } catch {
            return false;
        }
    }

    accept(id: string, key = 'kami.age-gate.ok'): void {
        try {
            localStorage.setItem(key, '1');
        } catch {
            /* private mode */
        }
        window.modals?.close?.(id);
        document.dispatchEvent(new CustomEvent('age-gate:accept', { detail: { id } }));
    }
}
