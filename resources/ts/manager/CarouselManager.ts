/**
 * CarouselManager — scroll-snap carousels with arrows and dots.
 *
 * Markup contract (see components/carousel.blade.php):
 *   <div class="carousel" data-carousel>
 *       <button data-carousel-prev>…</button>
 *       <div class="carousel-track"> …slides… </div>
 *       <button data-carousel-next>…</button>
 *       <div class="carousel-dots" data-carousel-dots></div>
 *   </div>
 */
export class CarouselManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-carousel]').forEach((el) => this.setup(el));
    }

    private setup(el: HTMLElement): void {
        const track = el.querySelector<HTMLElement>('.carousel-track');
        if (!track) return;
        const slides = Array.from(track.children) as HTMLElement[];
        const dotsHost = el.querySelector<HTMLElement>('[data-carousel-dots]');

        const dots: HTMLButtonElement[] = [];
        if (dotsHost && slides.length > 1) {
            slides.forEach((slide, i) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'carousel-dot';
                dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                dot.addEventListener('click', () => {
                    track.scrollTo({ left: slide.offsetLeft, behavior: 'smooth' });
                });
                dotsHost.appendChild(dot);
                dots.push(dot);
            });
        }

        const currentIndex = (): number => {
            let best = 0;
            let bestDist = Infinity;
            slides.forEach((slide, i) => {
                const dist = Math.abs(slide.offsetLeft - track.scrollLeft);
                if (dist < bestDist) {
                    bestDist = dist;
                    best = i;
                }
            });
            return best;
        };

        const update = () => {
            const index = currentIndex();
            dots.forEach((dot, i) => dot.classList.toggle('carousel-dot--active', i === index));
        };

        const step = (dir: number) => {
            const next = Math.min(slides.length - 1, Math.max(0, currentIndex() + dir));
            const slide = slides[next];
            if (slide) track.scrollTo({ left: slide.offsetLeft, behavior: 'smooth' });
        };

        el.querySelector('[data-carousel-prev]')?.addEventListener('click', () => step(-1));
        el.querySelector('[data-carousel-next]')?.addEventListener('click', () => step(1));
        track.addEventListener('scroll', update, { passive: true });
        update();
    }
}
