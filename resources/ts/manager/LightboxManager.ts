/**
 * LightboxManager — fullscreen media viewer for [data-lightbox] anchors.
 *
 *   <a href="/img/full.jpg" data-lightbox="gallery" data-caption="…">…</a>
 *
 * Items with the same data-lightbox value form a group navigable with
 * arrows / arrow keys. Closes on ×, backdrop click, or Escape.
 */
export class LightboxManager {
    private overlay: HTMLElement | null = null;
    private items: HTMLAnchorElement[] = [];
    private index = 0;

    constructor() {
        document.addEventListener('click', (e) => {
            const anchor = (e.target as HTMLElement | null)?.closest<HTMLAnchorElement>('a[data-lightbox]');
            if (!anchor) return;
            e.preventDefault();
            const group = anchor.dataset.lightbox ?? '';
            this.items = Array.from(document.querySelectorAll<HTMLAnchorElement>(`a[data-lightbox="${group}"]`));
            this.index = Math.max(0, this.items.indexOf(anchor));
            this.open();
        });

        document.addEventListener('keydown', (e) => {
            if (!this.overlay) return;
            if (e.key === 'Escape') this.close();
            if (e.key === 'ArrowLeft') this.step(-1);
            if (e.key === 'ArrowRight') this.step(1);
        });
    }

    private open(): void {
        this.close();

        const overlay = document.createElement('div');
        overlay.className = 'lightbox-overlay';
        overlay.innerHTML = `
            <img class="lightbox-media" src="" alt="">
            <span class="lightbox-counter"></span>
            <p class="lightbox-caption"></p>
            <button type="button" class="lightbox-btn lightbox-btn--close" aria-label="Close">&times;</button>
            <button type="button" class="lightbox-btn lightbox-btn--prev" aria-label="Previous">&lsaquo;</button>
            <button type="button" class="lightbox-btn lightbox-btn--next" aria-label="Next">&rsaquo;</button>`;

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) this.close();
        });
        overlay.querySelector('.lightbox-btn--close')?.addEventListener('click', () => this.close());
        overlay.querySelector('.lightbox-btn--prev')?.addEventListener('click', () => this.step(-1));
        overlay.querySelector('.lightbox-btn--next')?.addEventListener('click', () => this.step(1));

        document.body.appendChild(overlay);
        this.overlay = overlay;
        this.render();
        requestAnimationFrame(() => overlay.classList.add('lightbox-overlay--visible'));
    }

    private render(): void {
        if (!this.overlay) return;
        const item = this.items[this.index];
        if (!item) return;

        const media = this.overlay.querySelector<HTMLImageElement>('.lightbox-media');
        const caption = this.overlay.querySelector('.lightbox-caption');
        const counter = this.overlay.querySelector('.lightbox-counter');
        if (media) media.src = item.href;
        if (caption) caption.textContent = item.dataset.caption ?? '';
        if (counter) counter.textContent = this.items.length > 1 ? `${this.index + 1} / ${this.items.length}` : '';

        const single = this.items.length <= 1;
        this.overlay.querySelector<HTMLElement>('.lightbox-btn--prev')?.toggleAttribute('hidden', single);
        this.overlay.querySelector<HTMLElement>('.lightbox-btn--next')?.toggleAttribute('hidden', single);
    }

    private step(dir: number): void {
        if (this.items.length <= 1) return;
        this.index = (this.index + dir + this.items.length) % this.items.length;
        this.render();
    }

    close(): void {
        this.overlay?.remove();
        this.overlay = null;
    }
}
