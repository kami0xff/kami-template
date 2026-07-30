/**
 * Caumont agency home — sticky header, mega menu, portal tabs, reveals.
 */
export function bootCaumontSite(): void {
    const header = document.getElementById('header');
    if (!header) return;

    const onScroll = () => {
        header.classList.toggle('scrolled', window.scrollY > 40);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('in');
                    io.unobserve(e.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
    );
    document.querySelectorAll('.reveal:not(.in)').forEach((el) => io.observe(el));

    const backdrop = document.getElementById('mega-backdrop');
    const items = header.querySelectorAll('.nav-has-mega');
    const panels = header.querySelectorAll('.mega');
    let closeTimer: ReturnType<typeof setTimeout> | null = null;
    const CLOSE_DELAY = 160;

    const setOpen = (key: string | null) => {
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }
        items.forEach((it) => {
            it.classList.toggle('is-mega-open', it.getAttribute('data-mega') === key);
        });
        panels.forEach((p) => {
            const on = p.getAttribute('data-mega-panel') === key;
            p.classList.toggle('is-open', on);
            p.setAttribute('aria-hidden', on ? 'false' : 'true');
        });
        header.classList.toggle('is-mega-open', !!key);
        backdrop?.classList.toggle('is-open', !!key);
    };

    const scheduleClose = () => {
        if (closeTimer) clearTimeout(closeTimer);
        closeTimer = setTimeout(() => setOpen(null), CLOSE_DELAY);
    };

    items.forEach((item) => {
        const key = item.getAttribute('data-mega');
        item.addEventListener('mouseenter', () => setOpen(key));
        item.addEventListener('mouseleave', scheduleClose);
        item.querySelector('a')?.addEventListener('focus', () => setOpen(key));
    });
    panels.forEach((panel) => {
        panel.addEventListener('mouseenter', () => {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }
        });
        panel.addEventListener('mouseleave', scheduleClose);
    });
    backdrop?.addEventListener('click', () => setOpen(null));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setOpen(null);
    });
    header.querySelectorAll('.mega a, .mega-feature').forEach((a) => {
        a.addEventListener('click', () => setTimeout(() => setOpen(null), 50));
    });

    const portal = document.getElementById('portal');
    portal?.querySelectorAll('.ptab').forEach((tab) => {
        tab.addEventListener('click', () => {
            const v = tab.getAttribute('data-tab');
            if (!v) return;
            portal.setAttribute('data-view', v);
            portal.querySelectorAll('.ptab').forEach((t) => {
                const on = t === tab;
                t.classList.toggle('is-active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href');
            if (!id || id.length < 2) return;
            const t = document.querySelector(id);
            if (!t) return;
            e.preventDefault();
            const top = t.getBoundingClientRect().top + window.scrollY - 8;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });
}
