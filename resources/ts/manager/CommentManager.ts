/**
 * CommentManager — votes + one-level reply composers for [data-comments].
 *
 * Markup:
 *   [data-comments]
 *     [data-comment-composer]  (top-level)
 *     [data-comments-list]
 *       [data-comment]
 *         [data-comment-vote] / [data-comment-upvote] / [data-comment-downvote]
 *         [data-comment-reply] → toggles [data-comment-reply-slot]
 *         [data-comment-replies]
 *
 * Demo-only: posts append client-side; wire submit to your API later.
 */

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatScore(n: number): string {
    return String(n);
}

export class CommentManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-comments]').forEach((root) => this.bind(root));
    }

    private bind(root: HTMLElement): void {
        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;

            const up = t.closest<HTMLElement>('[data-comment-upvote]');
            if (up) {
                e.preventDefault();
                const rail = up.closest<HTMLElement>('[data-comment-vote]');
                if (rail) this.vote(rail, 1);
                return;
            }

            const down = t.closest<HTMLElement>('[data-comment-downvote]');
            if (down) {
                e.preventDefault();
                const rail = down.closest<HTMLElement>('[data-comment-vote]');
                if (rail) this.vote(rail, -1);
                return;
            }

            const replyBtn = t.closest<HTMLElement>('[data-comment-reply]');
            if (replyBtn) {
                e.preventDefault();
                const comment = replyBtn.closest<HTMLElement>('[data-comment]');
                if (comment) this.toggleReply(comment);
                return;
            }

            const cancel = t.closest<HTMLElement>('[data-comment-cancel-reply]');
            if (cancel) {
                e.preventDefault();
                const slot = cancel.closest<HTMLElement>('[data-comment-reply-slot]');
                if (slot) slot.hidden = true;
            }
        });

        root.addEventListener('submit', (e) => {
            const form = (e.target as HTMLElement).closest<HTMLFormElement>('[data-comment-composer]');
            if (!form || !root.contains(form)) return;
            e.preventDefault();
            this.submit(root, form);
        });
    }

    private vote(rail: HTMLElement, next: 1 | -1): void {
        const scoreEl = rail.querySelector<HTMLElement>('[data-comment-score]');
        const upBtn = rail.querySelector<HTMLElement>('[data-comment-upvote]');
        const downBtn = rail.querySelector<HTMLElement>('[data-comment-downvote]');
        if (!scoreEl || !upBtn || !downBtn) return;

        const score = Number.parseInt(rail.dataset.score ?? scoreEl.textContent ?? '0', 10) || 0;
        const current = Number.parseInt(rail.dataset.value ?? '0', 10) || 0;

        let value = 0;
        let delta = 0;
        if (current === next) {
            // Toggle off
            value = 0;
            delta = -next;
        } else if (current !== 0) {
            // Switch side: undo old then apply new
            value = next;
            delta = next - current;
        } else {
            value = next;
            delta = next;
        }

        const newScore = score + delta;
        rail.dataset.score = String(newScore);
        rail.dataset.value = String(value);
        scoreEl.textContent = formatScore(newScore);
        if (value > 0) scoreEl.dataset.tone = 'up';
        else if (value < 0) scoreEl.dataset.tone = 'down';
        else delete scoreEl.dataset.tone;

        upBtn.setAttribute('aria-pressed', value === 1 ? 'true' : 'false');
        downBtn.setAttribute('aria-pressed', value === -1 ? 'true' : 'false');
    }

    private toggleReply(comment: HTMLElement): void {
        const slot = comment.querySelector<HTMLElement>(':scope > .comment-main > [data-comment-reply-slot]');
        if (!slot) return;

        const opening = slot.hidden;
        // Close other reply composers in this thread
        const root = comment.closest('[data-comments]');
        root?.querySelectorAll<HTMLElement>('[data-comment-reply-slot]').forEach((el) => {
            el.hidden = true;
        });
        slot.hidden = !opening;

        if (opening) {
            queueMicrotask(() => {
                slot.querySelector<HTMLTextAreaElement>('[data-comment-input]')?.focus();
            });
        }
    }

    private submit(root: HTMLElement, form: HTMLFormElement): void {
        const input = form.querySelector<HTMLTextAreaElement>('[data-comment-input]');
        const text = input?.value.trim() ?? '';
        if (!text) return;

        const replyTo = form.dataset.commentReplyTo;
        const id = `c-${Date.now().toString(36)}`;
        const html = this.renderComment({
            id,
            author: 'You',
            avatar: 'https://i.pravatar.cc/64?img=5',
            time: 'Just now',
            body: text,
            score: 1,
            vote: 1,
            replyable: !replyTo,
        });

        if (replyTo) {
            const parent = root.querySelector<HTMLElement>(`[data-comment-id="${replyTo}"]`);
            let replies = parent?.querySelector<HTMLElement>(':scope > .comment-main > [data-comment-replies]');
            if (parent && !replies) {
                replies = document.createElement('div');
                replies.className = 'comment-replies';
                replies.dataset.commentReplies = '';
                parent.querySelector('.comment-main')?.appendChild(replies);
            }
            replies?.insertAdjacentHTML('beforeend', html);
            const slot = form.closest<HTMLElement>('[data-comment-reply-slot]');
            if (slot) slot.hidden = true;
        } else {
            const list = root.querySelector('[data-comments-list]');
            list?.insertAdjacentHTML('afterbegin', html);
        }

        if (input) input.value = '';
        this.bumpCount(root, 1);
    }

    private bumpCount(root: HTMLElement, delta: number): void {
        const el = root.querySelector<HTMLElement>('[data-comments-count]');
        if (!el) return;
        const n = Number.parseInt(el.textContent ?? '0', 10);
        if (Number.isFinite(n)) el.textContent = String(n + delta);
    }

    private renderComment(opts: {
        id: string;
        author: string;
        avatar: string;
        time: string;
        body: string;
        score: number;
        vote: number;
        replyable: boolean;
    }): string {
        const replyBtn = opts.replyable
            ? `<button type="button" class="comment-action" data-comment-reply aria-controls="reply-composer-${esc(opts.id)}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M9 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5" />
                        <path d="m15 19 5-5-5-5M20 14H10" />
                    </svg>
                    Reply
               </button>
               <div class="comment-reply-composer" id="reply-composer-${esc(opts.id)}" data-comment-reply-slot hidden>
                 <form class="comments-composer" data-comment-composer data-comment-reply-to="${esc(opts.id)}">
                   <div class="comments-composer-row">
                     <span class="user-avatar-wrap user-avatar-wrap--sm"><span class="user-avatar"><img class="avatar-img" src="${esc(opts.avatar)}" alt=""></span></span>
                     <textarea class="comments-composer-input" name="body" rows="3" placeholder="Write a reply…" data-comment-input required></textarea>
                   </div>
                   <div class="comments-composer-actions">
                     <button type="button" class="btn btn-muted btn-sm" data-comment-cancel-reply>Cancel</button>
                     <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                   </div>
                 </form>
               </div>`
            : '';

        const tone = opts.vote > 0 ? 'up' : opts.vote < 0 ? 'down' : '';

        return `<article class="comment${opts.replyable ? '' : ' comment--reply'}" data-comment data-comment-id="${esc(opts.id)}" id="comment-${esc(opts.id)}">
            <div class="comment-vote" data-comment-vote data-score="${opts.score}" data-value="${opts.vote}">
                <button type="button" class="comment-vote-btn comment-vote-btn--up" data-comment-upvote aria-label="Upvote" aria-pressed="${opts.vote === 1}" title="Upvote">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                </button>
                <span class="comment-score" data-comment-score${tone ? ` data-tone="${tone}"` : ''}>${opts.score}</span>
                <button type="button" class="comment-vote-btn comment-vote-btn--down" data-comment-downvote aria-label="Downvote" aria-pressed="${opts.vote === -1}" title="Downvote">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>
                </button>
            </div>
            <div class="comment-main">
                <header class="comment-head">
                    <span class="user-avatar-wrap user-avatar-wrap--xs"><span class="user-avatar"><img class="avatar-img" src="${esc(opts.avatar)}" alt=""></span></span>
                    <a href="#" class="comment-author">${esc(opts.author)}</a>
                    <time class="comment-meta">${esc(opts.time)}</time>
                </header>
                <p class="comment-body">${esc(opts.body)}</p>
                <div class="comment-actions">
                    ${replyBtn}
                    <button type="button" class="comment-action" data-comment-share>Share</button>
                </div>
            </div>
        </article>`;
    }
}
