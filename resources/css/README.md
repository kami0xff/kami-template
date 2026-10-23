# Design System

One shared UI for every site built from this template. Vanilla CSS custom
properties + Blade components + a few TypeScript managers. No framework,
no build plugin, no lock-in.

**Living styleguide:** `/design` renders the whole kit. If you add or change
a component, add it there — it's the documentation *and* the regression test.

## Principles (adapted from Meta's Astryx)

- **A theme is only token overrides.** Sites never edit component CSS.
  Same structure, new look = new token values.
- **Clean by default, drama by opt-in.** The base kit is flat and simple.
  Glow, texture, and metallic effects live in `themes/` and effect variants
  (they are "specifications" you can enable anywhere, per site or per element).
- **Strong conventions over enforcement.** Every component follows the same
  anatomy, so once you've read one you can predict all of them — humans and
  AI agents alike.
- **Open internals.** Compositions assemble the same building blocks you use
  directly; nothing is hidden behind a closed API.

## Cascade layers (enforced, not by convention)

`app.css` declares the order once:

```css
@layer reset, tokens, themes, components, compositions, pages;
```

| Layer          | Contents                                            | May contain |
| -------------- | --------------------------------------------------- | ----------- |
| `reset`        | `tokens/reset.css`                                  | element rules |
| `tokens`       | `tokens/primitives.css`, `tokens/effects.css`, all `*.tokens.css` | **custom props only** |
| `themes`       | `themes/*.css` opt-in token-override specs          | custom props only |
| `components`   | one `*.css` per component folder                    | class rules reading its own `--component-*` tokens + globals |
| `compositions` | named assemblies for a context (`*/compositions/`)  | class rules |
| `pages`        | page-level layout                                   | class rules |

Later layers win. **Unlayered CSS beats all layers** — that is deliberate:
a site's own stylesheet overrides the system without fighting specificity.

## Color policy (audited against stripchat.com)

Stripchat's entire homepage uses ~4 chromatic colors — everything else is
grayscale. We enforce the same discipline:

1. **Neutrals** — the black/gray/silver/white ramps. 95% of the UI.
2. **Main colors (max 3)** — `--main1` (brand: CTAs, links, active states),
   `--main2` (highlight/money), `--main3` (cool accent). A re-skin usually
   only changes these three.
3. **System colors** — fixed vocabulary, never re-pointed per site:
   `--lime`, `--fuchsia`, `--azure`, `--neon-pink`, `--neon-purple`,
   `--red-bright`, `--orange-bright`. Used only through semantic roles
   (`--color-success`, `--color-danger`, `--color-live`, …) for statuses,
   badges, and dots.

If a component needs a color, it goes through a semantic role — never a
raw main/system value, and never a new hex.

System colors are deliberately **electric** (`#8aff00`, `#f000ff`,
`#00a3ff`, …) — they must cut through dark UI at 8px-dot size. Tint them
with `color-mix(in srgb, var(--color-x) N%, transparent)` for surfaces;
never introduce pastel variants.

### Form accent rule

Every interactive form state — focus border/ring, checked toggle/radio/
checkbox, selected tag, slider fill, pin focus — derives from one token:
`--form-accent` (defaults to `--color-primary`). Success/error states go
through `--color-success` / `--color-danger`. Re-theming the brand color
re-skins the entire form suite with zero component edits.

## Token rules

1. `tokens/primitives.css` is the **only** file allowed to contain literals
   (hex, rgba, px scales). Everything else derives from it.
2. Component tokens (`button/button.tokens.css`) map primitives to component
   roles, in three sections: **A) Settings** (colors/roles), **B) Layout**
   (geometry), **C) Effects** (shadows, gradients, motion).
3. Component CSS reads only its own tokens plus global semantics
   (`--text-muted`, `--surface1`, `--space*`, `--radius*`).

## Interaction layer (adapted from Astryx's component code)

`interaction/interaction.css` defines how every pressable control *feels*,
in one place, following the patterns in `facebook/astryx/packages/core/src`:

1. **Overlay hover/press** — instead of a hover color per variant, a
   translucent white layer is stacked on the control's own background:
   `background-image: linear-gradient(var(--overlay-hover), var(--overlay-hover))`.
   One token pair (`--overlay-hover` 6%, `--overlay-pressed` 12%) gives a
   consistent hover/press on any base color — primary, danger, gold, ghost.
   Applied to chrome controls (`.btn`, `.pill`, …) **and** dense rows
   (`.sidebar-link`, `.dropdown-item`, `.panel-list-item`, `.search-item`,
   `.menu-item`). Rows skip press-scale. Hover only fires under
   `@media (hover: hover)` so touch devices never get a stuck hover state.
   Component hover rules must use `background-color`, never the `background`
   shorthand, or they wipe the overlay.
