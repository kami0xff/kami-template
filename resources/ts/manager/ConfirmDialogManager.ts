/**
 * ConfirmDialogManager — promise-based confirm dialogs built on modal CSS.
 *
 *   const ok = await window.dialogs.confirm({
 *       title: 'Delete photo?',
 *       message: 'This cannot be undone.',
 *       confirmText: 'Delete',
 *       danger: true,
 *   });
 *
 * The dialog DOM is created on demand and removed on resolve, so no
 * markup needs to exist on the page.
 */
export interface ConfirmOptions {
    title: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    danger?: boolean;
}

export class ConfirmDialogManager {
    confirm(options: ConfirmOptions): Promise<boolean> {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'modal-overlay modal-active';

            const confirmVariant = options.danger ? 'btn-danger' : 'btn-primary';
            overlay.innerHTML = `
                <div class="modal modal--confirm" role="alertdialog" aria-modal="true" aria-label="${escapeHtml(options.title)}">
                    <div class="modal-body" style="display:grid; gap:var(--space2); padding:var(--space5);">
                        <strong style="font-size:var(--text-subheading);">${escapeHtml(options.title)}</strong>
                        ${options.message ? `<p style="color:var(--text-muted); margin:0;">${escapeHtml(options.message)}</p>` : ''}
                        <div style="display:flex; gap:var(--space2); justify-content:flex-end; margin-top:var(--space3);">
                            <button type="button" class="btn btn-muted" data-confirm-cancel>${escapeHtml(options.cancelText ?? 'Cancel')}</button>
                            <button type="button" class="btn ${confirmVariant}" data-confirm-ok>${escapeHtml(options.confirmText ?? 'Confirm')}</button>
                        </div>
                    </div>
                </div>`;

            const settle = (value: boolean) => {
                overlay.remove();
                document.removeEventListener('keydown', onKey);
                resolve(value);
            };

            const onKey = (e: KeyboardEvent) => {
                if (e.key === 'Escape') settle(false);
            };

            overlay.addEventListener('pointerdown', (e) => {
                if (e.target === overlay) settle(false);
            });
            overlay.querySelector('[data-confirm-cancel]')?.addEventListener('click', () => settle(false));
            overlay.querySelector('[data-confirm-ok]')?.addEventListener('click', () => settle(true));
            document.addEventListener('keydown', onKey);

            document.body.appendChild(overlay);
            (overlay.querySelector('[data-confirm-ok]') as HTMLElement | null)?.focus();
        });
    }
}

function escapeHtml(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
