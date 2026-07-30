/**
 * CreateModelManager — multi-step "create model" wizard.
 * Mount: [data-ai-create-wizard]
 * On submit, appends a card into [data-ai-studio-grid] if present.
 */

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

export class CreateModelManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-ai-create-wizard]').forEach((root) => this.bind(root));
    }

    private bind(root: HTMLElement): void {
        let step = 1;
        const form = root.querySelector<HTMLFormElement>('[data-ai-wizard-form]');
        if (!form) return;

        const sync = () => {
            root.querySelectorAll<HTMLElement>('[data-ai-wizard-panel]').forEach((panel) => {
                panel.hidden = Number(panel.dataset.aiWizardPanel) !== step;
            });
            root.querySelectorAll<HTMLElement>('[data-ai-wizard-goto]').forEach((btn) => {
                btn.classList.toggle('is-active', Number(btn.dataset.aiWizardGoto) === step);
            });
            const back = root.querySelector<HTMLElement>('[data-ai-wizard-back]');
            const next = root.querySelector<HTMLElement>('[data-ai-wizard-next]');
            const submit = root.querySelector<HTMLElement>('[data-ai-wizard-submit]');
            if (back) back.hidden = step <= 1;
            if (next) next.hidden = step >= 4;
            if (submit) submit.hidden = step !== 4;
            if (step === 4) this.fillReview(root, form);
        };

        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const goto = t.closest<HTMLElement>('[data-ai-wizard-goto]');
            if (goto && root.contains(goto)) {
                step = Number(goto.dataset.aiWizardGoto) || 1;
                sync();
                return;
            }

            if (t.closest('[data-ai-wizard-back]') && root.contains(t.closest('[data-ai-wizard-back]')!)) {
                step = Math.max(1, step - 1);
                sync();
                return;
            }

            if (t.closest('[data-ai-wizard-next]') && root.contains(t.closest('[data-ai-wizard-next]')!)) {
                if (step === 1 || step === 2 || (step === 3 && form.reportValidity())) {
                    step = Math.min(4, step + 1);
                    sync();
                }
                return;
            }

            const typeLabel = t.closest<HTMLElement>('[data-ai-wizard-panel="1"] .ai-mode-tab');
            if (typeLabel && root.contains(typeLabel)) {
                root.querySelectorAll('[data-ai-wizard-panel="1"] .ai-mode-tab').forEach((el) => el.classList.remove('is-active'));
                typeLabel.classList.add('is-active');
                const input = typeLabel.querySelector<HTMLInputElement>('input[name="type"]');
                if (input) input.checked = true;
            }
        });

        form.querySelector<HTMLInputElement>('[data-ai-wizard-file]')?.addEventListener('change', (e) => {
            const file = (e.target as HTMLInputElement).files?.[0];
            const label = root.querySelector('[data-ai-wizard-file-label]');
            if (label && file) label.textContent = file.name;
        });
        form.querySelector<HTMLInputElement>('[data-ai-wizard-cover]')?.addEventListener('change', (e) => {
            const file = (e.target as HTMLInputElement).files?.[0];
            const label = root.querySelector('[data-ai-wizard-cover-label]');
            if (label && file) label.textContent = file.name;
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const data = new FormData(form);
            const type = String(data.get('type') || 'checkpoint');
            const name = String(data.get('name') || 'Untitled').trim();
            const version = String(data.get('version') || 'v1.0');
            const base = String(data.get('base') || 'SDXL');
            const id = `user-${type}-${Date.now().toString(36)}`;
            const coverInput = form.querySelector<HTMLInputElement>('[data-ai-wizard-cover]');
            const coverFile = coverInput?.files?.[0];
            const image = coverFile
                ? URL.createObjectURL(coverFile)
                : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=400&h=520&fit=crop';

            const card = `<button type="button" class="ai-resource" data-ai-resource
                data-resource-id="${esc(id)}"
                data-resource-type="${esc(type)}"
                data-resource-name="${esc(name)}"
                data-resource-version="${esc(version)}"
                data-resource-base="${esc(base)}"
                data-resource-family="${esc(base)}"
                data-resource-image="${esc(image)}">
                <span class="ai-resource-badge ai-resource-badge--${esc(type)}">${esc(type)}</span>
                <span class="ai-resource-thumb"><img src="${esc(image)}" alt="" loading="lazy"></span>
                <span class="ai-resource-meta">
                    <span class="ai-resource-title">${esc(name)}</span>
                    <span class="ai-resource-sub">${esc(version)} · ${esc(base)}</span>
                </span>
            </button>`;

            document.querySelectorAll('[data-ai-studio-grid]').forEach((grid) => {
                grid.insertAdjacentHTML('afterbegin', card);
            });

            root.dispatchEvent(
                new CustomEvent('ai-model:created', {
                    bubbles: true,
                    detail: { id, type, name, version, base, image },
                }),
            );
            window.toasts?.success?.(`Created “${name}” in library (demo)`);
            form.reset();
            step = 1;
            sync();
            document.querySelector('#ai-studio')?.scrollIntoView({ behavior: 'smooth' });
        });

        sync();
    }

    private fillReview(root: HTMLElement, form: HTMLFormElement): void {
        const data = new FormData(form);
        const review = root.querySelector('[data-ai-wizard-review]');
        if (!review) return;
        const rows: [string, string][] = [
            ['Type', String(data.get('type') || '')],
            ['Name', String(data.get('name') || '')],
            ['Version', String(data.get('version') || '')],
            ['Base', String(data.get('base') || '')],
            ['Tags', String(data.get('tags') || '—')],
            ['Public', data.get('public') ? 'Yes' : 'No'],
            ['Weights', form.querySelector<HTMLInputElement>('[data-ai-wizard-file]')?.files?.[0]?.name || '—'],
        ];
        review.innerHTML = rows
            .map(([k, v]) => `<div><span>${esc(k)}</span><strong>${esc(v || '—')}</strong></div>`)
            .join('');
    }
}
