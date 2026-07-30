/**
 * AccordionManager — exclusive open behavior for [data-accordion-exclusive].
 *
 * Markup:
 *   [data-accordion][data-accordion-exclusive]
 *     details[data-collapsible]
 *
 * When one item opens, siblings in the same group close.
 * Standalone <x-collapsible> needs no JS (native details + CSS chevron).
 */

export class AccordionManager {
    constructor(scope: ParentNode = document) {
        scope.querySelectorAll<HTMLElement>('[data-accordion-exclusive]').forEach((group) => {
            group.addEventListener('toggle', (e) => {
                const target = e.target;
                if (!(target instanceof HTMLDetailsElement)) return;
                if (!target.hasAttribute('data-collapsible')) return;
                if (!target.open) return;

                group.querySelectorAll<HTMLDetailsElement>('[data-collapsible]').forEach((item) => {
                    if (item !== target && item.open) item.open = false;
                });
            }, true);
        });
    }
}
