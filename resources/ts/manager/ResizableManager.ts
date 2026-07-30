/**
 * ResizableManager — draggable split panes.
 *
 * The handle resizes its previous sibling pane (flex-basis in px, clamped
 * by data-min / data-max on the pane, defaults 120px / unbounded).
 */
export class ResizableManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-resizable] [data-resizable-handle], .resizable > .resizable-handle')
            .forEach((handle) => this.setup(handle as HTMLElement));
    }

    private setup(handle: HTMLElement): void {
        const pane = handle.previousElementSibling as HTMLElement | null;
        if (!pane) return;

        handle.addEventListener('pointerdown', (e: PointerEvent) => {
            e.preventDefault();
            handle.setPointerCapture(e.pointerId);
            handle.classList.add('resizable-handle--active');

            const startX = e.clientX;
            const startWidth = pane.getBoundingClientRect().width;
            const min = Number(pane.dataset.min ?? 120);
            const max = Number(pane.dataset.max ?? Infinity);

            const onMove = (ev: PointerEvent) => {
                const width = Math.min(max, Math.max(min, startWidth + (ev.clientX - startX)));
                pane.style.flexBasis = `${width}px`;
            };

            const onUp = () => {
                handle.classList.remove('resizable-handle--active');
                handle.removeEventListener('pointermove', onMove);
                handle.removeEventListener('pointerup', onUp);
            };

            handle.addEventListener('pointermove', onMove);
            handle.addEventListener('pointerup', onUp);
        });
    }
}
