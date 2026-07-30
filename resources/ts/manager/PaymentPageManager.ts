/**
 * PaymentPageManager — UI flow for [data-payment-checkout].
 *
 * Stages: select → gateway → status.
 * Real money moves on the third-party provider; listen for:
 *   payment:continue  { amount, price, method?, provider }
 *   payment:complete  { state, amount, price }
 * and credit the wallet from your backend after the provider webhook/return.
 */

type Stage = 'select' | 'gateway' | 'status';

function statusMarkup(state: 'success' | 'failed' | 'pending', amount: string): string {
    const copy = {
        success: {
            title: 'Payment confirmed',
            text: `${amount} tokens are on the way to your wallet. Refresh if the balance takes a moment to update.`,
            icon: `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg>`,
        },
        failed: {
            title: 'Payment failed',
            text: 'The provider couldn’t complete this charge. No tokens were added.',
            icon: `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6 6 18M6 6l12 12"/></svg>`,
        },
        pending: {
            title: 'Payment pending',
            text: 'Waiting on gateway confirmation. Your wallet updates when it clears.',
            icon: `<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l3 2"/></svg>`,
        },
    }[state];

    return `
        <div class="payment-status payment-status--${state}" data-payment-status="${state}">
            <span class="payment-status-icon" aria-hidden="true">${copy.icon}</span>
            <h2 class="payment-status-title">${copy.title}</h2>
            <p class="payment-status-text">${copy.text}</p>
            <div class="payment-status-actions">
                <a class="btn btn-primary" href="/design#wallet">Back to wallet</a>
                <button type="button" class="btn btn-muted" data-payment-restart>Buy more</button>
            </div>
        </div>
    `;
}

export class PaymentPageManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-payment-checkout]').forEach((root) => {
            this.bind(root);
            this.syncSummary(root);
        });
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('change', (e) => {
            const t = e.target as HTMLElement;
            if (t.closest('[data-wallet-pack], .payment-methods')) {
                this.syncSummary(root);
            }
        });

        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const pack = t.closest<HTMLElement>('[data-wallet-pack]');
            if (pack && root.contains(pack)) {
                root.querySelectorAll('.wallet-pack.is-selected').forEach((el) => el.classList.remove('is-selected'));
                pack.classList.add('is-selected');
                const input = pack.querySelector<HTMLInputElement>('input[type="radio"]');
                if (input && !input.checked) {
                    input.checked = true;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.syncSummary(root);
                return;
            }

            if (t.closest('[data-payment-continue]') && root.contains(t.closest('[data-payment-continue]')!)) {
                e.preventDefault();
                const detail = this.selectedPack(root);
                if (!detail) return;
                root.dispatchEvent(new CustomEvent('payment:continue', { bubbles: true, detail }));
                this.setStage(root, 'gateway');
                return;
            }

            if (t.closest('[data-payment-back]') && root.contains(t.closest('[data-payment-back]')!)) {
                e.preventDefault();
                this.setStage(root, 'select');
                return;
            }

            const sim = t.closest<HTMLElement>('[data-payment-simulate]');
            if (sim && root.contains(sim)) {
                e.preventDefault();
                const state = (sim.dataset.paymentSimulate as 'success' | 'failed') || 'success';
                this.complete(root, state);
                return;
            }

            if (t.closest('[data-payment-restart]') && root.contains(t.closest('[data-payment-restart]')!)) {
                e.preventDefault();
                this.setStage(root, 'select');
            }
        });
    }

    private selectedPack(root: HTMLElement): { amount: string; price: string; method: string | null; provider: string } | null {
        const pack =
            root.querySelector<HTMLElement>('[data-wallet-pack] input:checked')?.closest<HTMLElement>('[data-wallet-pack]') ??
            root.querySelector<HTMLElement>('[data-wallet-pack].is-selected');
        if (!pack) return null;

        const methodInput = root.querySelector<HTMLInputElement>('.payment-methods input:checked');
        return {
            amount: pack.dataset.packAmount ?? '',
            price: pack.dataset.packPrice ?? '',
            method: methodInput?.value ?? null,
            provider: root.dataset.provider ?? 'provider',
        };
    }

    private syncSummary(root: HTMLElement): void {
        const pack = this.selectedPack(root);
        const amountLabel = pack?.amount ? `${Number(pack.amount).toLocaleString()} tokens` : '—';
        const priceLabel = pack?.price || '—';

        root.querySelectorAll<HTMLElement>('[data-payment-summary-pack]').forEach((el) => {
            el.textContent = amountLabel;
        });
        root.querySelectorAll<HTMLElement>('[data-payment-summary-price]').forEach((el) => {
            el.textContent = priceLabel;
        });
    }

    private setStage(root: HTMLElement, stage: Stage): void {
        root.dataset.stage = stage;
        root.querySelectorAll<HTMLElement>('[data-payment-stage]').forEach((el) => {
            el.hidden = el.dataset.paymentStage !== stage;
        });
    }

    private complete(root: HTMLElement, state: 'success' | 'failed' | 'pending'): void {
        const pack = this.selectedPack(root);
        const host = root.querySelector<HTMLElement>('[data-payment-status-host]');
        if (host) {
            host.innerHTML = statusMarkup(state, pack?.amount ? Number(pack.amount).toLocaleString() : '');
        }
        this.setStage(root, 'status');
        root.dispatchEvent(
            new CustomEvent('payment:complete', {
                bubbles: true,
                detail: { state, amount: pack?.amount ?? null, price: pack?.price ?? null },
            }),
        );
    }
}
