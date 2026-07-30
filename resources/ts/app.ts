// Application entrypoint (TypeScript).
// Built by Vite into public/build/assets/app.js and loaded in the layout.
//
// The CSS is imported here so Vite bundles the design system into
// public/build/assets/app.css (tokens → component tokens → component CSS).
import '../css/app.css';

import { PanelManager } from './manager/PanelManager';
import { ModalManager } from './manager/ModalManager';
import { SidebarManager } from './manager/SidebarManager';
import { SubSidebarManager } from './manager/SubSidebarManager';
import { ToastNotificationManager } from './manager/ToastNotificationManager';
import { BannerManager } from './manager/BannerManager';
import { NotificationManager } from './manager/NotificationManager';
import { DataTableManager } from './manager/DataTableManager';
import { PopoverManager } from './manager/PopoverManager';
import { ConfirmDialogManager } from './manager/ConfirmDialogManager';
import { OverflowListManager } from './manager/OverflowListManager';
import { LightboxManager } from './manager/LightboxManager';
import { CarouselManager } from './manager/CarouselManager';
import { ContextMenuManager } from './manager/ContextMenuManager';
import { CommandPaletteManager } from './manager/CommandPaletteManager';
import { CalendarManager } from './manager/CalendarManager';
import { DateFieldManager } from './manager/DateFieldManager';
import { SelectorManager } from './manager/SelectorManager';
import { ResizableManager } from './manager/ResizableManager';
import { SearchSuggestManager } from './manager/SearchSuggestManager';
import { SubnavSearchManager } from './manager/SubnavSearchManager';
import { UploadManager } from './manager/UploadManager';
import { MultipartFormManager } from './manager/MultipartFormManager';
import { ChatDockManager } from './manager/ChatDockManager';
import { ForumManager } from './manager/ForumManager';
import { ExploreManager } from './manager/ExploreManager';
import { StoriesManager } from './manager/StoriesManager';
import { AuthGuestManager } from './manager/AuthGuestManager';
import { FormWidgetsManager } from './manager/FormWidgetsManager';
import { AgeGateManager } from './manager/AgeGateManager';
import { VideoPlayerManager } from './manager/VideoPlayerManager';
import { ChartManager } from './manager/ChartManager';
import { TipManager } from './manager/TipManager';
import { ShaderHeroManager } from './manager/ShaderHeroManager';
import { InfiniteScrollManager } from './manager/InfiniteScrollManager';
import { bootDesignThemeLab } from './manager/DesignThemeLabManager';
import { MegaMenuManager } from './manager/MegaMenuManager';
import { CommentManager } from './manager/CommentManager';
import { ReactionManager } from './manager/ReactionManager';
import { CreatorSectionsManager } from './manager/CreatorSectionsManager';
import { PaymentPageManager } from './manager/PaymentPageManager';
import { AiChatManager } from './manager/AiChatManager';
import { AiStudioManager } from './manager/AiStudioManager';
import { AiDockManager } from './manager/AiDockManager';
import { AiConfigManager } from './manager/AiConfigManager';
import { CreateModelManager } from './manager/CreateModelManager';
import { AccordionManager } from './manager/AccordionManager';
import { initTabs } from './manager/TabManager';

