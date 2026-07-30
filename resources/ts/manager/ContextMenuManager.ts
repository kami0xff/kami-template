/**
 * ContextMenuManager — right-click menus.
 *
 *   <div data-context-menu="photo-menu">…right-clickable region…</div>
 *   <x-context-menu id="photo-menu"> …menu items… </x-context-menu>
 *
 * The menu opens at the cursor (clamped to the viewport) and closes on
 * click, Escape, scroll, or another context menu opening.
 */
export class ContextMenuManager {
    private openMenu: HTMLElement | null = null;

    constructor() {
        document.addEventListener('contextmenu', (e) => {
            const region = (e.target as HTMLElement | null)?.closest<HTMLElement>('[data-context-menu]');
            if (!region) return;
            const menu = document.getElementById(region.dataset.contextMenu ?? '');
            if (!menu) return;

            e.preventDefault();
            this.closeAll();
            menu.classList.add('context-menu--open');
            this.openMenu = menu;

            // Clamp to viewport
            const { innerWidth, innerHeight } = window;
            const rect = menu.getBoundingClientRect();
            menu.style.left = `${Math.min(e.clientX, innerWidth - rect.width - 8)}px`;
            menu.style.top = `${Math.min(e.clientY, innerHeight - rect.height - 8)}px`;
        });

        document.addEventListener('click', () => this.closeAll());
        document.addEventListener('scroll', () => this.closeAll(), { passive: true, capture: true });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closeAll();
        });
    }

    closeAll(): void {
        this.openMenu?.classList.remove('context-menu--open');
        this.openMenu = null;
    }
}
