/**
 * ChatDockManager — single bottom dock for people + AI model chats.
 *
 * Mount: <div data-chat-dock> with [data-chat-windows] + [data-chat-heads].
 *
 *   chats.open({ id, name, avatar, status, messages })
 *   chats.openAi(configId | AiSavedConfig)
 *   chats.minimize(id) / expand(id) / close(id) / toggle(id)
 *   chats.setThinking(id, mode | false)
 *
 * window.aiDock is a thin alias of this same dock.
 */

import { AiConfigStore, type AiSavedConfig } from '../ai/config-store';
import { chatThinkingHtml, detectThinkingMode, type AiThinkingMode } from '../ai/thinking-orb';
import { VoiceNoteRecorder, formatVoiceDuration, waveBarsHtml } from '../chat/voice-note';

export type ChatStatus = 'online' | 'live' | 'offline';
export type ChatKind = 'person' | 'ai';

export interface ChatMessage {
    id?: string;
    from: 'me' | 'them';
    text?: string;
    image?: string;
    /** Object URL or remote URL for a voice note */
    audio?: string;
    audioDuration?: number;
    /** Normalized amplitude peaks for waveform UI */
    audioPeaks?: number[];
    time?: string;
}

export interface ChatAiMeta {
    checkpoint: { id?: string | null; name: string; meta?: string | null };
    loras: { id?: string; name: string; weight: number }[];
    settings: Record<string, unknown>;
}

export interface ChatOpenOptions {
    id: string;
    name: string;
    avatar?: string;
    status?: ChatStatus;
    subtitle?: string;
    messages?: ChatMessage[];
    unread?: number;
    /** person (default) or ai model / girlfriend config */
    kind?: ChatKind;
    ai?: ChatAiMeta;
}

type ChatState = 'open' | 'minimized';

interface ChatEntry {
    opts: ChatOpenOptions;
    state: ChatState;
    windowEl: HTMLElement;
    headEl: HTMLElement;
    voice?: VoiceNoteRecorder;
}

const MAX_OPEN_CAP = 5;
const MAX_HEADS = 10;
const ICONS = {
    more: `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/></svg>`,
    minimize: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/></svg>`,
    close: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>`,
    send: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h12M13 6l6 6-6 6"/></svg>`,
    gift: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="10" width="18" height="11" rx="1.5"/><path d="M12 10V21M3 14h18M12 10c-2.5 0-4-1.5-4-3.2S9.2 4 12 6.2C14.8 4 16 5 16 6.8S14.5 10 12 10z"/></svg>`,
    camera: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 8h3l2-2h6l2 2h3v11H4V8z"/><circle cx="12" cy="13" r="3.2"/></svg>`,
    smile: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 10h.01M15 10h.01"/></svg>`,
    mic: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3a3 3 0 0 0-3 3v6a3 3 0 0 0 6 0V6a3 3 0 0 0-3-3z"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg>`,
    stop: `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>`,
    play: `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>`,
    pause: `<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>`,
    trashSm: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>`,
    user: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="3.2"/><path d="M5.5 19c1.5-3.2 3.8-4.8 6.5-4.8S17 15.8 18.5 19"/></svg>`,
    bookmark: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4h10v16l-5-3.2L7 20V4z"/></svg>`,
    translate: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="3.5" width="10" height="10" rx="1.5"/><path d="M6.2 10.2 8.5 5.8l2.3 4.4M6.9 8.8h3.2"/><path d="M13 13.5h7.5M16.8 13.5c0 3.2-1.6 5.8-3.5 7"/><path d="M17.2 13.5c0 3.2 1.6 5.8 3.5 7"/><path d="M13.5 16.8H20"/></svg>`,
    bell: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9a6 6 0 1 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>`,
    requests: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H14a2.5 2.5 0 0 1 2.5 2.5v6A2.5 2.5 0 0 1 14 15H9l-4 3v-3.5A2.5 2.5 0 0 1 4 12.5v-6z"/><path d="M7 7.5 15.5 16"/><path d="M15.5 7.5 7 16"/></svg>`,
    warning: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4.5 21 19H3L12 4.5z"/><path d="M12 10v4.5M12 17h.01"/></svg>`,
    block: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M7.2 7.2l9.6 9.6"/></svg>`,
    trash: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>`,
    studio: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4 7 17M17 7l1.4-1.4"/></svg>`,
};