function bootApp(): void {
    const panelManager = new PanelManager({}, { multiPanelMode: true });
    const modalManager = new ModalManager();
    const toastManager = new ToastNotificationManager();

    window.panels = panelManager;
    window.modals = modalManager;
    window.toasts = toastManager;

    toastManager.consumeFlash();

    // Always available — creates `.banner-top-root` when the page has no app-shell.
    const bannerManager = new BannerManager();
    window.banners = bannerManager;
    bannerManager.consumeFlash();

    initTabs();

    window.dataTables = new DataTableManager();
    window.notifications = new NotificationManager();
    window.popovers = new PopoverManager();

    // Drawers reuse ModalManager with drawer selectors/classes.
    window.drawers = new ModalManager({
        modalSelector: '.drawer',
        triggerSelector: '[data-action="drawer"]',
        activeClass: 'drawer-open',
    });
    window.dialogs = new ConfirmDialogManager();
    window.lightbox = new LightboxManager();
    window.commandPalette = new CommandPaletteManager();
    window.contextMenus = new ContextMenuManager();
    new OverflowListManager();
    new CarouselManager();
    new CalendarManager();
    new DateFieldManager();
    new SelectorManager();
    new ResizableManager();
    new SearchSuggestManager();
    window.subnavSearch = new SubnavSearchManager();
    new AccordionManager();
    new CommentManager();
    new ReactionManager();
    new CreatorSectionsManager();
    new PaymentPageManager();
    window.aiChat = new AiChatManager();
    new AiStudioManager();
    new AiConfigManager();
    new CreateModelManager();
    new ForumManager();
    window.explore = new ExploreManager();
    window.stories = new StoriesManager();
    new AuthGuestManager();
    new FormWidgetsManager();
    window.ageGate = new AgeGateManager();
    window.videoPlayers = new VideoPlayerManager();
    window.charts = new ChartManager();
    window.tips = new TipManager();
    new ShaderHeroManager();
    window.infiniteScroll = new InfiniteScrollManager();
    bootDesignThemeLab();
    window.uploads = new UploadManager();
    window.multiparts = new MultipartFormManager();

    initNumberInputs();
    initRelativeTimestamps();

    if (document.querySelector('.sidebar')) {
        window.sidebar = new SidebarManager(panelManager, modalManager);
    }

    window.subSidebars = new SubSidebarManager();
    window.chats = new ChatDockManager();
    // Same dock as chats — people + AI models share one interface
    window.aiDock = new AiDockManager();
    window.megaMenus = new MegaMenuManager();
}

// Modules are deferred — DOMContentLoaded may already have fired.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp);
} else {
    bootApp();
}

