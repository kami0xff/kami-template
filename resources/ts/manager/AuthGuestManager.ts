/**
 * AuthGuestManager — guest login / signup modal wiring.
 *
 * - [data-auth-open="login"|"signup"] opens the matching modal
 * - switches close the other modal first
 * - username suggest, social stub toasts, signup promo dismiss
 */
export class AuthGuestManager {
    private loginId = 'auth-login';
    private signupId = 'auth-signup';

    constructor() {
        document.addEventListener('click', (e) => {
            const t = (e.target as HTMLElement | null)?.closest<HTMLElement>('[data-auth-open]');
            if (!t) return;
            e.preventDefault();
            const which = t.dataset.authOpen;
            if (which === 'login' || which === 'signup') this.open(which);
        });

        document.querySelectorAll<HTMLElement>('[data-auth-suggest-username]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = btn.closest('form')?.querySelector<HTMLInputElement>('[data-auth-username]');
                if (!input) return;
                input.value = this.suggestUsername();
                input.focus();
            });
        });

        document.querySelectorAll<HTMLElement>('[data-auth-social], [data-auth-passless], [data-auth-continue-guest], [data-auth-creator]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const label = btn.getAttribute('aria-label')
                    || btn.textContent?.trim()
                    || 'Action';
                window.toasts?.info?.(`${label} — wire to your auth provider.`);
            });
        });

        document.querySelectorAll<HTMLElement>('[data-signup-promo-close]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const bar = btn.closest<HTMLElement>('[data-signup-promo]');
                if (!bar) return;
                bar.hidden = true;
                try {
                    localStorage.setItem('kami.signup-promo.dismissed', '1');
                } catch {
                    /* ignore */
                }
            });
        });

        // Restore dismissed sticky promo
        try {
            if (localStorage.getItem('kami.signup-promo.dismissed') === '1') {
                document.querySelectorAll<HTMLElement>('[data-signup-promo]').forEach((el) => {
                    el.hidden = true;
                });
            }
        } catch {
            /* ignore */
        }
    }

    open(which: 'login' | 'signup'): void {
        const openId = which === 'login' ? this.loginId : this.signupId;
        const closeId = which === 'login' ? this.signupId : this.loginId;
        window.modals?.close?.(closeId);
        window.modals?.open?.(openId);
    }

    private suggestUsername(): string {
        const adjectives = ['swift', 'neon', 'lucky', 'quiet', 'vivid', 'cosmic'];
        const nouns = ['fox', 'wave', 'pixel', 'nova', 'ember', 'kite'];
        const a = adjectives[Math.floor(Math.random() * adjectives.length)];
        const n = nouns[Math.floor(Math.random() * nouns.length)];
        const num = Math.floor(10 + Math.random() * 89);
        return `${a}_${n}${num}`;
    }
}
