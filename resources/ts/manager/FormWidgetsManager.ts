/**
 * FormWidgetsManager — interactive behaviors for form CSS orphans:
 * slider, tags, token-picker, rating, pwd-strength, combobox, avatar-upload.
 */
export class FormWidgetsManager {
    constructor(scope: ParentNode = document) {
        this.bindSliders(scope);
        this.bindTags(scope);
        this.bindTokenPickers(scope);
        this.bindRatings(scope);
        this.bindPwdStrength(scope);
        this.bindComboboxes(scope);
        this.bindAvatarUploads(scope);
        this.bindFollowButtons(scope);
        this.bindFilterBars(scope);
        this.bindSelectRows(scope);
    }

    private bindSliders(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-slider]').forEach((el) => {
            if (el.dataset.bound === '1') return;
            el.dataset.bound = '1';
            const min = Number(el.dataset.min ?? 0);
            const max = Number(el.dataset.max ?? 100);
            const step = Number(el.dataset.step ?? 1);
            const mode = el.dataset.mode ?? 'single';

            const setPct = (which: '1' | '2', pct: number) => {
                const clamped = Math.min(100, Math.max(0, pct));
                if (mode === 'range') {
                    el.style.setProperty(which === '1' ? '--p1' : '--p2', `${clamped}%`);
                } else {
                    el.style.setProperty('--p', `${clamped}%`);
                }
                const value = Math.round(min + (clamped / 100) * (max - min));
                const snapped = Math.round(value / step) * step;
                const thumb = el.querySelector<HTMLElement>(`[data-thumb="${which}"]`);
                thumb?.setAttribute('aria-valuenow', String(snapped));
                if (mode === 'range') {
                    const key = which === '1' ? 'min' : 'max';
                    const input = el.querySelector<HTMLInputElement>(`[data-slider-input="${key}"]`);
                    if (input) input.value = String(snapped);
                } else {
                    const input = el.querySelector<HTMLInputElement>('[data-slider-input="value"]');
                    if (input) input.value = String(snapped);
                }
            };

            const pctFromEvent = (clientX: number) => {
                const rect = el.getBoundingClientRect();
                return ((clientX - rect.left) / Math.max(1, rect.width)) * 100;
            };

            let active: '1' | '2' = '1';
            const onMove = (clientX: number) => setPct(active, pctFromEvent(clientX));

            el.querySelectorAll<HTMLElement>('[data-thumb]').forEach((thumb) => {
                thumb.addEventListener('pointerdown', (e) => {
                    active = (thumb.dataset.thumb as '1' | '2') || '1';
                    thumb.setPointerCapture(e.pointerId);
                    onMove(e.clientX);
                    const move = (ev: PointerEvent) => onMove(ev.clientX);
                    const up = () => {
                        thumb.releasePointerCapture(e.pointerId);
                        thumb.removeEventListener('pointermove', move);
                        thumb.removeEventListener('pointerup', up);
                    };
                    thumb.addEventListener('pointermove', move);
                    thumb.addEventListener('pointerup', up);
                });
            });

