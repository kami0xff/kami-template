/**
 * ForumManager — imageboard interactions for [data-forum].
 *
 * - Click post No. → insert >>id into nearest reply textarea
 * - Click thumbnail → expand/collapse in place (lightbox still works via data-lightbox)
 * - Hash #p123 → highlight target post
 * - File input → local preview
 * - Form submit → forum:submit CustomEvent (apps handle persistence)
 */
export class ForumManager {
    private roots: HTMLElement[] = [];

    constructor(scope: ParentNode = document) {
        this.roots = Array.from(scope.querySelectorAll<HTMLElement>('[data-forum]'));
        if (this.roots.length === 0 && scope instanceof Document) {
            // Allow standalone threads/forms outside a board shell
            this.roots = Array.from(scope.querySelectorAll<HTMLElement>('[data-forum-thread], [data-forum-form]'))
                .map((el) => el.closest<HTMLElement>('[data-forum]') ?? el);
            this.roots = [...new Set(this.roots)];
        }

        this.roots.forEach((root) => this.bind(root));
        this.highlightFromHash();
        window.addEventListener('hashchange', () => this.highlightFromHash());
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const quoteBtn = t.closest<HTMLElement>('[data-forum-quote]');
            if (quoteBtn) {
                e.preventDefault();
                const id = quoteBtn.dataset.quoteId;
                if (id) this.quote(root, id);
                return;
            }

            const thumbLink = t.closest<HTMLAnchorElement>('[data-forum-thumb]');
            if (thumbLink && !t.closest('[data-lightbox]')) {
                const img = thumbLink.querySelector('img');
                if (img && !e.metaKey && !e.ctrlKey) {
                    e.preventDefault();
                    this.toggleExpand(img);
                }
            }
        });

        root.querySelectorAll<HTMLFormElement>('[data-forum-form]').forEach((form) => {
            const file = form.querySelector<HTMLInputElement>('[data-forum-file]');
            const preview = form.querySelector<HTMLImageElement>('[data-forum-file-preview]');

            file?.addEventListener('change', () => {
                const f = file.files?.[0];
                if (!preview) return;
                if (!f || !f.type.startsWith('image/')) {
                    preview.removeAttribute('src');
                    return;
                }
                preview.src = URL.createObjectURL(f);
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const comment = form.querySelector<HTMLTextAreaElement>('[data-forum-comment-input]');
                const name = form.querySelector<HTMLInputElement>('[data-forum-name]');
                const subject = form.querySelector<HTMLInputElement>('[data-forum-subject]');
                const options = form.querySelector<HTMLInputElement>('[data-forum-options]');

                root.dispatchEvent(new CustomEvent('forum:submit', {
                    bubbles: true,
                    detail: {
                        mode: form.dataset.forumFormMode ?? 'reply',
                        threadId: form.dataset.threadId ?? null,
                        name: name?.value?.trim() || 'Anonymous',
                        subject: subject?.value?.trim() || null,
                        options: options?.value?.trim() || null,
                        comment: comment?.value ?? '',
                        file: file?.files?.[0] ?? null,
                        form,
                    },
                }));
            });
        });

        this.formatGreentext(root);
    }

    private quote(root: HTMLElement, id: string): void {
        const form =
            root.querySelector<HTMLFormElement>('[data-forum-form][data-forum-form-mode="reply"]')
            ?? root.querySelector<HTMLFormElement>('[data-forum-form]');
        const input = form?.querySelector<HTMLTextAreaElement>('[data-forum-comment-input]');
        if (!input) {
            window.toasts?.info?.(`Quote >>${id}`);
            return;
        }

        const snip = `>>${id}\n`;
        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? input.value.length;
        const before = input.value.slice(0, start);
        const after = input.value.slice(end);
        const needsNl = before.length > 0 && !before.endsWith('\n');
        input.value = `${before}${needsNl ? '\n' : ''}${snip}${after}`;
        input.focus();
        const caret = (needsNl ? before.length + 1 : before.length) + snip.length;
        input.setSelectionRange(caret, caret);
        form?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    private toggleExpand(img: HTMLImageElement): void {
        const full = img.dataset.full;
        if (!full) return;

        if (img.classList.contains('is-expanded')) {
            const thumb = img.getAttribute('src');
            // restore original thumb if we swapped
            if (img.dataset.thumbSrc) img.src = img.dataset.thumbSrc;
            img.classList.remove('is-expanded');
            void thumb;
            return;
        }

        img.dataset.thumbSrc = img.currentSrc || img.src;
        if (img.src !== full) img.src = full;
        img.classList.add('is-expanded');
    }

    private highlightFromHash(): void {
        document.querySelectorAll('.forum-post.is-highlight').forEach((el) => {
            el.classList.remove('is-highlight');
        });

        const hash = window.location.hash;
        if (!/^#p\d+/.test(hash)) return;
        const post = document.querySelector<HTMLElement>(hash);
        if (!post?.hasAttribute('data-forum-post')) return;
        post.classList.add('is-highlight');
        post.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /** Turn leading ">" lines into greentext when authors used plain text slots. */
    private formatGreentext(root: HTMLElement): void {
        root.querySelectorAll<HTMLElement>('[data-forum-comment]').forEach((el) => {
            if (el.dataset.forumFormatted === '1') return;
            // Only auto-format pure text nodes / simple paragraphs without existing greentext markup
            if (el.querySelector('.forum-greentext, .forum-quote-link')) {
                el.dataset.forumFormatted = '1';
                return;
            }

            const raw = el.textContent ?? '';
            if (!raw.includes('>') && !raw.includes('>>')) {
                el.dataset.forumFormatted = '1';
                return;
            }

            const html = raw
                .split('\n')
                .map((line) => {
                    const esc = escapeHtml(line);
                    if (/^&gt;&gt;\d+/.test(esc)) {
                        return esc.replace(/^&gt;&gt;(\d+)/, '<a class="forum-quote-link" href="#p$1" data-forum-quote-link>&gt;&gt;$1</a>');
                    }
                    if (/^&gt;/.test(esc)) {
                        return `<span class="forum-greentext">${esc}</span>`;
                    }
                    return esc;
                })
                .join('<br>');

            el.innerHTML = html;
            el.dataset.forumFormatted = '1';
        });
    }
}

function escapeHtml(s: string): string {
    return s
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
