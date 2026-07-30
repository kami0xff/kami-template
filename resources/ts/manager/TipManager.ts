/**
 * TipManager — amount presets, custom input, tip-item → amount sync.
 */

type TipRoot = HTMLElement & { __tipBound?: boolean };

export class TipManager {
    constructor() {
        document.querySelectorAll<HTMLElement>('[data-tipping]').forEach((root) => this.mount(root));
    }

    mount(root: HTMLElement): void {
        const el = root as TipRoot;
        if (el.__tipBound) return;
        el.__tipBound = true;

        const amounts = root.querySelector<HTMLElement>('[data-tipping-amounts]');
        const custom = root.querySelector<HTMLInputElement>('[data-tipping-custom]');
        const cta = root.querySelector<HTMLButtonElement>('[data-tipping-cta]');
        const balanceNode = root.querySelector<HTMLElement>('[data-tipping-balance]');
        const status = root.querySelector<HTMLElement>('[data-tipping-status]');

        const setAmount = (n: number, syncCustom = true) => {
            const value = Math.max(1, Math.round(n) || 1);
            root.querySelectorAll<HTMLElement>('[data-tipping-amount]').forEach((btn) => {
                const v = Number(btn.getAttribute('data-tipping-amount'));
                btn.classList.toggle('is-active', v === value);
            });
            if (syncCustom && custom) custom.value = String(value);

            root.querySelectorAll<HTMLElement>('[data-tipping-item]').forEach((item) => {
                const tokens = Number(item.getAttribute('data-tokens'));
                item.classList.toggle('is-active', tokens === value);
            });

            root.dispatchEvent(
                new CustomEvent('tip:amount', {
                    bubbles: true,
                    detail: { amount: value },
                }),
            );
        };

        root.querySelectorAll<HTMLElement>('[data-tipping-amount]').forEach((btn) => {
            btn.addEventListener('click', () => {
                setAmount(Number(btn.getAttribute('data-tipping-amount')));
            });
        });

        custom?.addEventListener('input', () => {
            const n = Number(custom.value);
            if (!Number.isFinite(n) || n <= 0) return;
            setAmount(n, false);
        });

        root.querySelectorAll<HTMLElement>('[data-tipping-item]').forEach((item) => {
            item.addEventListener('click', () => {
                const tokens = Number(item.getAttribute('data-tokens'));
                if (!Number.isFinite(tokens)) return;
                setAmount(tokens);
            });
        });

        root.querySelectorAll<HTMLElement>('[data-tipping-tab]').forEach((tab) => {
            tab.addEventListener('click', () => {
                root.querySelectorAll<HTMLElement>('[data-tipping-tab]').forEach((t) => {
                    t.classList.toggle('is-active', t === tab);
                });
                const panel = tab.getAttribute('data-tipping-tab');
                root.querySelectorAll<HTMLElement>('[data-tipping-panel]').forEach((p) => {
                    p.hidden = p.getAttribute('data-tipping-panel') !== panel;
                });
            });
        });

        cta?.addEventListener('click', () => {
            const amount = Number(custom?.value || root.querySelector('.tipping-amount.is-active')?.getAttribute('data-tipping-amount') || 0);
            const action = amounts?.getAttribute('data-action') || 'tip';
            root.dispatchEvent(
                new CustomEvent('tip:submit', {
                    bubbles: true,
                    detail: { action, amount },
                }),
            );
        });

        // Keep balance line in sync if present
        if (status && balanceNode && !balanceNode.textContent?.trim()) {
            status.hidden = true;
        }
    }
}