function esc(value: string): string {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function initials(name: string): string {
    return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('');
}

function nowTime(): string {
    const d = new Date();
    return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
}

const voiceProgressRaf = new WeakMap<Element, number>();

function paintVoiceProgress(wrap: Element, progress: number, audio?: HTMLAudioElement): void {
    const p = Math.min(1, Math.max(0, progress));
    const bars = wrap.querySelector<HTMLElement>('[data-chat-voice-bars]');
    if (bars) {
        bars.style.setProperty('--voice-progress', String(p));
        const nodes = bars.querySelectorAll('i');
        nodes.forEach((bar, i) => {
            bar.classList.toggle('is-played', (i + 0.5) / nodes.length <= p);
        });
    }
    const time = wrap.querySelector('[data-chat-voice-time]');
    if (time && audio) {
        const total =
            Number.isFinite(audio.duration) && audio.duration > 0
                ? audio.duration
                : Number((audio as HTMLAudioElement).dataset.duration) || 0;
        const remaining = Math.max(0, Math.ceil(total - audio.currentTime));
        time.textContent = formatVoiceDuration(audio.paused && p <= 0 ? total : remaining);
    }
}

function stopVoiceProgress(wrap: Element | null | undefined): void {
    if (!wrap) return;
    const raf = voiceProgressRaf.get(wrap);
    if (raf) cancelAnimationFrame(raf);
    voiceProgressRaf.delete(wrap);
}

function startVoiceProgress(wrap: Element, audio: HTMLAudioElement, playBtn: HTMLElement): void {
    stopVoiceProgress(wrap);
    const tick = () => {
        if (audio.paused) return;
        const dur =
            Number.isFinite(audio.duration) && audio.duration > 0
                ? audio.duration
                : Number(audio.dataset.duration) || 1;
        paintVoiceProgress(wrap, audio.currentTime / dur, audio);
        voiceProgressRaf.set(wrap, requestAnimationFrame(tick));
    };
    audio.onended = () => {
        playBtn.innerHTML = ICONS.play;
        paintVoiceProgress(wrap, 1, audio);
        stopVoiceProgress(wrap);
        setTimeout(() => {
            paintVoiceProgress(wrap, 0, audio);
            const total = Number(audio.dataset.duration) || Math.round(audio.duration || 0);
            const time = wrap.querySelector('[data-chat-voice-time]');
            if (time) time.textContent = formatVoiceDuration(total);
        }, 280);
    };
    tick();
}

export class ChatDockManager {
    private root: HTMLElement;
    private windowsHost: HTMLElement;
    private headsHost: HTMLElement;
    private moreBtn: HTMLButtonElement | null;
    private headsMenu: HTMLElement | null;
    private chats = new Map<string, ChatEntry>();
    private openOrder: string[] = [];
    /** Minimized chats, oldest → newest (newest sits at the bottom of the stack). */
    private headOrder: string[] = [];
    private handleDocClick: (e: MouseEvent) => void;
    private handleKey: (e: KeyboardEvent) => void;
    private handleResize: () => void;

    constructor(root?: HTMLElement | null) {
        const el = root ?? document.querySelector<HTMLElement>('[data-chat-dock]');
        if (!el) {
            const dock = document.createElement('div');
            dock.className = 'chat-dock';
            dock.dataset.chatDock = '';
            dock.id = 'chat-dock';
            dock.innerHTML = `
                <div class="chat-dock-windows" data-chat-windows></div>
                <div class="chat-dock-heads" data-chat-heads>
                    <button type="button" class="chat-pill chat-pill--more" data-chat-heads-more aria-label="More chats" aria-haspopup="menu" aria-expanded="false" hidden>
                        <span class="chat-pill-more-label" aria-hidden="true">…</span>
                        <span class="chat-pill-badge" data-chat-heads-more-badge hidden>0</span>
                    </button>
                    <div class="chat-heads-menu" data-chat-heads-menu hidden role="menu" aria-label="All chats"></div>
                </div>`;
            document.body.appendChild(dock);
            this.root = dock;
        } else {
            this.root = el;
        }

        this.windowsHost = this.root.querySelector('[data-chat-windows]') ?? this.root;
        this.headsHost = this.root.querySelector('[data-chat-heads]') ?? this.root;
        this.moreBtn = this.headsHost.querySelector('[data-chat-heads-more]');
        this.headsMenu = this.headsHost.querySelector('[data-chat-heads-menu]');

        // Keep the more control as the last DOM child (top of column-reverse stack).
        if (this.moreBtn) this.headsHost.appendChild(this.moreBtn);
        if (this.headsMenu) this.headsHost.appendChild(this.headsMenu);

        this.handleDocClick = (e) => {
            const t = e.target as HTMLElement;
            if (!t.closest('.chat-window-menu') && !t.closest('[data-chat-more]')) {
                this.closeAllMenus();
            }
            if (!t.closest('[data-chat-heads-more]') && !t.closest('[data-chat-heads-menu]')) {
                this.closeHeadsMenu();
            }
        };
        this.handleKey = (e) => {
            if (e.key === 'Escape') {
                this.closeAllMenus();
                this.closeHeadsMenu();
            }
        };
        this.handleResize = () => {
            // Re-fit open windows + head stack when the viewport changes.
            const open = [...this.openOrder];
            if (open.length) {
                const keep = open[open.length - 1];
                this.enforceMaxOpen(keep);
            }
            this.syncHeadStack();
        };

        this.moreBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleHeadsMenu();
        });

        document.addEventListener('click', this.handleDocClick);
        document.addEventListener('keydown', this.handleKey);
        window.addEventListener('resize', this.handleResize);

        document.addEventListener('ai-studio:use', ((e: CustomEvent) => {
            const ck = e.detail?.config?.checkpoint ?? {};
            const live: AiSavedConfig = {
                id: 'studio-live',
                name: ck.name || 'Studio session',
                avatar: null,
                subtitle: ck.meta || 'Live from studio',
                checkpoint: {
                    id: ck.id ?? null,
                    name: ck.name || 'Checkpoint',
                    meta: ck.meta ?? null,
                },
                loras: (e.detail?.config?.loras ?? []).map(
                    (l: { id?: string; name?: string; weight?: number }, i: number) => ({
                        id: String(l.id ?? `lora-${i}`),
                        name: String(l.name ?? 'LoRA'),
                        weight: Number(l.weight ?? 0.8),
                    }),
                ),
                settings: e.detail?.config?.settings ?? {},
                createdAt: new Date().toISOString(),
                updatedAt: new Date().toISOString(),
            };
            if (this.chats.has('studio-live')) this.close('studio-live');
            this.openAi(live);
            window.toasts?.success?.(`Opened ${live.name} in chat dock`);
        }) as EventListener);

        document.addEventListener('ai-studio:save', ((e: CustomEvent) => {
            const name = window.prompt('Name this configuration', e.detail?.config?.checkpoint?.name || 'My config');
            if (!name) return;
            const saved = AiConfigStore.fromStudioPayload({
                name,
                subtitle: e.detail?.config?.checkpoint?.meta || 'Saved preset',
                config: e.detail?.config ?? {},
            });
            document.dispatchEvent(new CustomEvent('ai-config:saved', { detail: { config: saved } }));
            window.toasts?.success?.(`Saved “${saved.name}”`);
        }) as EventListener);
    }

    /** Open a saved AI config / girlfriend in the same dock as people chats. */
    openAi(target: string | AiSavedConfig): void {
        const config = typeof target === 'string' ? AiConfigStore.get(target) : target;
        if (!config) {
            window.toasts?.error?.('Config not found');
            return;
        }

        const existing = this.chats.get(config.id);
        if (existing) {
            const next = this.aiToOpenOptions(config);
            existing.opts = {
                ...existing.opts,
                ...next,
                messages: existing.opts.messages?.length ? existing.opts.messages : next.messages,
            };
            this.refreshAiChrome(config.id);
            this.expand(config.id);
            return;
        }

        this.open(this.aiToOpenOptions(config));
    }

    open(options: ChatOpenOptions): void {
        const existing = this.chats.get(options.id);
        if (existing) {
            existing.opts = { ...existing.opts, ...options };
            if (options.kind === 'ai') this.refreshAiChrome(options.id);
            this.expand(options.id);
            return;
        }

        const windowEl = this.buildWindow(options);
        const headEl = this.buildHead(options);

        this.windowsHost.appendChild(windowEl);
        if (this.moreBtn) this.headsHost.insertBefore(headEl, this.moreBtn);
        else this.headsHost.appendChild(headEl);

        const entry: ChatEntry = { opts: options, state: 'open', windowEl, headEl };
        this.chats.set(options.id, entry);
        this.openOrder.push(options.id);
        this.applyState(options.id, 'open');

        // Staggered enter
        windowEl.classList.add('chat-window--entering');
        requestAnimationFrame(() => windowEl.classList.remove('chat-window--entering'));

        this.enforceMaxOpen(options.id);
        this.syncHeadStack();
        this.scrollToBottom(options.id);
        windowEl.querySelector<HTMLInputElement>('[data-chat-input]')?.focus();
    }

    minimize(id: string): void {
        if (!this.chats.has(id)) return;
        this.applyState(id, 'minimized');
        this.openOrder = this.openOrder.filter((x) => x !== id);
        this.headOrder = this.headOrder.filter((x) => x !== id);
        this.headOrder.push(id);
        this.closeMenu(id);
        this.syncHeadStack();
    }

    expand(id: string): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        this.applyState(id, 'open');
        this.openOrder = this.openOrder.filter((x) => x !== id);
        this.openOrder.push(id);
        this.headOrder = this.headOrder.filter((x) => x !== id);
        this.setUnread(id, 0);
        this.closeHeadsMenu();
        this.enforceMaxOpen(id);
        this.syncHeadStack();
        this.scrollToBottom(id);
        entry.windowEl.querySelector<HTMLInputElement>('[data-chat-input]')?.focus();
    }

    toggle(id: string): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        entry.state === 'open' ? this.minimize(id) : this.expand(id);
    }

    close(id: string): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        entry.voice?.destroy();
        entry.windowEl.remove();
        entry.headEl.remove();
        this.chats.delete(id);
        this.openOrder = this.openOrder.filter((x) => x !== id);
        this.headOrder = this.headOrder.filter((x) => x !== id);
        this.syncHeadStack();
    }

    closeAll(): void {
        [...this.chats.keys()].forEach((id) => this.close(id));
        this.closeHeadsMenu();
    }

    send(id: string, text: string): void {
        const entry = this.chats.get(id);
        const prompt = text.trim();
        if (!entry || !prompt) return;
        const msg: ChatMessage = { from: 'me', text: prompt, time: nowTime() };
        this.appendMessage(id, msg);

        if (entry.opts.kind !== 'ai') return;

        const mode = detectThinkingMode(prompt);
        entry.windowEl.dispatchEvent(
            new CustomEvent('ai-dock:send', {
                bubbles: true,
                detail: { id, prompt, mode, config: entry.opts.ai, kind: 'ai' },
            }),
        );
        entry.windowEl.dispatchEvent(
            new CustomEvent('chat-dock:send', {
                bubbles: true,
                detail: { id, prompt, mode, opts: entry.opts },
            }),
        );

        if (this.root.dataset.live === 'true') return;

        this.setThinking(id, mode);
        const delay = mode === 'video' ? 2200 : mode === 'image' ? 1600 : 1100;
        window.setTimeout(() => {
            this.clearThinking(id);
            const ck = entry.opts.ai?.checkpoint.name ?? entry.opts.name;
            const loraCount = entry.opts.ai?.loras.length ?? 0;
            if (mode === 'image' || mode === 'video') {
                this.receive(id, {
                    text: mode === 'video' ? 'Here’s a clip from your stack 🎬' : 'Here’s a photo from your stack 📸',
                    image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=500&fit=crop',
                });
            } else {
                this.receive(id, {
                    text: `Using ${ck} · ${loraCount} LoRA(s).\n\n“${prompt}”\n\n(Demo — listen for ai-dock:send / chat-dock:send.)`,
                });
            }
        }, delay);
    }

    sendVoice(id: string, audioUrl: string, durationSec: number, blob?: Blob, peaks?: number[]): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        this.appendMessage(id, {
            from: 'me',
            audio: audioUrl,
            audioDuration: durationSec,
            audioPeaks: peaks,
            time: nowTime(),
        });
        entry.windowEl.dispatchEvent(
            new CustomEvent('chat-dock:voice', {
                bubbles: true,
                detail: { id, audioUrl, durationSec, blob, peaks, opts: entry.opts },
            }),
        );
    }

    /** Incoming message — bumps unread badge when the chat is minimized. */
    receive(id: string, message: Omit<ChatMessage, 'from'> & { from?: 'them' }): void {
        if (!this.chats.has(id)) return;
        this.clearThinking(id);
        const msg: ChatMessage = {
            from: 'them',
            text: message.text,
            image: message.image,
            audio: message.audio,
            audioDuration: message.audioDuration,
            time: message.time ?? nowTime(),
            id: message.id,
        };
        this.appendMessage(id, msg);
        const entry = this.chats.get(id)!;
        if (entry.state === 'minimized') {
            this.setUnread(id, (entry.opts.unread ?? 0) + 1);
            this.syncHeadStack();
        }
    }

    /**
     * Show / hide the glowing thinking orb in an AI girlfriend chat.
     * Pass false (or call clearThinking) when the reply / media is ready.
     */
    setThinking(id: string, mode: AiThinkingMode | false = 'thinking'): void {
        if (mode === false) {
            this.clearThinking(id);
            return;
        }
        const entry = this.chats.get(id);
        if (!entry) return;
        const list = entry.windowEl.querySelector<HTMLElement>('[data-chat-messages]');
        if (!list) return;
        this.clearThinking(id);
        const avatar = `<div class="chat-msg-avatar">${this.renderAvatar(entry.opts, 28)}</div>`;
        list.insertAdjacentHTML('beforeend', chatThinkingHtml(mode, avatar));
        this.scrollToBottom(id);
    }

    clearThinking(id: string): void {
        const entry = this.chats.get(id);
        entry?.windowEl.querySelectorAll('[data-chat-thinking]').forEach((el) => el.remove());
    }

    getOpenIds(): string[] {
        return [...this.openOrder];
    }

    destroy(): void {
        document.removeEventListener('click', this.handleDocClick);
        document.removeEventListener('keydown', this.handleKey);
        window.removeEventListener('resize', this.handleResize);
        this.closeAll();
    }

    private enforceMaxOpen(keepId: string): void {
        const max = this.maxOpenWindows();
        while (this.openOrder.length > max) {
            const victim = this.openOrder.find((id) => id !== keepId) ?? this.openOrder[0];
            if (!victim) break;
            this.minimize(victim);
        }
    }

    /** How many full chat windows fit beside the head stack. */
    private maxOpenWindows(): number {
        const windowPitch = 352; // width + gap
        const headsReserve = 64;
        const margin = 24;
        const fit = Math.floor((window.innerWidth - headsReserve - margin) / windowPitch);
        return Math.max(1, Math.min(MAX_OPEN_CAP, fit));
    }

    private applyState(id: string, state: ChatState): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        entry.state = state;
        entry.windowEl.classList.toggle('chat-window--minimized', state === 'minimized');
        entry.windowEl.hidden = state === 'minimized';
        // Visibility of heads is owned by syncHeadStack().
        if (state === 'open') {
            entry.headEl.hidden = true;
            entry.headEl.classList.remove('chat-pill--overflow');
            entry.headEl.setAttribute('aria-hidden', 'true');
        }
    }

    /** How many head slots fit on the right (hard cap 10). */
    private maxVisibleHeads(): number {
        const cssMax = Number.parseInt(
            getComputedStyle(document.documentElement).getPropertyValue('--chat-heads-max').trim(),
            10,
        );
        const hardCap = Number.isFinite(cssMax) && cssMax > 0 ? cssMax : MAX_HEADS;
        const size = 48;
        const gap = 8;
        const pad = 48;
        const fit = Math.max(1, Math.floor((window.innerHeight - pad) / (size + gap)));
        return Math.min(hardCap, fit);
    }

    private syncHeadStack(): void {
        // Keep headOrder in sync with reality.
        this.headOrder = this.headOrder.filter((id) => this.chats.get(id)?.state === 'minimized');
        this.chats.forEach((entry, id) => {
            if (entry.state === 'minimized' && !this.headOrder.includes(id)) {
                this.headOrder.push(id);
            }
        });

        const minimized = this.headOrder;
        const max = this.maxVisibleHeads();
        const needsMore = minimized.length > max;
        const visibleCount = needsMore ? Math.max(0, max - 1) : minimized.length;
        const visible = minimized.slice(-visibleCount);
        const overflow = minimized.slice(0, Math.max(0, minimized.length - visibleCount));

        // column-reverse: first DOM child = bottom. Put newest first, then older, then more at end (= top).
        const ordered = [...visible].reverse();
        ordered.forEach((id) => {
            const entry = this.chats.get(id);
            if (!entry) return;
            entry.headEl.hidden = false;
            entry.headEl.classList.remove('chat-pill--overflow');
            entry.headEl.setAttribute('aria-hidden', 'false');
            this.headsHost.insertBefore(entry.headEl, this.moreBtn);
        });

        overflow.forEach((id) => {
            const entry = this.chats.get(id);
            if (!entry) return;
            entry.headEl.hidden = true;
            entry.headEl.classList.add('chat-pill--overflow');
            entry.headEl.setAttribute('aria-hidden', 'true');
        });

        // Open chats never show as heads.
        this.chats.forEach((entry) => {
            if (entry.state === 'open') {
                entry.headEl.hidden = true;
                entry.headEl.classList.remove('chat-pill--overflow');
            }
        });

        if (this.moreBtn) {
            this.moreBtn.hidden = !needsMore;
            this.headsHost.appendChild(this.moreBtn);
            if (this.headsMenu) this.headsHost.appendChild(this.headsMenu);
            const overflowUnread = overflow.reduce(
                (sum, id) => sum + (this.chats.get(id)?.opts.unread ?? 0),
                0,
            );
            const badge = this.moreBtn.querySelector<HTMLElement>('[data-chat-heads-more-badge]');
            if (badge) {
                badge.hidden = overflowUnread <= 0;
                badge.textContent = overflowUnread > 9 ? '9+' : String(overflowUnread);
            }
            this.moreBtn.classList.toggle('chat-pill--unread', overflowUnread > 0);
            this.moreBtn.setAttribute(
                'aria-label',
                needsMore
                    ? `${minimized.length - visibleCount} more chats${overflowUnread ? `, ${overflowUnread} unread` : ''}`
                    : 'More chats',
            );
        }

        if (this.headsMenu && !this.headsMenu.hidden) {
            this.renderHeadsMenu();
        }
    }

    private toggleHeadsMenu(): void {
        if (!this.headsMenu || !this.moreBtn) return;
        const open = this.headsMenu.hidden;
        this.closeAllMenus();
        if (open) {
            this.renderHeadsMenu();
            this.headsMenu.hidden = false;
            this.moreBtn.setAttribute('aria-expanded', 'true');
        } else {
            this.closeHeadsMenu();
        }
    }

    private closeHeadsMenu(): void {
        if (this.headsMenu) this.headsMenu.hidden = true;
        this.moreBtn?.setAttribute('aria-expanded', 'false');
    }

    private renderHeadsMenu(): void {
        if (!this.headsMenu) return;
        const entries = [...this.chats.values()].sort((a, b) => {
            // Minimized with unread first, then minimized, then open.
            const score = (e: ChatEntry) =>
                (e.state === 'minimized' ? 0 : 2) + ((e.opts.unread ?? 0) > 0 ? 0 : 1);
            return score(a) - score(b) || a.opts.name.localeCompare(b.opts.name);
        });

        if (!entries.length) {
            this.headsMenu.innerHTML = `<div class="chat-heads-menu-empty">No open chats</div>`;
            return;
        }

        this.headsMenu.innerHTML = entries.map((entry) => {
            const unread = entry.opts.unread ?? 0;
            const meta =
                entry.state === 'open' ? 'Open'
                    : entry.opts.status === 'live' ? 'Live'
                        : entry.opts.status === 'online' ? 'Online'
                            : (entry.opts.subtitle || 'Minimized');
            return `
                <button type="button" class="chat-heads-menu-item ${entry.state === 'open' ? 'is-open' : ''}" role="menuitem" data-chat-pick="${esc(entry.opts.id)}">
                    <span class="chat-heads-menu-avatar">${this.renderAvatar(entry.opts, 32)}</span>
                    <span class="chat-heads-menu-copy">
                        <span class="chat-heads-menu-name">${esc(entry.opts.name)}</span>
                        <span class="chat-heads-menu-meta">${esc(meta)}</span>
                    </span>
                    <span class="chat-heads-menu-unread" ${unread ? '' : 'hidden'}>${unread > 9 ? '9+' : unread}</span>
                </button>
            `;
        }).join('');

        this.headsMenu.querySelectorAll<HTMLElement>('[data-chat-pick]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = btn.dataset.chatPick;
                if (!id) return;
                this.closeHeadsMenu();
                this.expand(id);
            });
        });
    }

    private scrollToBottom(id: string): void {
        const list = this.chats.get(id)?.windowEl.querySelector('[data-chat-messages]');
        if (list) list.scrollTop = list.scrollHeight;
    }

    private closeAllMenus(): void {
        this.chats.forEach((_, id) => this.closeMenu(id));
    }

    private closeMenu(id: string): void {
        const menu = this.chats.get(id)?.windowEl.querySelector<HTMLElement>('[data-chat-menu]');
        const btn = this.chats.get(id)?.windowEl.querySelector<HTMLElement>('[data-chat-more]');
        if (menu) menu.hidden = true;
        btn?.setAttribute('aria-expanded', 'false');
    }

    private toggleMenu(id: string): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        const menu = entry.windowEl.querySelector<HTMLElement>('[data-chat-menu]');
        const btn = entry.windowEl.querySelector<HTMLElement>('[data-chat-more]');
        if (!menu || !btn) return;
        const open = menu.hidden;
        this.closeAllMenus();
        menu.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    private aiToOpenOptions(config: AiSavedConfig): ChatOpenOptions {
        return {
            id: config.id,
            name: config.name,
            avatar: config.avatar ?? undefined,
            status: 'online',
            subtitle: config.subtitle || config.checkpoint.name,
            kind: 'ai',
            ai: {
                checkpoint: config.checkpoint,
                loras: config.loras,
                settings: config.settings,
            },
            messages: [
                {
                    from: 'them',
                    text: `Ready with ${config.checkpoint.name}. Prompt me, ask for a photo, or a clip.`,
                    time: nowTime(),
                },
            ],
        };
    }

    private aiConfigBarHtml(ai?: ChatAiMeta): string {
        if (!ai) return '';
        const loraLabel =
            ai.loras.length > 0
                ? `${ai.loras.length} LoRA${ai.loras.length > 1 ? 's' : ''}`
                : 'No LoRAs';
        return `<div class="chat-ai-bar" data-chat-ai-bar>
            <span class="chat-ai-chip"><strong>${esc(ai.checkpoint.name)}</strong></span>
            <span class="chat-ai-chip">${esc(loraLabel)}</span>
            <span class="chat-ai-chip">${esc(String(ai.settings.steps ?? 28))} steps · CFG ${esc(String(ai.settings.cfg ?? 5.5))}</span>
        </div>`;
    }

    private refreshAiChrome(id: string): void {
        const entry = this.chats.get(id);
        if (!entry || entry.opts.kind !== 'ai') return;
        const bar = entry.windowEl.querySelector('[data-chat-ai-bar]');
        if (bar) bar.outerHTML = this.aiConfigBarHtml(entry.opts.ai);
        const status = entry.windowEl.querySelector('.chat-window-status');
        if (status) status.textContent = `AI · ${entry.opts.subtitle || entry.opts.ai?.checkpoint.name || 'model'}`;
        const name = entry.windowEl.querySelector('.chat-window-name');
        if (name) name.textContent = entry.opts.name;
    }

    private buildWindow(opts: ChatOpenOptions): HTMLElement {
        const el = document.createElement('section');
        const isAi = opts.kind === 'ai';
        el.className = `chat-window${isAi ? ' chat-window--ai' : ''}`;
        el.dataset.chatWindow = opts.id;
        el.dataset.chatKind = opts.kind ?? 'person';
        el.setAttribute('role', 'dialog');
        el.setAttribute('aria-label', `Chat with ${opts.name}`);

        const statusLabel = isAi
            ? `AI · ${opts.subtitle || opts.ai?.checkpoint.name || 'model'}`
            : opts.status === 'live' ? 'Live now'
                : opts.status === 'online' ? 'Online'
                    : (opts.subtitle ?? '');

        const statusClass =
            isAi ? 'chat-window-status--ai'
                : opts.status === 'online' || opts.status === 'live'
                    ? 'chat-window-status--online'
                    : '';

        const dayLabel = isAi ? 'Today' : "Aujourd'hui";
        const placeholder = isAi ? 'Prompt this model…' : 'Private message…';

        el.innerHTML = `
            <header class="chat-window-head" data-chat-head>
                <div class="chat-window-peer">
                    <div class="chat-window-avatar">
                        ${this.renderAvatar(opts, 32)}
                        ${opts.status === 'live' ? '<span class="chat-window-live">LIVE</span>' : ''}
                        ${isAi && opts.status !== 'live' ? '<span class="chat-window-ai-badge">AI</span>' : ''}
                    </div>
                    <div class="chat-window-meta">
                        <span class="chat-window-name">${esc(opts.name)}</span>
                        ${statusLabel ? `<span class="chat-window-status ${statusClass}">${esc(statusLabel)}</span>` : ''}
                    </div>
                </div>
                <div class="chat-window-actions">
                    ${isAi ? `<button type="button" class="chat-window-btn" data-chat-studio aria-label="Open studio" title="Studio">${ICONS.studio}</button>` : ''}
                    <button type="button" class="chat-window-btn" data-chat-more aria-label="More" aria-haspopup="menu" aria-expanded="false">${ICONS.more}</button>
                    <button type="button" class="chat-window-btn" data-chat-minimize aria-label="Minimize">${ICONS.minimize}</button>
                    <button type="button" class="chat-window-btn" data-chat-close aria-label="Close">${ICONS.close}</button>
                </div>
            </header>
            <div class="chat-window-menu" data-chat-menu hidden role="menu">
                ${this.renderMenu(opts)}
            </div>
            ${isAi ? this.aiConfigBarHtml(opts.ai) : ''}
            <div class="chat-window-messages" data-chat-messages>
                <div class="chat-day">${esc(dayLabel)}</div>
                ${this.renderMessages(opts.messages ?? [], opts)}
            </div>
            <footer class="chat-window-composer" data-chat-composer>
                <div class="chat-voice-tray" data-chat-voice-tray hidden>
                    <div class="chat-voice-recording" data-chat-voice-recording hidden>
                        <span class="chat-voice-dot" aria-hidden="true"></span>
                        <div class="chat-voice-wave" data-chat-voice-wave aria-hidden="true">${Array.from({ length: 40 }, () => '<i style="--h:18%"></i>').join('')}</div>
                        <span class="chat-voice-timer" data-chat-voice-timer>0:00</span>
                        <button type="button" class="chat-voice-stop" data-chat-voice-stop aria-label="Stop recording">${ICONS.stop}</button>
                    </div>
                    <div class="chat-voice-preview" data-chat-voice-preview hidden>
                        <button type="button" class="chat-voice-play" data-chat-voice-play aria-label="Play recording">${ICONS.play}</button>
                        <div class="chat-voice-wave chat-voice-wave--preview" data-chat-voice-preview-wave aria-hidden="true"></div>
                        <span class="chat-voice-preview-label" data-chat-voice-preview-label>0:00</span>
                        <button type="button" class="chat-voice-cancel" data-chat-voice-cancel aria-label="Cancel recording">${ICONS.trashSm}</button>
                        <button type="button" class="chat-composer-send" data-chat-voice-send aria-label="Send voice note">${ICONS.send}</button>
                    </div>
                </div>
                <div class="chat-composer-main" data-chat-composer-main>
                    <label class="chat-composer-field">
                        <input type="text" class="chat-composer-input" data-chat-input placeholder="${esc(placeholder)}" autocomplete="off" />
                        <span class="chat-composer-tools">
                            <button type="button" class="chat-composer-tool" aria-label="Attach photo">${ICONS.camera}</button>
                            <button type="button" class="chat-composer-tool" aria-label="Emoji">${ICONS.smile}</button>
                            <button type="button" class="chat-composer-tool chat-composer-tool--mic" data-chat-voice aria-label="Record voice message">${ICONS.mic}</button>
                        </span>
                    </label>
                    ${isAi ? '' : `<button type="button" class="chat-composer-gift" data-chat-gift aria-label="Send gift">${ICONS.gift}</button>`}
                    <button type="button" class="chat-composer-send" data-chat-send aria-label="Send">${ICONS.send}</button>
                </div>
            </footer>
        `;

        el.querySelector('[data-chat-head]')?.addEventListener('click', (e) => {
            const t = e.target as HTMLElement;
            if (t.closest('.chat-window-actions')) return;
            this.minimize(opts.id);
        });

        el.querySelector('[data-chat-more]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMenu(opts.id);
        });

        el.querySelector('[data-chat-minimize]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.minimize(opts.id);
        });

        el.querySelector('[data-chat-close]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.close(opts.id);
        });

        el.querySelector('[data-chat-studio]')?.addEventListener('click', (e) => {
            e.stopPropagation();
            window.location.href = '/ai#ai-studio';
        });

        el.querySelector('[data-chat-menu]')?.addEventListener('click', (e) => e.stopPropagation());

        const input = el.querySelector<HTMLInputElement>('[data-chat-input]');
        const send = () => {
            if (!input) return;
            this.send(opts.id, input.value);
            input.value = '';
        };
        el.querySelector('[data-chat-send]')?.addEventListener('click', send);
        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                send();
            }
        });

        el.querySelector('[data-chat-gift]')?.addEventListener('click', () => {
            window.toasts?.info?.(`Gift picker for ${opts.name} — wire to your tip modal.`);
        });

        this.bindVoice(opts.id, el);

        el.querySelectorAll<HTMLElement>('[data-chat-menu-action]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const action = btn.dataset.chatMenuAction;
                this.closeMenu(opts.id);
                if (action === 'delete' || action === 'close') {
                    this.close(opts.id);
                    window.toasts?.success?.(action === 'delete' ? 'Conversation removed.' : 'Closed.');
                    return;
                }
                if (action === 'clear') {
                    const entry = this.chats.get(opts.id);
                    if (entry) {
                        entry.opts.messages = [];
                        const list = entry.windowEl.querySelector('[data-chat-messages]');
                        if (list) {
                            list.innerHTML = `<div class="chat-day">Today</div>`;
                        }
                    }
                    window.toasts?.success?.('Conversation cleared.');
                    return;
                }
                if (action === 'studio' || action === 'params') {
                    window.location.href = '/ai#ai-studio';
                    return;
                }
                window.toasts?.info?.(btn.textContent?.trim() || 'Action');
            });
        });

        return el;
    }

    private buildHead(opts: ChatOpenOptions): HTMLElement {
        const btn = document.createElement('button');
        btn.type = 'button';
        const isAi = opts.kind === 'ai';
        btn.className = `chat-pill${isAi ? ' chat-pill--ai' : ''}`;
        btn.dataset.chatHead = opts.id;
        btn.setAttribute('aria-label', `Open chat with ${opts.name}`);
        btn.hidden = true;
        btn.innerHTML = `
            <span class="chat-pill-badge" data-chat-unread ${opts.unread ? '' : 'hidden'}>${opts.unread && opts.unread > 9 ? '9+' : (opts.unread ?? 0)}</span>
            ${this.renderAvatar(opts, 48)}
            ${opts.status === 'online' || opts.status === 'live' ? '<span class="chat-pill-dot"></span>' : ''}
            ${isAi ? '<span class="chat-pill-ai-mark">AI</span>' : ''}
            <span class="chat-pill-close" data-chat-head-close aria-label="Close">&times;</span>
        `;
        if (opts.unread) btn.classList.add('chat-pill--unread');

        btn.addEventListener('click', (e) => {
            if ((e.target as HTMLElement).closest('[data-chat-head-close]')) {
                e.stopPropagation();
                this.close(opts.id);
                return;
            }
            this.expand(opts.id);
        });

        return btn;
    }

    private appendMessage(id: string, msg: ChatMessage): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        entry.opts.messages = [...(entry.opts.messages ?? []), msg];
        const list = entry.windowEl.querySelector('[data-chat-messages]');
        if (!list) return;
        // Re-render so group start/end + avatar gutter stay correct.
        const day = list.querySelector('.chat-day');
        list.innerHTML = '';
        if (day) list.appendChild(day);
        else {
            const d = document.createElement('div');
            d.className = 'chat-day';
            d.textContent = "Aujourd'hui";
            list.appendChild(d);
        }
        list.insertAdjacentHTML('beforeend', this.renderMessages(entry.opts.messages ?? [], entry.opts));
        this.scrollToBottom(id);
    }

    private setUnread(id: string, count: number): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        entry.opts.unread = count;
        const badge = entry.headEl.querySelector<HTMLElement>('[data-chat-unread]');
        if (badge) {
            badge.hidden = count <= 0;
            badge.textContent = count > 9 ? '9+' : String(count);
        }
        entry.headEl.classList.toggle('chat-pill--unread', count > 0);
        entry.headEl.setAttribute(
            'aria-label',
            count > 0
                ? `${count} new message${count > 1 ? 's' : ''} from ${entry.opts.name}`
                : `Open chat with ${entry.opts.name}`,
        );
    }

    private renderAvatar(opts: ChatOpenOptions, size: number): string {
        if (opts.avatar) {
            return `<span class="user-avatar-wrap" style="width:${size}px;height:${size}px"><span class="user-avatar"><img class="avatar-img" src="${esc(opts.avatar)}" alt=""></span></span>`;
        }
        return `<span class="user-avatar-wrap" style="width:${size}px;height:${size}px"><span class="user-avatar"><span class="avatar-initials">${esc(initials(opts.name))}</span></span></span>`;
    }

    private renderMessages(messages: ChatMessage[], opts: ChatOpenOptions): string {
        return messages.map((msg, i) => {
            const prev = messages[i - 1];
            const next = messages[i + 1];
            const groupStart = !prev || prev.from !== msg.from;
            const groupEnd = !next || next.from !== msg.from;
            return this.renderMessage(msg, opts, { groupStart, groupEnd });
        }).join('');
    }

    private renderMessage(
        msg: ChatMessage,
        opts: ChatOpenOptions,
        group: { groupStart: boolean; groupEnd: boolean } = { groupStart: true, groupEnd: true },
    ): string {
        const media = msg.image
            ? `<img class="chat-bubble-media" src="${esc(msg.image)}" alt="">`
            : '';
        const audio = msg.audio
            ? `<div class="chat-voice-bubble" data-chat-voice-bubble>
                <button type="button" class="chat-voice-bubble-play" data-chat-audio-play aria-label="Play voice note">${ICONS.play}</button>
                <div class="chat-voice-bubble-bars" data-chat-voice-bars aria-hidden="true">${waveBarsHtml(msg.audioPeaks ?? [], 32)}</div>
                <span class="chat-voice-bubble-time" data-chat-voice-time>${esc(formatVoiceDuration(msg.audioDuration ?? 0))}</span>
                <audio src="${esc(msg.audio)}" preload="metadata" data-chat-audio data-duration="${msg.audioDuration ?? 0}"></audio>
               </div>`
            : '';
        const text = msg.text ? `<p class="chat-bubble-text">${esc(msg.text)}</p>` : '';
        const meta = group.groupEnd && msg.time
            ? `<div class="chat-msg-meta"><span>${esc(msg.time)}</span></div>`
            : `<div class="chat-msg-meta" hidden></div>`;
        const bubbleClass = `chat-bubble${msg.image ? ' chat-bubble--media' : ''}${msg.audio ? ' chat-bubble--voice' : ''}`;
        const groupClass = [
            group.groupStart ? 'chat-msg--group-start' : '',
            group.groupEnd ? 'chat-msg--group-end' : '',
        ].filter(Boolean).join(' ');

        const avatar = msg.from === 'them'
            ? (group.groupEnd
                ? `<div class="chat-msg-avatar">${this.renderAvatar(opts, 28)}</div>`
                : `<div class="chat-msg-avatar is-spacer" aria-hidden="true"></div>`)
            : '';

        return `
            <div class="chat-msg chat-msg--${msg.from} ${groupClass}">
                ${avatar}
                <div class="chat-msg-body">
                    <div class="${bubbleClass}">
                        ${media}
                        ${audio}
                        ${text}
                    </div>
                    ${meta}
                </div>
            </div>
        `;
    }

    private bindVoice(id: string, el: HTMLElement): void {
        const ensureVoice = (): VoiceNoteRecorder | null => {
            const e = this.chats.get(id);
            if (!e) return null;
            if (!e.voice) {
                e.voice = new VoiceNoteRecorder({
                    onState: (state) => this.renderVoiceUi(id, state),
                    onTick: (elapsed, peaks) => this.renderVoiceTick(id, elapsed, peaks),
                    onError: (message) => window.toasts?.error?.(message),
                });
            }
            return e.voice;
        };

        el.querySelector('[data-chat-voice]')?.addEventListener('click', () => {
            void ensureVoice()?.start();
        });
        el.querySelector('[data-chat-voice-stop]')?.addEventListener('click', () => {
            ensureVoice()?.stop();
        });
        el.querySelector('[data-chat-voice-cancel]')?.addEventListener('click', () => {
            ensureVoice()?.cancel();
        });
        el.querySelector('[data-chat-voice-send]')?.addEventListener('click', () => {
            const voice = ensureVoice();
            const result = voice?.consume();
            if (!result) return;
            this.sendVoice(id, result.url, result.durationSec, result.blob, result.peaks);
            this.renderVoiceUi(id, 'idle');
        });

        let previewAudio: HTMLAudioElement | null = null;
        let previewRaf = 0;
        const stopPreviewProgress = () => {
            if (previewRaf) cancelAnimationFrame(previewRaf);
            previewRaf = 0;
        };
        const paintPreviewProgress = (progress: number) => {
            const wave = el.querySelector<HTMLElement>('[data-chat-voice-preview-wave]');
            if (!wave) return;
            wave.style.setProperty('--voice-progress', String(Math.min(1, Math.max(0, progress))));
            const bars = wave.querySelectorAll('i');
            bars.forEach((bar, i) => {
                bar.classList.toggle('is-played', (i + 0.5) / bars.length <= progress);
            });
        };
        const tickPreviewProgress = () => {
            if (!previewAudio || previewAudio.paused) return;
            const dur = previewAudio.duration || 1;
            paintPreviewProgress(previewAudio.currentTime / dur);
            previewRaf = requestAnimationFrame(tickPreviewProgress);
        };

        el.querySelector('[data-chat-voice-play]')?.addEventListener('click', () => {
            const voice = ensureVoice();
            const result = voice?.getResult();
            const btn = el.querySelector<HTMLElement>('[data-chat-voice-play]');
            if (!result || !btn) return;
            if (!previewAudio || previewAudio.src !== result.url) {
                previewAudio?.pause();
                previewAudio = new Audio(result.url);
            }
            if (previewAudio.paused) {
                void previewAudio.play();
                btn.innerHTML = ICONS.pause;
                stopPreviewProgress();
                tickPreviewProgress();
                previewAudio.onended = () => {
                    btn.innerHTML = ICONS.play;
                    paintPreviewProgress(1);
                    stopPreviewProgress();
                    setTimeout(() => paintPreviewProgress(0), 250);
                };
            } else {
                previewAudio.pause();
                btn.innerHTML = ICONS.play;
                stopPreviewProgress();
            }
        });

        // Click waveform in preview to seek
        el.querySelector('[data-chat-voice-preview-wave]')?.addEventListener('click', (ev) => {
            if (!previewAudio) return;
            const wave = ev.currentTarget as HTMLElement;
            const rect = wave.getBoundingClientRect();
            const ratio = Math.min(1, Math.max(0, ((ev as MouseEvent).clientX - rect.left) / rect.width));
            if (Number.isFinite(previewAudio.duration)) {
                previewAudio.currentTime = ratio * previewAudio.duration;
                paintPreviewProgress(ratio);
            }
        });

        // Play voice bubbles in the thread with Messenger-style progress
        el.addEventListener('click', (ev) => {
            const target = ev.target as HTMLElement;
            const mouse = ev as MouseEvent;

            // Seek on waveform click
            const barsEl = target.closest<HTMLElement>('[data-chat-voice-bars]');
            if (barsEl && !target.closest('[data-chat-audio-play]')) {
                const wrap = barsEl.closest('[data-chat-voice-bubble]');
                const audio = wrap?.querySelector<HTMLAudioElement>('[data-chat-audio]');
                if (audio && wrap) {
                    const dur =
                        Number.isFinite(audio.duration) && audio.duration > 0
                            ? audio.duration
                            : Number(audio.dataset.duration) || 0;
                    if (dur > 0) {
                        const rect = barsEl.getBoundingClientRect();
                        const ratio = Math.min(1, Math.max(0, (mouse.clientX - rect.left) / rect.width));
                        audio.currentTime = ratio * dur;
                        paintVoiceProgress(wrap, ratio, audio);
                        return;
                    }
                }
            }

            const playBtn = target.closest<HTMLElement>('[data-chat-audio-play]');
            if (!playBtn) return;
            const wrap = playBtn.closest('[data-chat-voice-bubble]');
            const audio = wrap?.querySelector<HTMLAudioElement>('[data-chat-audio]');
            if (!audio || !wrap) return;

            if (audio.paused) {
                el.querySelectorAll<HTMLAudioElement>('[data-chat-audio]').forEach((a) => {
                    if (a === audio) return;
                    a.pause();
                    const otherWrap = a.closest('[data-chat-voice-bubble]');
                    const otherBtn = otherWrap?.querySelector<HTMLElement>('[data-chat-audio-play]');
                    if (otherBtn) otherBtn.innerHTML = ICONS.play;
                    stopVoiceProgress(otherWrap);
                });
                void audio.play();
                playBtn.innerHTML = ICONS.pause;
                startVoiceProgress(wrap, audio, playBtn);
            } else {
                audio.pause();
                playBtn.innerHTML = ICONS.play;
                stopVoiceProgress(wrap);
            }
        });
    }

    private renderVoiceUi(id: string, state: 'idle' | 'recording' | 'preview'): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        const tray = entry.windowEl.querySelector<HTMLElement>('[data-chat-voice-tray]');
        const main = entry.windowEl.querySelector<HTMLElement>('[data-chat-composer-main]');
        const recording = entry.windowEl.querySelector<HTMLElement>('[data-chat-voice-recording]');
        const preview = entry.windowEl.querySelector<HTMLElement>('[data-chat-voice-preview]');
        if (!tray || !main || !recording || !preview) return;

        const active = state !== 'idle';
        tray.hidden = !active;
        main.hidden = active;
        recording.hidden = state !== 'recording';
        preview.hidden = state !== 'preview';

        if (state === 'preview') {
            const result = entry.voice?.getResult();
            const label = entry.windowEl.querySelector('[data-chat-voice-preview-label]');
            if (label && result) label.textContent = formatVoiceDuration(result.durationSec);
            const play = entry.windowEl.querySelector('[data-chat-voice-play]');
            if (play) play.innerHTML = ICONS.play;
            const wave = entry.windowEl.querySelector<HTMLElement>('[data-chat-voice-preview-wave]');
            if (wave && result) {
                wave.innerHTML = waveBarsHtml(result.peaks, 40);
                wave.style.setProperty('--voice-progress', '0');
            }
        }
        if (state === 'idle') {
            const timer = entry.windowEl.querySelector('[data-chat-voice-timer]');
            if (timer) timer.textContent = '0:00';
            entry.windowEl.querySelectorAll<HTMLElement>('[data-chat-voice-wave] i').forEach((bar) => {
                bar.style.setProperty('--h', '18%');
                bar.classList.remove('is-played');
            });
        }
        if (state === 'recording') {
            // Reset bars for a fresh scrolling wave
            const wave = entry.windowEl.querySelector<HTMLElement>('[data-chat-voice-wave]');
            if (wave) {
                wave.innerHTML = Array.from({ length: 40 }, () => '<i style="--h:18%"></i>').join('');
            }
        }
    }

    private renderVoiceTick(id: string, elapsed: number, peaks?: number[]): void {
        const entry = this.chats.get(id);
        if (!entry) return;
        const timer = entry.windowEl.querySelector('[data-chat-voice-timer]');
        if (timer) timer.textContent = formatVoiceDuration(elapsed);
        if (!peaks?.length) return;

        const bars = entry.windowEl.querySelectorAll<HTMLElement>('[data-chat-voice-wave]:not(.chat-voice-wave--preview) i');
        // Paint right-aligned scrolling history: newest peak on the right
        const offset = Math.max(0, bars.length - peaks.length);
        bars.forEach((bar, i) => {
            const peakIdx = i - offset;
            const level = peakIdx >= 0 ? (peaks[peakIdx] ?? 0.15) : 0.1;
            const h = Math.round(12 + level * 88);
            bar.style.setProperty('--h', `${h}%`);
            bar.classList.toggle('is-live', peakIdx === peaks.length - 1);
        });
    }

    private renderMenu(opts?: ChatOpenOptions): string {
        if (opts?.kind === 'ai') {
            return `
                <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="studio">
                    <span class="chat-menu-item-icon">${ICONS.studio}</span>
                    <span class="chat-menu-item-label">Open model studio</span>
                </button>
                <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="profile">
                    <span class="chat-menu-item-icon">${ICONS.user}</span>
                    <span class="chat-menu-item-label">View / edit config</span>
                </button>
                <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="params">
                    <span class="chat-menu-item-icon">${ICONS.bookmark}</span>
                    <span class="chat-menu-item-label">Generation settings</span>
                </button>
                <div class="chat-menu-sep"></div>
                <div class="chat-menu-item chat-menu-item--toggle">
                    <span class="chat-menu-item-icon">${ICONS.bell}</span>
                    <span class="chat-menu-item-label">Notify when generation finishes</span>
                    <label class="chat-menu-toggle" onclick="event.stopPropagation()">
                        <input type="checkbox" aria-label="Generation notifications" checked>
                        <span class="chat-menu-toggle-ui"></span>
                    </label>
                </div>
                <div class="chat-menu-sep"></div>
                <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="clear">
                    <span class="chat-menu-item-icon">${ICONS.trash}</span>
                    <span class="chat-menu-item-label">Clear conversation</span>
                </button>
                <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="close">
                    <span class="chat-menu-item-icon">${ICONS.close}</span>
                    <span class="chat-menu-item-label">Close chat</span>
                </button>
            `;
        }

        return `
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="profile">
                <span class="chat-menu-item-icon">${ICONS.user}</span>
                <span class="chat-menu-item-label">Afficher le profil</span>
            </button>
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="bookmark">
                <span class="chat-menu-item-icon">${ICONS.bookmark}</span>
                <span class="chat-menu-item-label">Ajouter aux signets</span>
            </button>
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="translate">
                <span class="chat-menu-item-icon">${ICONS.translate}</span>
                <span class="chat-menu-item-label">Paramètres de traduction</span>
            </button>
            <div class="chat-menu-sep"></div>
            <div class="chat-menu-item chat-menu-item--toggle">
                <span class="chat-menu-item-icon">${ICONS.bell}</span>
                <span class="chat-menu-item-label">M’avertir lorsque je reçois des messages</span>
                <span class="chat-menu-help" title="Recevoir une notification pour chaque nouveau message">?</span>
                <label class="chat-menu-toggle" onclick="event.stopPropagation()">
                    <input type="checkbox" aria-label="Notifications">
                    <span class="chat-menu-toggle-ui"></span>
                </label>
            </div>
            <div class="chat-menu-sep"></div>
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="requests">
                <span class="chat-menu-item-icon">${ICONS.requests}</span>
                <span class="chat-menu-item-label">Déplacer vers Demandes de messages</span>
            </button>
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="report">
                <span class="chat-menu-item-icon">${ICONS.warning}</span>
                <span class="chat-menu-item-label">Signaler le modèle</span>
            </button>
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="block">
                <span class="chat-menu-item-icon">${ICONS.block}</span>
                <span class="chat-menu-item-label">Bloquer le modèle</span>
            </button>
            <button type="button" class="chat-menu-item" role="menuitem" data-chat-menu-action="delete">
                <span class="chat-menu-item-icon">${ICONS.trash}</span>
                <span class="chat-menu-item-label">Supprimer discussion</span>
            </button>
        `;
    }
}
