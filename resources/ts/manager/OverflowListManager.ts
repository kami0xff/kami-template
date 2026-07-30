/**
 * OverflowListManager — horizontal scroll rails with arrow paddles.
 *
 * Markup contract (see components/overflow-list.blade.php):
 *   <div class="overflow-list" data-overflow-list>
 *       <button class="overflow-list-arrow overflow-list-arrow--prev" data-overflow-prev>…</button>
 *       <div class="overflow-list-track">…pills…</div>
 *       <button class="overflow-list-arrow overflow-list-arrow--next" data-overflow-next>…</button>
 *   </div>
 *
 * Sets data-at-start / data-at-end on the root so CSS can hide paddles
 * and adjust the edge fades.
 */
export class OverflowListManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-overflow-list]').forEach((el) => this.setup(el));
    }

    private setup(el: HTMLElement): void {
        const track = el.querySelector<HTMLElement>('.overflow-list-track');
        if (!track) return;

        const update = () => {
            const atStart = track.scrollLeft <= 1;
            const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
            el.toggleAttribute('data-at-start', atStart);
            el.toggleAttribute('data-at-end', atEnd);
        };

        const scrollBy = (dir: 1 | -1) => {
            track.scrollBy({ left: dir * track.clientWidth * 0.6, behavior: 'smooth' });
        };

        el.querySelector('[data-overflow-prev]')?.addEventListener('click', () => scrollBy(-1));
        el.querySelector('[data-overflow-next]')?.addEventListener('click', () => scrollBy(1));

        track.addEventListener('scroll', update, { passive: true });
        new ResizeObserver(update).observe(track);
        update();
    }
}
