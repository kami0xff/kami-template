/**
 * CreatorSectionsManager — edit mode for [data-creator-sections].
 *
 * Creators can toggle edit, rename, reorder, remove, and add
 * socials / ad / links / text modules. Dispatches:
 *   creator-sections:change  { sections }
 *   creator-sections:edit    { editing }
 *
 * Persist by listening to those events and saving JSON to your API.
 */

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

const TEMPLATES: Record<string, { title: string; body: string }> = {
    socials: {
        title: 'Socials',
        body: `<div class="creator-socials">
            <a class="creator-social" href="#" target="_blank" rel="noopener noreferrer" title="X">
                <span class="creator-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M18.2 3H21l-6.6 7.5L22 21h-6.2l-4.3-5.5L6.2 21H3.4l7-8L2 3h6.3l3.9 5.1L18.2 3zm-1.1 16.2h1.7L7 4.7H5.2l11.9 14.5z" fill="currentColor"/></svg></span>
                <span class="creator-social-label">X</span>
            </a>
            <a class="creator-social" href="#" target="_blank" rel="noopener noreferrer" title="Instagram">
                <span class="creator-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/></svg></span>
                <span class="creator-social-label">Instagram</span>
            </a>
            <a class="creator-social" href="#" target="_blank" rel="noopener noreferrer" title="TikTok">
                <span class="creator-social-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 4c.4 2.4 1.8 4 4 4.4V11c-1.5-.1-2.9-.6-4-1.4V15a5 5 0 1 1-5-5c.3 0 .7 0 1 .1V13a2.5 2.5 0 1 0 2.5 2.5V4h1.5z" fill="currentColor"/></svg></span>
                <span class="creator-social-label">TikTok</span>
            </a>
        </div>`,
    },
    ad: {
        title: 'Promo',
        body: `<div class="creator-ad">
            <a class="creator-ad-media" href="#" target="_blank" rel="noopener sponsored">
                <span class="creator-ad-badge">Ad</span>
                <img src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&h=450&fit=crop" alt="Promo" loading="lazy">
            </a>
            <div class="creator-ad-meta">
                <p class="creator-ad-title">Your sponsor goes here</p>
                <p class="creator-ad-text">Swap this image and link for a brand deal or affiliate.</p>
                <a class="btn btn-muted btn-sm creator-ad-cta" href="#">Learn more</a>
            </div>
        </div>`,
    },
    links: {
        title: 'Links',
        body: `<div class="creator-links">
            <a class="creator-link" href="#" target="_blank" rel="noopener noreferrer">Wishlist</a>
            <a class="creator-link" href="#" target="_blank" rel="noopener noreferrer">Fan club</a>
            <a class="creator-link" href="#" target="_blank" rel="noopener noreferrer">Booking / calendar</a>
        </div>`,
    },
    text: {
        title: 'About',
        body: `<p class="creator-text" data-creator-view-only>Tell fans what you’re about — schedule, kinks, collab info…</p>
        <textarea class="creator-text-input" data-creator-edit-only data-creator-text-input rows="3">Tell fans what you’re about — schedule, kinks, collab info…</textarea>`,
    },
};

