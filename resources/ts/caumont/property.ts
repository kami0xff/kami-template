/**
 * Caumont property page — sale / rental / short-stay modes.
 */
const MODES = ['sale', 'rental', 'stay'] as const;
type Mode = (typeof MODES)[number];

function fmt(n: number): string {
    return '€ ' + Math.round(n).toLocaleString('en-US');
}

export function bootCaumontProperty(): void {
    const setMode = (mode: string, fromUser = false) => {
        const m = (MODES as readonly string[]).includes(mode) ? (mode as Mode) : 'sale';
        document.body.setAttribute('data-mode', m);
        document.querySelectorAll('[data-mode-tab]').forEach((t) => {
            const on = t.getAttribute('data-mode-tab') === m;
            t.classList.toggle('is-active', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        if (history.replaceState) history.replaceState(null, '', '#' + m);
        if (fromUser) window.dispatchEvent(new CustomEvent('caumont:mode', { detail: m }));
    };

    (window as unknown as { setMode: typeof setMode }).setMode = setMode;

    document.querySelectorAll('[data-mode-tab]').forEach((t) => {
        t.addEventListener('click', () => setMode(t.getAttribute('data-mode-tab') || 'sale', true));
    });

    setMode((location.hash || '#sale').slice(1), false);

    // Sticky header (shared class names with home)
    const header = document.querySelector('header.site');
    if (header) {
        const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 40);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // Default short-stay math if bindings exist
    const rate = 4200;
    const sub = rate * 7;
    document.querySelectorAll('[data-bind="rate"], [data-bind="rate-key"]').forEach((el) => {
        el.textContent = fmt(rate);
    });
    document.querySelectorAll('[data-bind="stay-sub"]').forEach((el) => {
        el.textContent = fmt(sub);
    });
    document.querySelectorAll('[data-bind="stay-total"]').forEach((el) => {
        el.textContent = fmt(sub + 1400 + 600);
    });
}