            el.addEventListener('pointerdown', (e) => {
                if ((e.target as HTMLElement).closest('[data-thumb]')) return;
                if (mode === 'range') {
                    const p1 = parseFloat(getComputedStyle(el).getPropertyValue('--p1')) || 20;
                    const p2 = parseFloat(getComputedStyle(el).getPropertyValue('--p2')) || 75;
                    const p = pctFromEvent(e.clientX);
                    active = Math.abs(p - p1) <= Math.abs(p - p2) ? '1' : '2';
                }
                onMove(e.clientX);
            });
        });
    }

    private bindTags(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-tags]').forEach((root) => {
            if (root.dataset.bound === '1') return;
            root.dataset.bound = '1';
            const name = root.dataset.name || 'tags';
            const field = root.querySelector<HTMLInputElement>('[data-tag-field]');
            if (!field) return;

            const add = (raw: string) => {
                const tag = raw.trim().replace(/,/g, '');
                if (!tag) return;
                if (root.querySelector(`[data-tag="${CSS.escape(tag)}"]`)) return;
                const chip = document.createElement('span');
                chip.className = 'form-tag accent';
                chip.dataset.tag = tag;
                chip.innerHTML = `${escapeHtml(tag)}<button type="button" class="form-tag-remove" data-tag-remove aria-label="Remove ${escapeHtml(tag)}">&times;</button><input type="hidden" name="${name}[]" value="${escapeAttr(tag)}">`;
                root.insertBefore(chip, field);
            };

            root.addEventListener('click', (e) => {
                const btn = (e.target as HTMLElement).closest<HTMLElement>('[data-tag-remove]');
                if (btn) {
                    btn.closest('[data-tag]')?.remove();
                    return;
                }
                field.focus();
            });

            field.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    add(field.value);
                    field.value = '';
                } else if (e.key === 'Backspace' && !field.value) {
                    root.querySelectorAll('[data-tag]').item(root.querySelectorAll('[data-tag]').length - 1)?.remove();
                }
            });
        });
    }

    private bindTokenPickers(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-token-picker]').forEach((root) => {
            if (root.dataset.bound === '1') return;
            root.dataset.bound = '1';
            const hidden = root.querySelector<HTMLInputElement>('[data-token-hidden]');
            const input = root.querySelector<HTMLInputElement>('[data-token-input]');
            const min = Number(root.dataset.min ?? 10);
            const max = Number(root.dataset.max ?? 10000);
            const step = Number(root.dataset.step ?? 10);

            const setValue = (value: number) => {
                const v = Math.min(max, Math.max(min, value));
                if (hidden) hidden.value = String(v);
                if (input) input.value = String(v);
                root.querySelectorAll<HTMLElement>('[data-token-chip]').forEach((chip) => {
                    chip.classList.toggle('selected', Number(chip.dataset.value) === v);
                });
            };

            root.addEventListener('click', (e) => {
                const chip = (e.target as HTMLElement).closest<HTMLElement>('[data-token-chip]');
                if (chip) setValue(Number(chip.dataset.value));
                if ((e.target as HTMLElement).closest('[data-token-dec]')) {
                    setValue(Number(input?.value || hidden?.value || min) - step);
                }
                if ((e.target as HTMLElement).closest('[data-token-inc]')) {
                    setValue(Number(input?.value || hidden?.value || min) + step);
                }
            });
            input?.addEventListener('change', () => setValue(Number(input.value)));
        });
    }

    private bindRatings(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-rating]').forEach((root) => {
            if (root.dataset.bound === '1' || root.dataset.readonly === '1') return;
            root.dataset.bound = '1';
            const input = root.querySelector<HTMLInputElement>('[data-rating-input]');
            const paint = (n: number) => {
                root.querySelectorAll<HTMLElement>('[data-rating-star]').forEach((star) => {
                    star.classList.toggle('filled', Number(star.dataset.value) <= n);
                });
                if (input) input.value = String(n);
            };
            root.addEventListener('click', (e) => {
                const star = (e.target as HTMLElement).closest<HTMLElement>('[data-rating-star]');
                if (!star) return;
                paint(Number(star.dataset.value));
            });
        });
    }

    private bindPwdStrength(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-pwd-strength]').forEach((meter) => {
            if (meter.dataset.bound === '1') return;
            meter.dataset.bound = '1';
            const forName = meter.dataset.for;
            const input = forName
                ? document.querySelector<HTMLInputElement>(`input[name="${CSS.escape(forName)}"]`)
                : meter.parentElement?.querySelector<HTMLInputElement>('input[type="password"]');
            if (!input) return;

            const labels = ['Too weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const score = (v: string) => {
                let s = 0;
                if (v.length >= 8) s++;
                if (/[A-Z]/.test(v)) s++;
                if (/\d/.test(v)) s++;
                if (/[^A-Za-z0-9]/.test(v)) s++;
                return s;
            };

            const update = () => {
                const v = input.value;
                const s = score(v);
                meter.dataset.level = String(s);
                const label = meter.querySelector('[data-pwd-label]');
                if (label) label.textContent = labels[s] ?? labels[0]!;
                meter.querySelector<HTMLElement>('[data-rule="length"]')?.classList.toggle('done', v.length >= 8);
                meter.querySelector<HTMLElement>('[data-rule="upper"]')?.classList.toggle('done', /[A-Z]/.test(v));
                meter.querySelector<HTMLElement>('[data-rule="number"]')?.classList.toggle('done', /\d/.test(v));
                meter.querySelector<HTMLElement>('[data-rule="symbol"]')?.classList.toggle('done', /[^A-Za-z0-9]/.test(v));
            };
            input.addEventListener('input', update);
            update();
        });
    }

    private bindComboboxes(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-combobox]').forEach((root) => {
            if (root.dataset.bound === '1') return;
            root.dataset.bound = '1';
            const input = root.querySelector<HTMLInputElement>('[data-combo-input]');
            const list = root.querySelector<HTMLElement>('[data-combo-list]');
            if (!input || !list) return;

            const setOpen = (open: boolean) => {
                root.dataset.open = open ? 'true' : 'false';
                list.hidden = !open;
                input.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            input.addEventListener('focus', () => setOpen(true));
            input.addEventListener('input', () => setOpen(true));
            root.addEventListener('click', (e) => {
                const item = (e.target as HTMLElement).closest<HTMLElement>('[data-combo-item]');
                if (!item) return;
                input.value = item.dataset.value || item.textContent?.trim() || '';
                root.querySelectorAll('[data-combo-item]').forEach((el) => el.classList.remove('active'));
                item.classList.add('active');
                setOpen(false);
            });
            document.addEventListener('click', (e) => {
                if (!root.contains(e.target as Node)) setOpen(false);
            });
        });
    }

    private bindAvatarUploads(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-avatar-upload]').forEach((root) => {
            if (root.dataset.bound === '1') return;
            root.dataset.bound = '1';
            const input = root.querySelector<HTMLInputElement>('[data-avatar-input]');
            const preview = root.querySelector<HTMLImageElement>('[data-avatar-preview]');
            const placeholder = root.querySelector<HTMLElement>('[data-avatar-placeholder]');
            input?.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file || !preview) return;
                const url = URL.createObjectURL(file);
                preview.src = url;
                preview.hidden = false;
                preview.removeAttribute('hidden');
                if (placeholder) {
                    placeholder.hidden = true;
                    placeholder.setAttribute('hidden', '');
                }
                root.classList.add('has-image');
            });
        });
    }

    private bindFollowButtons(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-follow-button]').forEach((btn) => {
            if (btn.dataset.bound === '1') return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                const state = btn.dataset.state || 'follow';
                const next = state === 'follow' ? 'following' : 'follow';
                btn.dataset.state = next;
                btn.classList.remove('follow-btn--follow', 'follow-btn--following', 'follow-btn--pending');
                btn.classList.add(`follow-btn--${next}`);
                btn.setAttribute('aria-pressed', next === 'following' ? 'true' : 'false');
                const label = btn.querySelector('[data-follow-label]');
                if (label) label.textContent = next === 'following' ? 'Following' : 'Follow';
                btn.dispatchEvent(new CustomEvent('follow:toggle', { bubbles: true, detail: { state: next } }));
            });
        });
    }

    private bindFilterBars(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-filter-bar]').forEach((bar) => {
            if (bar.dataset.bound === '1') return;
            bar.dataset.bound = '1';
            bar.querySelector('[data-filter-clear]')?.addEventListener('click', () => {
                bar.querySelectorAll('.pill.is-active, .pill[aria-pressed="true"], .pill--active').forEach((el) => {
                    el.classList.remove('is-active', 'pill--active');
                    el.setAttribute('aria-pressed', 'false');
                });
                bar.dispatchEvent(new CustomEvent('filter:clear', { bubbles: true }));
            });
        });
    }

    private bindSelectRows(scope: ParentNode): void {
        scope.querySelectorAll<HTMLElement>('[data-form-select-row]').forEach((row) => {
            if (row.dataset.bound === '1') return;
            row.dataset.bound = '1';
            const input = row.querySelector<HTMLInputElement>('[data-select-row-input]');
            if (!input) return;
            const sync = () => {
                const name = input.name;
                document.querySelectorAll<HTMLInputElement>(`input[data-select-row-input][name="${CSS.escape(name)}"]`).forEach((el) => {
                    el.closest('[data-form-select-row]')?.classList.toggle('selected', el.checked);
                });
            };
            input.addEventListener('change', sync);
            sync();
        });
    }
}

function escapeHtml(value: string): string {
    return value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function escapeAttr(value: string): string {
    return escapeHtml(value).replace(/"/g, '&quot;');
}
