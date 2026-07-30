/**
 * Shared glowing orb markup for AI thinking / image / video wait states.
 */

export type AiThinkingMode = 'thinking' | 'image' | 'video';

const COPY: Record<AiThinkingMode, { label: string; hint: string; role: string }> = {
    thinking: { label: 'Thinking…', hint: 'She’s composing a reply', role: 'Thinking' },
    image: { label: 'Creating a photo…', hint: 'Generating an image for you', role: 'Creating' },
    video: { label: 'Filming a clip…', hint: 'Rendering a short video', role: 'Filming' },
};

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

export function detectThinkingMode(prompt: string): AiThinkingMode {
    const p = prompt.toLowerCase();
    if (/\b(video|clip|reel|animate|animation|film|movie)\b/.test(p)) return 'video';
    if (/\b(image|photo|picture|selfie|pic|draw|render|generate)\b/.test(p)) return 'image';
    return 'thinking';
}

export function orbHtml(mode: AiThinkingMode = 'thinking', size: 'sm' | 'md' | 'lg' | 'xl' = 'sm'): string {
    const copy = COPY[mode];
    return `<div class="ai-orb ai-orb--${mode} ai-orb--${size}" data-ai-orb data-mode="${mode}" role="status" aria-live="polite" aria-label="${esc(copy.label)}">
        <div class="ai-orb-stage" aria-hidden="true">
            <span class="ai-orb-glow"></span>
            <span class="ai-orb-ring"></span>
            <span class="ai-orb-sparks"><i></i><i></i><i></i></span>
            <span class="ai-orb-core"></span>
        </div>
        <div class="ai-orb-label">
            <strong>${esc(copy.label)}</strong>
            <span>${esc(copy.hint)}</span>
        </div>
    </div>`;
}

/** AI workspace thread row */
export function thinkingMessageHtml(mode: AiThinkingMode = 'thinking'): string {
    const copy = COPY[mode];
    return `<article class="ai-msg ai-msg--assistant ai-thinking ai-thinking--${mode}" data-ai-typing data-ai-thinking data-mode="${mode}">
        <div class="ai-msg-role">${esc(copy.role)}</div>
        <div class="ai-thinking-card">${orbHtml(mode, 'sm')}</div>
    </article>`;
}

/** Chat-dock girlfriend message row */
export function chatThinkingHtml(mode: AiThinkingMode = 'thinking', avatarHtml = ''): string {
    return `<div class="chat-msg chat-msg--them chat-msg--group-start chat-msg--group-end chat-msg--thinking" data-chat-thinking data-mode="${mode}">
        ${avatarHtml}
        <div class="chat-msg-body">
            <div class="ai-thinking ai-thinking--in-chat ai-thinking--${mode}">
                <div class="ai-thinking-card">${orbHtml(mode, 'sm')}</div>
            </div>
            <div class="chat-msg-meta" hidden></div>
        </div>
    </div>`;
}