export class CreatorSectionsManager {
    private handleDocPointer: (e: PointerEvent) => void;

    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-creator-sections]').forEach((root) => {
            this.bind(root);
            this.syncToolbar(root);
        });

        this.handleDocPointer = (e) => {
            const t = e.target as Node;
            document.querySelectorAll<HTMLElement>('.creator-add-menu.is-open').forEach((menu) => {
                if (!menu.contains(t)) menu.classList.remove('is-open');
            });
        };
        document.addEventListener('pointerdown', this.handleDocPointer);
    }

    destroy(): void {
        document.removeEventListener('pointerdown', this.handleDocPointer);
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const toggle = t.closest<HTMLElement>('[data-creator-toggle-edit]');
            if (toggle && root.contains(toggle)) {
                e.preventDefault();
                this.setEditing(root, !root.classList.contains('creator-sections--editing'));
                return;
            }

            const addToggle = t.closest<HTMLElement>('[data-creator-add-toggle]');
            if (addToggle && root.contains(addToggle)) {
                e.preventDefault();
                addToggle.closest('.creator-add-menu')?.classList.toggle('is-open');
                return;
            }

            const addType = t.closest<HTMLElement>('[data-creator-add]');
            if (addType && root.contains(addType)) {
                e.preventDefault();
                this.addSection(root, addType.dataset.creatorAdd ?? 'text');
                addType.closest('.creator-add-menu')?.classList.remove('is-open');
                return;
            }

            const remove = t.closest<HTMLElement>('[data-creator-remove]');
            if (remove && root.contains(remove)) {
                e.preventDefault();
                remove.closest('[data-creator-section]')?.remove();
                this.emitChange(root);
                return;
            }

            const move = t.closest<HTMLElement>('[data-creator-move]');
            if (move && root.contains(move)) {
                e.preventDefault();
                this.moveSection(move.closest('[data-creator-section]')!, move.dataset.creatorMove === 'up' ? -1 : 1);
                this.emitChange(root);
            }
        });

        root.addEventListener('input', (e) => {
            const t = e.target as HTMLElement;
            if (t.matches('[data-creator-section-title-input]')) {
                const section = t.closest('[data-creator-section]');
                const view = section?.querySelector<HTMLElement>('[data-creator-section-title]');
                if (view && t instanceof HTMLInputElement) view.textContent = t.value;
                this.emitChange(root);
            }
            if (t.matches('[data-creator-text-input]') && t instanceof HTMLTextAreaElement) {
                const view = t.parentElement?.querySelector<HTMLElement>('[data-creator-view-only]');
                if (view) view.textContent = t.value;
                this.emitChange(root);
            }
        });
    }

    private setEditing(root: HTMLElement, editing: boolean): void {
        root.classList.toggle('creator-sections--editing', editing);
        this.syncToolbar(root);
        const btn = root.querySelector<HTMLElement>('[data-creator-toggle-edit]');
        if (btn) btn.textContent = editing ? 'Done' : 'Edit sections';
        root.dispatchEvent(new CustomEvent('creator-sections:edit', {
            bubbles: true,
            detail: { editing },
        }));
    }

    private syncToolbar(root: HTMLElement): void {
        const editing = root.classList.contains('creator-sections--editing');
        const addMenu = root.querySelector<HTMLElement>('[data-creator-add-menu]');
        if (addMenu) addMenu.hidden = !editing;
        if (!editing) addMenu?.classList.remove('is-open');
    }

    private addSection(root: HTMLElement, type: string): void {
        const tpl = TEMPLATES[type] ?? TEMPLATES.text;
        const id = `section-${Date.now().toString(36)}`;
        const html = `<section class="creator-section" data-creator-section data-creator-section-id="${id}" data-creator-section-type="${esc(type)}">
            <header class="creator-section-head">
                <h3 class="creator-section-title" data-creator-view-only data-creator-section-title>${esc(tpl.title)}</h3>
                <input class="creator-section-title-input" data-creator-edit-only data-creator-section-title-input type="text" value="${esc(tpl.title)}" aria-label="Section title">
                <div class="creator-section-actions" data-creator-edit-only>
                    <button type="button" class="creator-section-action" data-creator-move="up" title="Move up" aria-label="Move up">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                    </button>
                    <button type="button" class="creator-section-action" data-creator-move="down" title="Move down" aria-label="Move down">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
                    </button>
                    <button type="button" class="creator-section-action creator-section-action--danger" data-creator-remove title="Remove" aria-label="Remove section">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>
                    </button>
                </div>
            </header>
            <div class="creator-section-body">${tpl.body}</div>
        </section>`;

        root.querySelector('[data-creator-sections-list]')?.insertAdjacentHTML('beforeend', html);
        this.emitChange(root);
    }

    private moveSection(section: HTMLElement, dir: -1 | 1): void {
        const list = section.parentElement;
        if (!list) return;
        if (dir < 0) {
            const prev = section.previousElementSibling;
            if (prev) list.insertBefore(section, prev);
        } else {
            const next = section.nextElementSibling;
            if (next) list.insertBefore(next, section);
        }
    }

    private emitChange(root: HTMLElement): void {
        const sections = [...root.querySelectorAll<HTMLElement>('[data-creator-section]')].map((el) => ({
            id: el.dataset.creatorSectionId ?? '',
            type: el.dataset.creatorSectionType ?? 'custom',
            title: el.querySelector<HTMLElement>('[data-creator-section-title]')?.textContent?.trim() ?? '',
        }));
        root.dispatchEvent(new CustomEvent('creator-sections:change', {
            bubbles: true,
            detail: { sections },
        }));
    }
}