2. **Press scale** — `:active { transform: scale(0.98) }` for tactile
   feedback, disabled under `prefers-reduced-motion`.
3. **Focus** — `:focus-visible` gets a `2px` accent outline offset by `3px`
   (`--focus-outline`, `--focus-outline-offset`). Destructive controls
   signal with `--color-danger` instead.
4. **Disabled** — flat `opacity: 0.5`, no overlay, no scale.

Other Astryx patterns adopted system-wide:

- **Motion tokens** — `--duration-fast` (175ms), `--duration-medium`
  (410ms) and the house easing `--ease-standard: cubic-bezier(0.24,1,0.4,1)`.
  A global `prefers-reduced-motion` rule in `tokens/reset.css` collapses
  all transitions/animations.
- **Semantic radius** — pick by surface role, not size: `--radius-inner`
  (nested bits) → `--radius-element` (controls) → `--radius-container`
  (12px — cards, popovers, modals, toasts, alerts) → `--radius-page`.
- **Elevation rim** — every `--shadow*` step carries a 1px white inset rim
  ("lit from above"). On a dark canvas the rim lifts popovers/modals in a
  way drop shadows alone can't.
- **Inverted tooltips** — tooltip bg = text color, tooltip text = surface
  color: maximal contrast, unmistakably "chrome" not "content".
- **Touch inputs** — `@media (pointer: coarse)` keeps input text ≥ 1rem so
  iOS Safari doesn't auto-zoom on focus.

## Component anatomy

```
button/
  button.tokens.css   ← the component's vocabulary (Settings/Layout/Effects)
  button.css          ← structure + variants, reads only tokens
resources/views/components/button/index.blade.php   ← Blade shell (`<x-button>`)
resources/views/components/button/foo.blade.php     ← child (`<x-button.foo>`)
resources/ts/manager/ButtonManager.ts               ← behavior, if any (flat)
```

**Blade folders:** prefer `components/<domain>/index.blade.php` so the
public tag stays `<x-<domain>>`. Children live beside it as
`<domain>/<child>.blade.php` → `<x-<domain>.<child>>`. Avoid
`<domain>/<domain>.blade.php` (that forces the awkward
`<x-<domain>.<domain>>` tag).

**CSS folders:** mirror the domain name under `resources/css/<domain>/`.
Shared families may nest (`css/chip/broadcaster-chip/`, `css/menu/`).

**TypeScript:** managers stay flat in `resources/ts/manager/` and are
named after the domain (`StoriesManager`, `CarouselManager`, …). Boot
them from `resources/ts/app.ts`.

Adding a component checklist:

1. Create the CSS folder + `*.tokens.css` + `*.css`.
2. Import both in `app.css` (tokens → `layer(tokens)`, css → `layer(components)`).
3. Create the Blade component at `components/<domain>/index.blade.php` with `@props` documenting every option.
4. Add a manager under `resources/ts/manager/` only if behavior is needed; register it in `app.ts`.
5. Add it to the `/design` showcase page.
6. No literals outside primitives; no site/brand content in the component.

## Themes & specifications

A **specification** is any opt-in look layered on the clean default:

- **Theme file** (`themes/glow.css`, `themes/hhentai.css`, `themes/pornguru.css`) — token overrides
  that restyle whole regions or the whole palette. Import it in a site
  manifest: `@import '../themes/glow.css' layer(themes);` — or, for a single
  page, build it as its own Vite entry and load it after `app.css` (the
  layer order declared in `app.css` still applies).
  Palette re-skins (token-only, ~40 lines each):
  - `hhentai.css` — navy neutrals + azure `--main1`. Demo: `/hhentai`
  - `pornguru.css` — warm charcoal + coral `--main1`. Demo: `/pornguru-theme`
  `glow.css` is chrome drama (navbar texture / footer glow), not a palette swap.
- **Effect variant** — component-level opt-in (`card-primary-border-glow`,
  `corner-highlight-button`, `liquid-metal-button`, panel `border="frost"`).
  These ship in the base bundle but render nothing unless you ask for them.

Because every spec lives in this shared tree, a spec created while working
on one site is automatically available to every other site after a template
sync — never define one inside a site's own repo.

## Floating surfaces — the full family

