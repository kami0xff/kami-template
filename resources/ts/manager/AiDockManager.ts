/**
 * AiDockManager — thin alias of ChatDockManager.
 *
 * AI models and people chats share one dock (`window.chats`).
 * Keep this facade so existing `aiDock.open(...)` calls keep working.
 */

import type { AiSavedConfig } from '../ai/config-store';
import type { AiThinkingMode } from '../ai/thinking-orb';
import type { ChatDockManager } from './ChatDockManager';

function dock(): ChatDockManager | undefined {
    return window.chats;
}

export class AiDockManager {
    open(target: string | AiSavedConfig): void {
        dock()?.openAi(target);
    }

    minimize(id: string): void {
        dock()?.minimize(id);
    }

    expand(id: string): void {
        dock()?.expand(id);
    }

    close(id: string): void {
        dock()?.close(id);
    }

    closeAll(): void {
        // Only close AI windows so person chats stay open
        const chats = dock();
        if (!chats) return;
        chats.getOpenIds();
        // closeAll would wipe people chats — close AI ids via DOM kind
        document.querySelectorAll<HTMLElement>('[data-chat-kind="ai"]').forEach((el) => {
            const id = el.dataset.chatWindow;
            if (id) chats.close(id);
        });
        // also minimized AI heads
        document.querySelectorAll<HTMLElement>('.chat-window--ai').forEach((el) => {
            const id = el.dataset.chatWindow;
            if (id) chats.close(id);
        });
    }

    setThinking(id: string, mode: AiThinkingMode | false = 'thinking'): void {
        dock()?.setThinking(id, mode);
    }

    clearThinking(id: string): void {
        dock()?.clearThinking(id);
    }
}