// Steppers for <x-form.number> — native stepUp/stepDown keeps min/max/step.
function initNumberInputs(): void {
    document.addEventListener('click', (e) => {
        const btn = (e.target as HTMLElement | null)?.closest<HTMLElement>('[data-number-inc], [data-number-dec]');
        if (!btn) return;
        const input = btn.closest('.form-number')?.querySelector('input');
        if (!input) return;
        btn.hasAttribute('data-number-inc') ? input.stepUp() : input.stepDown();
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

// Relative time for <x-timestamp> ("3 min ago"), refreshed every minute.
function initRelativeTimestamps(): void {
    const format = (el: HTMLElement) => {
        const date = new Date(el.getAttribute('datetime') ?? '');
        if (isNaN(date.getTime())) return;
        const seconds = (date.getTime() - Date.now()) / 1000;
        const rtf = new Intl.RelativeTimeFormat(document.documentElement.lang || undefined, { numeric: 'auto' });
        const table: Array<[Intl.RelativeTimeFormatUnit, number]> = [
            ['year', 31536000], ['month', 2592000], ['week', 604800],
            ['day', 86400], ['hour', 3600], ['minute', 60],
        ];
        for (const [unit, size] of table) {
            if (Math.abs(seconds) >= size) {
                el.textContent = rtf.format(Math.round(seconds / size), unit);
                return;
            }
        }
        el.textContent = rtf.format(Math.round(seconds), 'second');
    };

    const all = () => document.querySelectorAll<HTMLElement>('[data-timestamp]').forEach(format);
    all();
    setInterval(all, 60000);
}

// Sidebar toggle — desktop collapses to an icon rail, mobile opens the drawer.
const SIDEBAR_RAIL_KEY = 'kami.sidebar-rail';
const DESKTOP_MQ = '(min-width: 1021px)';

function isDesktopSidebar(): boolean {
    return window.matchMedia(DESKTOP_MQ).matches;
}

function syncSidebarRailLabels(): void {
    document.querySelectorAll<HTMLElement>('.sidebar-link, .dropdown-trigger').forEach(el => {
        const text =
            el.querySelector('.sidebar-link-text, .dropdown-trigger-text')?.textContent?.trim() ??
            el.getAttribute('aria-label') ??
            '';
        if (!text) return;
        if (!el.getAttribute('title')) el.setAttribute('title', text);
        if (!el.getAttribute('data-tooltip')) el.setAttribute('data-tooltip', text);
        if (el.classList.contains('sidebar-link') && !el.querySelector('.sidebar-link-icon')) {
            el.dataset.initial = text.charAt(0);
        }
    });
}

function setSidebarRail(collapsed: boolean): void {
    document.body.classList.toggle('sidebar-rail', collapsed);
    try {
        localStorage.setItem(SIDEBAR_RAIL_KEY, collapsed ? '1' : '0');
    } catch {
        /* private mode */
    }
    // Sub-panels / in-place dropdowns need horizontal room — close them.
    if (collapsed) {
        window.subSidebars?.close();
        window.sidebar?.getDropdowns()?.closeAll();
    }
    syncHamburgerLabel();
}

function setSidebarDrawer(open: boolean): void {
    document.body.classList.toggle('sidebar-open', open);
    syncHamburgerLabel();
}

function syncHamburgerLabel(): void {
    const btn = document.querySelector<HTMLElement>('.nav-hamburger');
    if (!btn) return;
    const desktop = isDesktopSidebar();
    const label = desktop
        ? document.body.classList.contains('sidebar-rail')
            ? 'Expand sidebar'
            : 'Collapse sidebar'
        : document.body.classList.contains('sidebar-open')
          ? 'Close menu'
          : 'Open menu';
    btn.setAttribute('aria-label', label);
    btn.setAttribute('title', label);
    btn.setAttribute('aria-expanded', String(
        desktop ? !document.body.classList.contains('sidebar-rail') : document.body.classList.contains('sidebar-open'),
    ));
}

function toggleAppSidebar(force?: boolean): void {
    if (isDesktopSidebar()) {
        const next =
            typeof force === 'boolean' ? force : !document.body.classList.contains('sidebar-rail');
        // force=true → collapse to rail; force=false → expand full sidebar
        setSidebarRail(next);
    } else {
        const next =
            typeof force === 'boolean' ? force : !document.body.classList.contains('sidebar-open');
        setSidebarDrawer(next);
    }
}
window.toggleAppSidebar = toggleAppSidebar;

// Restore desktop rail preference; prepare tooltips.
document.addEventListener('DOMContentLoaded', () => {
    syncSidebarRailLabels();
    if (isDesktopSidebar()) {
        try {
            if (localStorage.getItem(SIDEBAR_RAIL_KEY) === '1') setSidebarRail(true);
        } catch {
            /* ignore */
        }
    }
    syncHamburgerLabel();
});

// Crossing the breakpoint: drop the wrong mode class.
window.matchMedia(DESKTOP_MQ).addEventListener('change', (e) => {
    if (e.matches) {
        setSidebarDrawer(false);
        try {
            if (localStorage.getItem(SIDEBAR_RAIL_KEY) === '1') setSidebarRail(true);
        } catch {
            /* ignore */
        }
    } else {
        setSidebarRail(false);
    }
    syncHamburgerLabel();
});

document.addEventListener('keydown', (e) => {
    // Escape only closes the mobile drawer — rail stays put.
    if (e.key === 'Escape' && !isDesktopSidebar()) toggleAppSidebar(false);
});

declare global {
    interface Window {
        panels: PanelManager;
        modals: ModalManager;
        sidebar: SidebarManager;
        subSidebars: SubSidebarManager;
        toasts: ToastNotificationManager;
        banners: BannerManager;
        dataTables: DataTableManager;
        notifications: NotificationManager;
        popovers: PopoverManager;
        drawers: ModalManager;
        dialogs: ConfirmDialogManager;
        lightbox: LightboxManager;
        commandPalette: CommandPaletteManager;
        contextMenus: ContextMenuManager;
        uploads: UploadManager;
        multiparts: MultipartFormManager;
        chats: ChatDockManager;
        aiChat: AiChatManager;
        aiDock: AiDockManager;
        megaMenus: MegaMenuManager;
        subnavSearch: SubnavSearchManager;
        explore: ExploreManager;
        stories: StoriesManager;
        ageGate: AgeGateManager;
        videoPlayers: VideoPlayerManager;
        charts: ChartManager;
        tips: TipManager;
        infiniteScroll: InfiniteScrollManager;
        toggleAppSidebar: (force?: boolean) => void;
    }
}

export {};