| Family | CSS | Behavior | Use for |
| --- | --- | --- | --- |
| **Fixed panel** | `panel/*` | `PanelManager` (`window.panels`) | slide-in trays: notifications, chat, user menu |
| **Anchored panel** (popover) | `popover/popover.css` | `PopoverManager` (`window.popovers`) | content attached to a trigger; 6 placements, click or hover (`<x-panels.popover>`) |
| **Dropdown menu** | `popover` + `menu/menu.css` | same manager | action lists: `<x-dropdown.menu>` with `.item` / `.separator` |
| **Sub-sidebar** (drill-in) | `sidebar/sub-sidebar.css` | `SubSidebarManager` (`window.subSidebars`) | second-level contextual sidebar: `<x-sidebar.sub-trigger target="…">` opens `<x-sidebar.sub-sidebar id="…">`; renders beside the sidebar on desktop, covers it on mobile. Panels must be SIBLINGS of the `<x-sidebar.sidebar>` element |
| **Context menu** | `menu/context-menu.css` | `ContextMenuManager` | right-click menus: `data-context-menu="{id}"` + `<x-context-menu>` |
| **Modal** | `modal/*` | `ModalManager` (`window.modals`) | centered dialogs |
| **Confirm dialog** | modal CSS | `ConfirmDialogManager` (`window.dialogs`) | `await dialogs.confirm({title, danger})` → boolean |
| **Drawer / sheet** | `drawer/*` | `ModalManager` instance (`window.drawers`) | `<x-drawer side="right\|left\|bottom">` — mobile filters, carts |
| **Command palette** | `command-palette/*` | `CommandPaletteManager` (`window.commandPalette`) | ⌘K action search: `<x-command-palette :items>` |
| **Lightbox** | `lightbox/*` | `LightboxManager` (`window.lightbox`) | `a[data-lightbox="group"]` fullscreen galleries |

Tooltips (`tooltip/tooltip.css`) are pure CSS — put `data-tooltip="…"`
(optionally `data-tooltip-pos="bottom"`) on any element, no wrapper or JS.

### Search bar + suggestion panel

