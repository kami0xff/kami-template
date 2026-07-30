/**
 * AiChatManager — prompt UI for configured models.
 *
 * Mount: [data-ai-chat]
 * Events:
 *   ai-chat:send     { prompt, model, conversationId, mode }
 *   ai-chat:new      { conversationId }
 *
 * Thinking orb:
 *   aiChat.showThinking(root|selector, mode?)
 *   aiChat.hideThinking(root|selector)
 */

import {
    detectThinkingMode,
    thinkingMessageHtml,
    type AiThinkingMode,
} from '../ai/thinking-orb';

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function messageHtml(role: string, content: string, images: string[] = []): string {
    const media =
        images.length > 0
            ? `<div class="ai-msg-media">${images
                  .map((src) => `<img src="${esc(src)}" alt="" loading="lazy">`)
                  .join('')}</div>`
            : '';
    const roleLabel = role === 'user' ? 'You' : role === 'system' ? 'System' : 'Model';
    const bubble = content ? `<div class="ai-msg-bubble">${esc(content)}</div>` : '';
    return `<article class="ai-msg ai-msg--${esc(role)}" data-ai-msg data-role="${esc(role)}">
        <div class="ai-msg-role">${roleLabel}</div>
        ${media}${bubble}
    </article>`;
}

function resolveRoot(target?: HTMLElement | string | null): HTMLElement | null {
    if (!target) return document.querySelector<HTMLElement>('[data-ai-chat]');
    if (typeof target === 'string') return document.querySelector<HTMLElement>(target);
    return target;
}

export class AiChatManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-ai-chat]').forEach((root) => this.bind(root));
    }

    showThinking(target?: HTMLElement | string | null, mode: AiThinkingMode = 'thinking'): void {
        const root = resolveRoot(target);
        const thread = root?.querySelector<HTMLElement>('[data-ai-thread]');
        if (!thread) return;
        this.hideThinking(root);
        thread.insertAdjacentHTML('beforeend', thinkingMessageHtml(mode));
        thread.scrollTop = thread.scrollHeight;
    }

    hideThinking(target?: HTMLElement | string | null): void {
        const root = resolveRoot(target);
        root?.querySelectorAll('[data-ai-thinking], [data-ai-typing]').forEach((el) => el.remove());
    }

    private bind(root: HTMLElement): void {
        const form = root.querySelector<HTMLFormElement>('[data-ai-composer]');
        const input = root.querySelector<HTMLTextAreaElement>('[data-ai-input]');
        const thread = root.querySelector<HTMLElement>('[data-ai-thread]');
        if (!form || !input || !thread) return;

        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const prompt = input.value.trim();
            if (!prompt) return;

            const model = root.querySelector<HTMLSelectElement>('[data-ai-model]')?.value ?? null;
            const conversationId = root.dataset.conversationId ?? 'default';
            const mode = detectThinkingMode(prompt);

            thread.querySelector('.ai-chat-empty')?.remove();
            thread.insertAdjacentHTML('beforeend', messageHtml('user', prompt));
            input.value = '';
            input.style.height = 'auto';
            thread.scrollTop = thread.scrollHeight;

            root.dispatchEvent(
                new CustomEvent('ai-chat:send', {
                    bubbles: true,
                    detail: { prompt, model, conversationId, mode },
                }),
            );

            this.demoReply(root, thread, prompt, model, mode);
        });

        root.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;
            const neu = t.closest<HTMLElement>('[data-ai-new-chat]');
            if (neu && root.contains(neu)) {
                const id = `chat-${Date.now()}`;
                root.dataset.conversationId = id;
                thread.innerHTML = `<div class="ai-chat-empty"><strong>New conversation</strong><p>Prompt your configured model. Attach a reference or just describe the shot.</p></div>`;
                root.dispatchEvent(new CustomEvent('ai-chat:new', { bubbles: true, detail: { conversationId: id } }));
                return;
            }

            const conv = t.closest<HTMLElement>('[data-ai-conv]');
            if (conv && root.contains(conv)) {
                root.querySelectorAll('.ai-chat-conv.is-active').forEach((el) => el.classList.remove('is-active'));
                conv.classList.add('is-active');
                root.dataset.conversationId = conv.dataset.aiConv ?? 'default';
            }
        });

        root.querySelector<HTMLSelectElement>('[data-ai-model]')?.addEventListener('change', (e) => {
            const sel = e.target as HTMLSelectElement;
            const label = root.querySelector<HTMLElement>('[data-ai-active-model-label]');
            if (label) label.textContent = sel.options[sel.selectedIndex]?.text ?? sel.value;
        });
    }

    private demoReply(
        root: HTMLElement,
        thread: HTMLElement,
        prompt: string,
        model: string | null,
        mode: AiThinkingMode,
    ): void {
        if (root.dataset.live === 'true') return;

        this.showThinking(root, mode);

        const delay = mode === 'video' ? 2200 : mode === 'image' ? 1600 : 1100;
        window.setTimeout(() => {
            this.hideThinking(root);
            const demoImage =
                mode === 'thinking'
                    ? []
                    : ['https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=512&h=512&fit=crop'];
            const text =
                mode === 'video'
                    ? `Here’s a short clip idea rendered with ${model ?? 'your config'}.\n\n(Demo — wire ai-chat:send for real video.)`
                    : mode === 'image'
                      ? `Here’s a photo from “${prompt}”.\n\n(Demo — wire ai-chat:send for real images.)`
                      : `Hey… I was thinking about that 💭\n\n“${prompt}”\n\n(Demo reply — wire ai-chat:send to your girlfriend model API.)`;

            thread.insertAdjacentHTML('beforeend', messageHtml('assistant', text, demoImage));
            thread.scrollTop = thread.scrollHeight;
        }, delay);
    }
}