`search/*` + `SearchSuggestManager` — the stripchat-style navbar search:
pill bar (magnifier · input · tool buttons · action chip like "Magic
Search 3") with a sectioned dropdown of suggestions — collapsible promo
card, gradient-bordered Magic Search examples, and recent/popular rows.

```blade
<x-search placeholder="Find…" :filter="true">
    <x-slot:chip><x-search.chip count="3">Magic Search</x-search.chip></x-slot:chip>
    <x-slot:panel>
        <x-search.promo thumb="…">
            Magic Search trouve des shows…
            <span class="search-promo-accent">3 recherches restantes</span>
            — <a href="#">Devenez Suprême</a> pour un accès illimité.
        </x-search.promo>
        <x-search.examples>
            <x-search.example>Superbe rousse</x-search.example>
        </x-search.examples>
        <x-search.section title="Recherches récentes">
            <x-search.item :count="30">
                <x-slot:icon>…</x-slot:icon>
                Japonaises
                <x-slot:trailing><x-atom.flag code="jp" /></x-slot:trailing>
            </x-search.item>
        </x-search.section>
    </x-slot:panel>
</x-search>
```

Focus opens the panel; Esc / outside click closes; ↑↓ + Enter navigate;
`/` focuses the first search on the page. Promo chevron toggles
`[data-collapsed]`. The input dispatches bubbling `search:query`
CustomEvents (`detail: { query, root }`) — the app owns the data and
re-renders panel sections however it likes.

**Don't confuse with** `<x-sidebar.filters>` — that is the sidebar
username/platform/age filter form (`components/sidebar/filters.blade.php`),
not the navbar Magic Search bar.

Gotcha: leave whitespace between `</x-slot:name>` and following plain
text — `</x-slot:icon>French` compiles to the literal `@endslotFrench`.

### Multipart form (creator onboarding)

`<x-form.multipart>` + `MultipartFormManager` (`window.multiparts`) — multi-step
wizard with draft restore, debounced autosave, and checkpoint payloads for
email reminders when a creator abandons onboarding mid-flow.

```blade
<x-form.multipart
    id="creator-onboarding"
    checkpoint-url="/api/onboarding/checkpoint"
    storage-key="onboarding.creator"
>
    <x-slot:header>
        <h2>Creator onboarding</h2>
        <p>We’ll email you if you leave before finishing.</p>
    </x-slot:header>

    <x-form.multipart.panel step="profile" title="Profile" reminder="complete_profile">
        <x-form.field name="display_name" label="Display name" :required="true">
            <x-form.input name="display_name" required />
        </x-form.field>
    </x-form.multipart.panel>

    <x-form.multipart.panel step="payout" title="Payout" reminder="add_payout">
        …
    </x-form.multipart.panel>
</x-form.multipart>
```

Checkpoint JSON posted to `checkpoint-url` (and emitted as `multipart:checkpoint`):

```json
{
  "id": "creator-onboarding",
  "step": "verify",
  "reminder": "verify_identity",
  "index": 1,
  "total": 4,
  "progress": 35,
  "values": { "display_name": "…" },
  "completedSteps": ["profile"],
  "updatedAt": "2026-07-25T06:00:00.000Z",
  "reason": "save-exit"
}
```

`reason` is `autosave` | `step` | `save-exit` | `complete`. Use `reminder` +
`progress` on the backend to schedule drip emails. Demo mode (`:demo="true"`)
skips the network and keeps drafts in `localStorage` only.

Visual-only steppers remain `<x-form.steps>` / `<x-form.step>` for static UI.

### Media uploader (YouTube-style)

`<x-form.uploader>` + `UploadManager` (`window.uploads`) — drag files into
a dropzone; each file becomes a job with thumbnail, progress bar, status
(`Waiting → Uploading % → Processing → Complete`), cancel / retry / remove.

```blade
{{-- Demo (simulated progress) — use on /design --}}
<x-form.uploader name="content[]" :demo="true" accept="image/*,video/*" />

{{-- Production — XHR POST with upload progress events --}}
<x-form.uploader
    name="content[]"
    upload-url="/api/uploads"
    accept="video/*,image/*"
    :max-files="10"
    :max-size="524288000"
    :concurrency="2"
/>
```

Listen for `upload:complete` / `upload:error` on the root (or document).
Simple single-file picker without a queue remains `<x-form.dropzone>`.

### Third-party assets

- **Country flags** — [flag-icons](https://flagicons.lipis.dev/), served
  from `public/vendor/flag-icons` (copied by the npm `postinstall` script,
  linked in `layouts/app.blade.php`). Use `<x-atom.flag code="fr" />`
  (`:squared="true"` for 1:1).

## Astryx-adapted components

Components ported from Meta's Astryx catalog and re-expressed in this
system's token/Blade conventions (options, not defaults):

- **Feedback**: `<x-alert>` (inline, tinted from semantic roles),
  `<x-atom.skeleton>`, `<x-atom.spinner>`, `<x-atom.progress>`, `<x-empty-state>`
- **Display**: `<x-atom.badge>` (+ `.status-dot`), `<x-atom.kbd>`,
  `<x-atom.divider>`, `<x-stat>`, `<x-description-list>`, `<x-timestamp>`
  (auto-refreshing relative time), `<x-table>` (static; data-table stays for
  sortable data), `<x-thumbnail>` (ratio frame + overlay slots),
  `<x-user-avatar.avatar-group>`
- **Navigation**: `<x-breadcrumbs>`, `<x-pagination>` (plain numbers + `{page}`
  URL pattern), `<x-overflow-list>` (the scrolling category rail),
  `<x-tree>`/`<x-tree.item>` (native `<details>`, zero JS)
- **Actions**: `<x-atom.button.icon-button>` (square, aria-label + tooltip),
  `<x-atom.button.group>`, `<x-toolbar>`
- **Forms**: `<x-form.number>` (steppers via native stepUp/stepDown),
  `<x-form.uploader>` (dropzone + job queue + progress),
  `<x-form.multipart>` (multi-step + checkpoints for email reminders),
  `<x-form.calendar>` (month grid, single or range → hidden inputs +
  `calendar:change` event)
- **Directory**: `<x-directory>` with `.section` / `.group` / `.item` — every
  category and tag of a site in labelled columns, filtered by a search box and
  a Main/#/A–Z bar (`DirectoryManager`). Full page by default; `compact` inside
  `<x-modal.modal>` or `<x-drawer>` for the overlay.
- **Layout/content**: `<x-collapsible>`, `.resizable` split panes
  (`ResizableManager`), `.prose` long-form typography bundle

Everything is demoed on `/design`, which doubles as the regression test.

## Per-site consumption

Each app has a hand-authored manifest in `apps/` (see `apps/gaycam.css`):

```css
@import '../app.css';                                  /* the base system   */
@import '../themes/glow.css' layer(themes);            /* opt-in theme      */
@import '../sidebar/compositions/gaycam/sidebar.css' layer(compositions);
@import '../pages/about.css' layer(pages);             /* site page styles  */
```

## How this stays shared across all sites

The canonical source is **this template repo**. Every site keeps the
template as a git remote and merges periodically
(see "Keeping projects in sync" in the root README):

```bash
git fetch template && git merge template/main
```

Rule of thumb: **UI work happens here, site work happens in the site.**
New components, tokens, themes, and specifications are committed to the
template; sites receive them on the next merge. If the family of sites
outgrows this (~5+), graduate the system to a Composer package.
