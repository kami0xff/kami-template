@extends('layouts.app')

@section('title', 'Design System — Showcase')

@section('content')
<x-app-shell>
    <x-slot:subnav>
        <a href="{{ route('design') }}" class="subnavbar-link active">Components</a>
        <a href="#theme-lab" class="subnavbar-link">Theme</a>
        <a href="#forms" class="subnavbar-link">Forms</a>
        <a href="#overlays" class="subnavbar-link">Overlays</a>
        <a href="#mega-menu" class="subnavbar-link">Mega</a>
        <a href="#comments" class="subnavbar-link">Comments</a>
        <a href="#forum" class="subnavbar-link">Forum</a>
        <a href="#explore" class="subnavbar-link">Explore</a>
        <a href="#stories" class="subnavbar-link">Stories</a>
        <a href="#video" class="subnavbar-link">Video</a>
        <a href="#admin" class="subnavbar-link">Admin</a>
        <a href="#charts" class="subnavbar-link">Charts</a>
        <a href="#kit" class="subnavbar-link">Kit</a>
        <a href="#auth-guest" class="subnavbar-link">Auth</a>
        <a href="#reactions" class="subnavbar-link">Reactions</a>
        <a href="#creator-sections" class="subnavbar-link">Creator</a>
        <a href="#wallet" class="subnavbar-link">Wallet</a>
        <a href="#tipping" class="subnavbar-link">Tipping</a>
        <a href="#shader-hero" class="subnavbar-link">Shader</a>
        <a href="#infinite-scroll" class="subnavbar-link">Infinite</a>
        <a href="{{ route('payment') }}" class="subnavbar-link">Payment</a>
        <a href="#ai-chat" class="subnavbar-link">AI</a>
        <a href="{{ route('ai') }}" class="subnavbar-link">AI Studio</a>
        <a href="#chat" class="subnavbar-link">Chat</a>
        <a href="#tokens" class="subnavbar-link">Tokens</a>

        <x-mega-menu :hover="true" aria-label="Explore">
            <x-slot:trigger>
                <button type="button" class="subnavbar-link">
                    Explore
                    <svg class="mega-menu-trigger-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 9l6 6 6-6" />
                    </svg>
                </button>
            </x-slot:trigger>

            <x-mega-menu.section title="Browse" action-href="#" action-label="See all categories">
                <x-mega-menu.grid cols="4">
                    <x-mega-menu.column title="Live">
                        <x-mega-menu.link href="#">Popular now</x-mega-menu.link>
                        <x-mega-menu.link href="#">New faces</x-mega-menu.link>
                        <x-mega-menu.link href="#">Private shows</x-mega-menu.link>
                        <x-mega-menu.link href="#" hint="Hot">VR rooms</x-mega-menu.link>
                    </x-mega-menu.column>

                    <x-mega-menu.column title="Discover">
                        <x-mega-menu.link href="#">Recommended</x-mega-menu.link>
                        <x-mega-menu.link href="#">Trending tags</x-mega-menu.link>
                        <x-mega-menu.link href="#">Countries</x-mega-menu.link>
                        <x-mega-menu.link href="#">Studios</x-mega-menu.link>
                    </x-mega-menu.column>

                    <x-mega-menu.feature
                        class="mega-menu-span-2"
                        href="#"
                        badge="Live"
                        title="Hillary is online"
                        src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&h=450&fit=crop"
                        aspect="21 / 9"
                    >
                        Jump into the room — tip menu and private chat ready.
                    </x-mega-menu.feature>
                </x-mega-menu.grid>
            </x-mega-menu.section>

            <x-mega-menu.separator />

            <x-mega-menu.section title="Media">
                <x-mega-menu.grid cols="4">
                    <x-mega-menu.media
                        href="#"
                        src="https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=640&h=400&fit=crop"
                        caption="Summer campaign"
                        aspect="4 / 3"
                    />
                    <x-mega-menu.media
                        href="#"
                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=640&h=400&fit=crop"
                        caption="Creator spotlight"
                        aspect="4 / 3"
                    />
                    <x-mega-menu.video
                        class="mega-menu-span-2"
                        href="#"
                        src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                        poster="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=800&h=450&fit=crop"
                        caption="Promo reel"
                        aspect="16 / 9"
                    />
                </x-mega-menu.grid>
            </x-mega-menu.section>
        </x-mega-menu>
    </x-slot:subnav>

    <x-slot:sidebar>
        <x-sidebar.sidebar>
            <x-sidebar.group label="Menu">
                <x-sidebar.link href="#" icon="home" :active="true">Home</x-sidebar.link>
                <x-sidebar.link href="#" icon="feed">Feed</x-sidebar.link>
                <x-sidebar.link href="#" icon="star">Recommended</x-sidebar.link>
                <x-sidebar.link href="#" icon="heart">My Favorites</x-sidebar.link>
                <x-sidebar.link href="#" icon="bookmark">My Collection</x-sidebar.link>
                <x-sidebar.link href="#" icon="clock">Viewed</x-sidebar.link>
            </x-sidebar.group>

            <x-sidebar.group label="Specialties">
                <x-sidebar.link href="#" :counter="64">French</x-sidebar.link>
                <x-sidebar.link href="#" :counter="141">Ukrainian</x-sidebar.link>
                <x-sidebar.link href="#" :counter="1343">New Models</x-sidebar.link>
                <x-sidebar.link href="#" :counter="161">VR Cams</x-sidebar.link>
                <x-sidebar.link href="#" :counter="78">BDSM</x-sidebar.link>
            </x-sidebar.group>

            <x-sidebar.group label="For you">
                <x-sidebar.link href="#" :counter="3376">Big Tits</x-sidebar.link>
                <x-sidebar.link href="#" :counter="105">Japanese</x-sidebar.link>
            </x-sidebar.group>

            {{-- Drill-in trigger: opens the contextual sub-sidebar --}}
            <x-sidebar.group label="Browse">
                <x-sidebar.sub-trigger target="categories" icon="tag" :counter="1200">
                    All Categories
                </x-sidebar.sub-trigger>
                <x-sidebar.sub-trigger target="countries" icon="globe">
                    By Country
                </x-sidebar.sub-trigger>

                {{-- In-place dropdown: expands inside the sidebar --}}
                <x-sidebar.dropdown id="demo-tags">
                    <x-slot:trigger>
                        <x-sidebar.dropdown-trigger icon="bookmark">
                            Popular Tags
                        </x-sidebar.dropdown-trigger>
                    </x-slot:trigger>
                    <x-sidebar.dropdown-group title="Trending">
                        <x-sidebar.dropdown-item>#new-faces</x-sidebar.dropdown-item>
                        <x-sidebar.dropdown-item>#interactive-toy</x-sidebar.dropdown-item>
                        <x-sidebar.dropdown-item>#couples</x-sidebar.dropdown-item>
                    </x-sidebar.dropdown-group>
                </x-sidebar.dropdown>
            </x-sidebar.group>

            <x-atom.pill variant="outline" style="width:100%; justify-content:center;">Categories +</x-atom.pill>
        </x-sidebar.sidebar>

        {{-- Sub-sidebars: siblings of the sidebar, one per drill-in target --}}
        <x-sidebar.sub-sidebar id="categories" title="Categories">
            <x-sidebar.group label="Appearance">
                <x-sidebar.link href="#" :counter="3376">Big Tits</x-sidebar.link>
                <x-sidebar.link href="#" :counter="921">Petite</x-sidebar.link>
                <x-sidebar.link href="#" :counter="1204">Curvy</x-sidebar.link>
                <x-sidebar.link href="#" :counter="644">Tattoos</x-sidebar.link>
            </x-sidebar.group>
            <x-sidebar.group label="Activity">
                <x-sidebar.link href="#" :counter="78">BDSM</x-sidebar.link>
                <x-sidebar.link href="#" :counter="412">Dancing</x-sidebar.link>
                <x-sidebar.link href="#" :counter="230">Roleplay</x-sidebar.link>
            </x-sidebar.group>
        </x-sidebar.sub-sidebar>

        <x-sidebar.sub-sidebar id="countries" title="By Country">
            <x-sidebar.group label="Popular">
                <x-sidebar.link href="#" :counter="64">
                    <x-slot:icon><x-atom.flag code="fr" /></x-slot:icon>
                    French
                </x-sidebar.link>
                <x-sidebar.link href="#" :counter="141">
                    <x-slot:icon><x-atom.flag code="ua" /></x-slot:icon>
                    Ukrainian
                </x-sidebar.link>
                <x-sidebar.link href="#" :counter="105">
                    <x-slot:icon><x-atom.flag code="jp" /></x-slot:icon>
                    Japanese
                </x-sidebar.link>
                <x-sidebar.link href="#" :counter="88">
                    <x-slot:icon><x-atom.flag code="co" /></x-slot:icon>
                    Colombian
                </x-sidebar.link>
            </x-sidebar.group>
        </x-sidebar.sub-sidebar>
    </x-slot:sidebar>

    <x-slot:overlays>
        <x-modal.modal id="demo-modal" title="Example modal" maxWidth="440px">
            <p style="color:var(--text-muted); margin:0;">
                A default modal. Opened with <code>modals.open('demo-modal')</code>,
                closes on the × button, backdrop click, or Escape.
            </p>
            <x-slot:footer>
                <x-atom.button.button variant="muted" text="Cancel" onclick="modals.close('demo-modal')" />
                <x-atom.button.button variant="primary" text="Confirm" onclick="modals.close('demo-modal'); toasts.success('Confirmed.')" />
            </x-slot:footer>
        </x-modal.modal>

        <x-drawer id="demo-drawer" side="right" title="Filters">
            <div style="display:grid; gap:var(--space3);">
                <p style="color:var(--text-muted); font-size:var(--text-lead);">A right-side drawer for mobile filters, carts, or settings.</p>
                <x-form.number name="demo_age" :value="25" :min="18" :max="99" />
                <x-atom.button.button variant="primary" text="Apply" onclick="drawers.close('demo-drawer')" />
            </div>
        </x-drawer>

        <x-drawer id="demo-sheet" side="bottom" title="Bottom sheet">
            <p style="color:var(--text-muted); font-size:var(--text-lead);">Slides up from the bottom — the mobile-native pattern for quick pickers.</p>
        </x-drawer>

        <x-drawer id="demo-drawer-float" side="right" variant="float" title="Messages">
            <div style="display:grid; gap:var(--space3);">
                <p style="color:var(--text-muted); font-size:var(--text-lead);">Float variant — fades in, inset from the edges, rounded with a soft silver rim. Default drawers still slide from the edge.</p>
                <x-atom.button.button variant="primary" text="Close" onclick="drawers.close('demo-drawer-float')" />
            </div>
        </x-drawer>

        <x-drawer id="demo-sheet-float" side="bottom" variant="float" title="Quick actions">
            <p style="color:var(--text-muted); font-size:var(--text-lead);">Bottom float sheet — same fade + inset treatment.</p>
        </x-drawer>

        <x-context-menu id="demo-context">
            <x-dropdown.item>Open in lightbox</x-dropdown.item>
            <x-dropdown.item>Save photo</x-dropdown.item>
            <x-dropdown.separator />
            <x-dropdown.item :danger="true">Report</x-dropdown.item>
        </x-context-menu>

        <x-command-palette :items="[
            ['label' => 'Home', 'href' => '#', 'hint' => 'G H'],
            ['label' => 'Design system', 'href' => '#', 'keywords' => 'styleguide components'],
            ['label' => 'Buy tokens', 'href' => '/payment', 'keywords' => 'payment credits money wallet'],
            ['label' => 'My favorites', 'href' => '#', 'keywords' => 'models follow'],
            ['label' => 'Settings', 'href' => '#', 'hint' => 'G S'],
        ]" />

        <x-modal.payment.buy-tokens />
    </x-slot:overlays>

    <div class="container page-section">
        <div class="page-title-bar">
            <h1 class="page-title-text">Design System</h1>
        </div>
        <p class="page-stats">The default kit — tokens → component tokens → components. Override tokens per site, never component CSS.</p>

        {{-- ============================== Shader hero ============================== --}}
        <section class="favorites-section" id="shader-hero" data-shader-hero-picker="#design-shader-hero">
            <div class="theme-lab-section-head" style="margin-bottom:16px;">
                <h2>Shader hero</h2>
                <p>
                    shaders.com-style GPU backdrops for marketing landings — with crawlable HTML on top.
                    Full-bleed page: <a href="{{ route('shader-hero') }}" style="color:var(--color-primary);">/shader-hero</a>.
                </p>
            </div>

            <div class="shader-hero-lab">
                <div class="shader-hero-lab-seo">
                    <h3>How this stays SEO-friendly</h3>
                    <ol>
                        <li><strong>Copy is real HTML</strong> — the heading, lede, and CTAs are in the DOM. Crawlers and screen readers never need the canvas.</li>
                        <li><strong>Canvas is decoration</strong> — wrapped in <code>aria-hidden</code>. We never paint text into WebGL.</li>
                        <li><strong>Progressive enhancement</strong> — CSS gradient shows first; WebGL fades in when ready. No JS / no WebGL / <code>prefers-reduced-motion</code> → static CSS only.</li>
                        <li><strong>Page SEO stays on the page</strong> — use <code>@@section('title')</code>, <code>meta_description</code>, OG image, and optional <code>&lt;x-seo.schema&gt;</code>. The shader does not replace those.</li>
                        <li><strong>Perf for Core Web Vitals</strong> — animation pauses off-screen; DPR capped; low-power WebGL context.</li>
                    </ol>
                </div>

                <div class="theme-lab-label">Variants</div>
                <div class="shader-hero-lab-picks" role="group" aria-label="Shader variants">
                    <button type="button" class="shader-hero-lab-pick is-active" data-shader-variant="chrome" aria-pressed="true">Chrome</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="glow" aria-pressed="false">Glow</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="studio" aria-pressed="false">Studio</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="aurora" aria-pressed="false">Aurora</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="plasma" aria-pressed="false">Plasma</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="mesh" aria-pressed="false">Mesh</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="silk" aria-pressed="false">Silk</button>
                    <button type="button" class="shader-hero-lab-pick" data-shader-variant="ember" aria-pressed="false">Ember</button>
                </div>

                <div class="shader-hero-lab-grid" aria-hidden="true">
                    <div class="shader-hero-lab-swatch" data-variant="chrome">Chrome</div>
                    <div class="shader-hero-lab-swatch" data-variant="glow">Glow</div>
                    <div class="shader-hero-lab-swatch" data-variant="studio">Studio</div>
                    <div class="shader-hero-lab-swatch" data-variant="aurora">Aurora</div>
                    <div class="shader-hero-lab-swatch" data-variant="plasma">Plasma</div>
                    <div class="shader-hero-lab-swatch" data-variant="mesh">Mesh</div>
                    <div class="shader-hero-lab-swatch" data-variant="silk">Silk</div>
                    <div class="shader-hero-lab-swatch" data-variant="ember">Ember</div>
                </div>

                <div class="shader-hero-lab-frame">
                    <x-shader-hero
                        id="design-shader-hero"
                        heading-level="2"
                        variant="chrome"
                        align="center"
                        eyebrow="Liquid chrome"
                        title="Full home automation <em>made agentic.</em>"
                        lede="Flat WebGL backdrop — SDF chrome tiles with blue/amber rim light and bloom. Real HTML on top, no 3D mesh."
                        primary-href="{{ route('shader-hero') }}"
                        primary-label="Open full demo"
                        secondary-href="#theme-lab"
                        secondary-label="Theme lab"
                        note="x-shader-hero variant=chrome"
                        style="--shader-hero-min-h: min(72vh, 620px);"
                    />
                </div>
            </div>
        </section>

        {{-- ============================== Theme lab (preview only) ============================== --}}
        <section class="favorites-section theme-lab-section" id="theme-lab" data-theme-lab>
            <div class="theme-lab-section-head">
                <h2>Theme lab</h2>
                <p>
                    Preview other brand colors on this page only. Overrides live CSS variables — nothing in the design system source changes.
                    Stored in <code>sessionStorage</code> until you reset or close the tab.
                    For a full agency domain pack (fonts, square edges, high-fidelity pages), see
                    <a href="{{ route('caumont') }}" style="color:var(--color-primary);">/caumont</a>.
                </p>
            </div>

            <div class="theme-lab-label">Presets</div>
            <div class="theme-lab-presets" role="group" aria-label="Brand presets">
                <button type="button" class="theme-lab-preset is-active" data-theme-lab-action="preset" data-theme-lab-preset="default">
                    <span class="theme-lab-preset-dot" style="background:#fa5365"></span> Default
                </button>
                <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="livestream">
                    <span class="theme-lab-preset-dot" style="background:#e44545"></span> Livestream
                </button>
                <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="social">
                    <span class="theme-lab-preset-dot" style="background:#ff0f7b"></span> Social
                </button>
                <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="realestate">
                    <span class="theme-lab-preset-dot" style="background:#2a9d8f"></span> Real estate
                </button>
                <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="ecommerce">
                    <span class="theme-lab-preset-dot" style="background:#4361ee"></span> Ecommerce
                </button>
                <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="azure">
                    <span class="theme-lab-preset-dot" style="background:#3aa0ff"></span> Azure
                </button>
                <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="coral">
                    <span class="theme-lab-preset-dot" style="background:#ff6b4a"></span> Coral
                </button>
            </div>

            <div class="theme-lab-colors" style="margin-top:14px;">
                <label class="theme-lab-color">
                    <span class="theme-lab-label">Primary</span>
                    <span class="theme-lab-color-row">
                        <input type="color" data-theme-lab-color="primary" value="#fa5365" aria-label="Primary color" />
                        <input type="text" data-theme-lab-color="primary" value="#fa5365" spellcheck="false" aria-label="Primary hex" />
                    </span>
                </label>
                <label class="theme-lab-color">
                    <span class="theme-lab-label">Money</span>
                    <span class="theme-lab-color-row">
                        <input type="color" data-theme-lab-color="money" value="#70b042" aria-label="Money color" />
                        <input type="text" data-theme-lab-color="money" value="#70b042" spellcheck="false" aria-label="Money hex" />
                    </span>
                </label>
                <label class="theme-lab-color">
                    <span class="theme-lab-label">Live</span>
                    <span class="theme-lab-color-row">
                        <input type="color" data-theme-lab-color="live" value="#70b042" aria-label="Live color" />
                        <input type="text" data-theme-lab-color="live" value="#70b042" spellcheck="false" aria-label="Live hex" />
                    </span>
                </label>
            </div>

            <div class="theme-lab-actions" style="margin-top:12px;">
                <button type="button" class="theme-lab-reset" data-theme-lab-action="reset">Reset to default</button>
            </div>

            {{-- Sticky mini dock while scrolling the showcase --}}
            <div class="theme-lab-dock" data-theme-lab-dock data-collapsed="true">
                <div class="theme-lab-bar">
                    <button type="button" class="theme-lab-toggle" data-theme-lab-action="toggle" aria-label="Toggle theme lab">
                        <span class="theme-lab-swatch" aria-hidden="true"></span>
                        Theme
                    </button>
                    <span class="theme-lab-badge" data-theme-lab-badge>Default</span>
                </div>
                <div class="theme-lab-body">
                    <div class="theme-lab-label">Quick presets</div>
                    <div class="theme-lab-presets">
                        <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="default">Default</button>
                        <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="livestream">Live</button>
                        <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="realestate">Estate</button>
                        <button type="button" class="theme-lab-preset" data-theme-lab-action="preset" data-theme-lab-preset="ecommerce">Shop</button>
                    </div>
                    <div class="theme-lab-actions">
                        <button type="button" data-theme-lab-action="toggle">Collapse</button>
                        <button type="button" class="theme-lab-reset" data-theme-lab-action="reset">Reset</button>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================== Buttons ============================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Buttons</h2></div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center;">
                <x-atom.button.button variant="primary" text="Primary" />
                <x-atom.button.button variant="danger" text="Danger" />
                <x-atom.button.button variant="success" text="Success" />
                <x-atom.button.button variant="warning" text="Warning" />
                <x-atom.button.button variant="muted" text="Muted" />
                <x-atom.button.button variant="back" text="Back" />
                <x-atom.button.button variant="money" text="Buy Tokens" />
                <x-atom.button.button variant="light" text="Sign In" />
                <x-atom.button.button variant="primary" text="Disabled" :disabled="true" />
                <x-atom.button.button variant="primary" text="Loading" :loading="true" />
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center; margin-top:var(--space4);">
                <x-atom.button.button variant="primary" size="xs" text="XS" />
                <x-atom.button.button variant="primary" size="sm" text="Small" />
                <x-atom.button.button variant="primary" size="md" text="Medium" />
                <x-atom.button.button variant="primary" size="lg" text="Large" />
                <x-atom.button.button variant="primary" size="xl" text="XL" />
                <x-atom.button.button-corner-highlight />
                <x-atom.button.liquid-metal>Liquid metal</x-atom.button.liquid-metal>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center; margin-top:var(--space4);">
                <x-atom.button.icon-button label="Notifications" variant="muted">
                    <x-icons.bell />
                </x-atom.button.icon-button>
                <x-atom.button.icon-button label="Add to favorites" variant="primary">
                    <x-icons.heart />
                </x-atom.button.icon-button>
                <x-atom.button.icon-button label="Messages" variant="muted" size="sm">
                    <x-icons.message />
                </x-atom.button.icon-button>
                <x-atom.button.group>
                    <button class="btn btn-muted btn--group-active" type="button">Grid</button>
                    <button class="btn btn-muted" type="button">List</button>
                    <button class="btn btn-muted" type="button">Map</button>
                </x-atom.button.group>
                <x-toolbar aria-label="Demo toolbar">
                    <x-atom.button.icon-button label="Bell" variant="muted" size="sm" :tooltip="false"><x-icons.bell /></x-atom.button.icon-button>
                    <x-atom.button.icon-button label="Heart" variant="muted" size="sm" :tooltip="false"><x-icons.heart /></x-atom.button.icon-button>
                    <span class="toolbar-separator"></span>
                    <x-atom.button.icon-button label="User" variant="muted" size="sm" :tooltip="false"><x-icons.user /></x-atom.button.icon-button>
                </x-toolbar>

                {{-- Toggle buttons (Astryx ToggleButton) — aria-pressed drives the state --}}
                <button class="btn btn-muted" type="button" aria-pressed="true"
                    onclick="this.setAttribute('aria-pressed', this.getAttribute('aria-pressed') !== 'true')">HD only</button>
                <button class="btn btn-muted" type="button" aria-pressed="false"
                    onclick="this.setAttribute('aria-pressed', this.getAttribute('aria-pressed') !== 'true')">Autoplay</button>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space4); align-items:center; margin-top:var(--space4);">
                <x-link href="#">Primary link</x-link>
                <x-link href="#" variant="muted">Muted link</x-link>
            </div>
        </section>

        {{-- ============================== Pills ============================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Pills</h2></div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space2); align-items:center;">
                <x-atom.pill>Categories</x-atom.pill>
                <x-atom.pill :active="true">Favorites</x-atom.pill>
                <x-atom.pill variant="outline">Gallery</x-atom.pill>
                <x-atom.pill variant="accent">Top Models</x-atom.pill>
                <x-atom.pill variant="money">Buy Tokens</x-atom.pill>
                <x-atom.pill :dot="true" :count="9997">LIVE</x-atom.pill>
                <x-atom.pill :count="141">Ukrainian</x-atom.pill>
                <x-atom.pill size="sm">New</x-atom.pill>
                <x-atom.pill size="sm" variant="outline" :count="78">BDSM</x-atom.pill>
            </div>
        </section>

        {{-- ==================== Search & suggestions ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Search &amp; suggestions</h2></div>
            <div style="max-width:420px;">
                <x-search placeholder="Find what you are looking for…" :filter="true">
                    <x-slot:chip>
                        <x-search.chip count="3">Magic Search</x-search.chip>
                    </x-slot:chip>
                    <x-slot:panel>
                        <x-search.promo thumb="https://i.pravatar.cc/104?img=12">
                            Magic Search trouve des shows qui correspondent à vos penchants.
                            <span class="search-promo-accent">3 recherches restantes</span>
                            — <a href="#">Devenez Suprême</a> pour bénéficier d’un accès illimité.
                        </x-search.promo>

                        <x-search.examples>
                            <x-search.example>Superbe rousse</x-search.example>
                            <x-search.example>Une amatrice de jeux vidéo</x-search.example>
                            <x-search.example>Je cherche une fille asiatique</x-search.example>
                        </x-search.examples>

                        <x-search.section title="Recherches récentes">
                            <x-search.item>
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                </x-slot:icon>
                                blonde
                            </x-search.item>
                            <x-search.item :count="30">
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M20.6 12.4 12.4 20.6a2 2 0 0 1-2.8 0L3.4 14.4a2 2 0 0 1 0-2.8L11.6 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v5.99a2 2 0 0 1-.6 1.41z"/><circle cx="16.5" cy="7.5" r="1"/></svg>
                                </x-slot:icon>
                                Japonaises
                                <x-slot:trailing><x-atom.flag code="jp" /></x-slot:trailing>
                            </x-search.item>
                            <x-search.item>
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><rect x="3" y="8" width="18" height="10" rx="2"/><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/></svg>
                                </x-slot:icon>
                                « japa » dans le Menu pourboire
                            </x-search.item>
                            <x-search.item>
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l1.6 5L19 8.4l-5.4 1.4L12 15l-1.6-5.2L5 8.4l5.4-1.4L12 2z"/></svg>
                                </x-slot:icon>
                                Je cherche une fille asiatique
                            </x-search.item>
                        </x-search.section>

                        <x-search.section title="Populaires">
                            <x-search.item :count="1832">
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M20.6 12.4 12.4 20.6a2 2 0 0 1-2.8 0L3.4 14.4a2 2 0 0 1 0-2.8L11.6 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v5.99a2 2 0 0 1-.6 1.41z"/><circle cx="16.5" cy="7.5" r="1"/></svg>
                                </x-slot:icon>
                                Ados 18+
                            </x-search.item>
                            <x-search.item :count="3991">
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M20.6 12.4 12.4 20.6a2 2 0 0 1-2.8 0L3.4 14.4a2 2 0 0 1 0-2.8L11.6 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v5.99a2 2 0 0 1-.6 1.41z"/><circle cx="16.5" cy="7.5" r="1"/></svg>
                                </x-slot:icon>
                                MILF
                            </x-search.item>
                            <x-search.item :count="1199">
                                <x-slot:icon>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M20.6 12.4 12.4 20.6a2 2 0 0 1-2.8 0L3.4 14.4a2 2 0 0 1 0-2.8L11.6 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v5.99a2 2 0 0 1-.6 1.41z"/><circle cx="16.5" cy="7.5" r="1"/></svg>
                                </x-slot:icon>
                                Sextoy
                            </x-search.item>
                        </x-search.section>
                    </x-slot:panel>
                </x-search>
                <p style="margin-top:var(--space3); font-size:var(--text-small); color:var(--text-muted);">
                    Focus the bar (or press <x-atom.kbd>/</x-atom.kbd>) to open suggestions — <x-atom.kbd>↑</x-atom.kbd> <x-atom.kbd>↓</x-atom.kbd> navigate, <x-atom.kbd>↵</x-atom.kbd> selects. Promo chevron collapses the tip.
                </p>
            </div>
        </section>

        {{-- ==================== Anchored & dropdown panels ==================== --}}
        <section class="favorites-section" id="directory">
            <div class="favorites-header"><h2>Directory</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Every category and tag in labelled columns, filtered by search and A–Z. Inline below, or
                <a href="#" onclick="modals.open('demo-directory'); return false;">open it as an overlay</a>.
            </p>
            @php
                $demoDirectory = [
                    'Age' => ['Teen 18+' => '1,654', 'Young 22+' => '4,390', 'MILF' => '1,848', 'Mature' => '253', 'Granny' => '57'],
                    'Ethnicity' => ['Arab' => '118', 'Asian' => '1,050', 'Ebony' => '1,215', 'Indian' => '368', 'Latina' => '2,917', 'White' => '2,683'],
                    'Body type' => ['Skinny' => '3,002', 'Athletic' => '766', 'Curvy' => '1,733', 'BBW' => '360'],
                    'Hair' => ['Blonde' => '1,393', 'Black' => '2,709', 'Brunette' => '3,649', 'Redhead' => '354'],
                ];
                $demoActivities = ['69 Position' => '1,333', 'Ahegao' => '4,764', 'Anal' => '3,067', 'Blowjob' => '6,047', 'Cosplay' => '1,435', 'Dirty Talk' => '6,209', 'Doggy Style' => '6,878', 'Erotic Dance' => '6,512', 'Fingering' => '6,809', 'Glory Hole' => '319', 'Handjob' => '3,972', 'Massage' => '3,029', 'Oil Show' => '5,827', 'Role Play' => '4,133', 'Shower' => '2,288', 'Striptease' => '6,151', 'Twerk' => '5,302', 'Yoga' => '1,799'];
            @endphp
            @include('pages.partials.design-directory', ['compact' => false])
            <x-modal.modal id="demo-directory" title="All categories" maxWidth="1100px">
                @include('pages.partials.design-directory', ['compact' => true])
            </x-modal.modal>
        </section>

        <section class="favorites-section" id="mega-menu">
            <div class="favorites-header"><h2>Mega menu</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Full-width desktop panel under the chrome — grids, link columns, images, video, separators, and featured promos.
                Hover <strong>Explore</strong> in the subheader, or open the click demo below.
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center; margin-bottom:var(--space4);">
                <x-mega-menu aria-label="Catalog mega menu">
                    <x-slot:trigger>
                        <x-atom.button.button variant="primary" text="Open mega menu ▾" />
                    </x-slot:trigger>

                    <x-mega-menu.grid cols="5">
                        <x-mega-menu.column title="Categories">
                            <x-mega-menu.link href="#">Girls</x-mega-menu.link>
                            <x-mega-menu.link href="#">Couples</x-mega-menu.link>
                            <x-mega-menu.link href="#">Men</x-mega-menu.link>
                            <x-mega-menu.link href="#">Trans</x-mega-menu.link>
                        </x-mega-menu.column>
                        <x-mega-menu.column title="Tags">
                            <x-mega-menu.link href="#">Interactive toy</x-mega-menu.link>
                            <x-mega-menu.link href="#">Outdoor</x-mega-menu.link>
                            <x-mega-menu.link href="#">Cosplay</x-mega-menu.link>
                            <x-mega-menu.link href="#">ASMR</x-mega-menu.link>
                        </x-mega-menu.column>
                        <x-mega-menu.column title="Regions">
                            <x-mega-menu.link href="#">Europe</x-mega-menu.link>
                            <x-mega-menu.link href="#">LatAm</x-mega-menu.link>
                            <x-mega-menu.link href="#">Asia</x-mega-menu.link>
                            <x-mega-menu.link href="#">North America</x-mega-menu.link>
                        </x-mega-menu.column>
                        <x-mega-menu.feature
                            class="mega-menu-span-2"
                            href="#"
                            badge="New"
                            title="Token weekend"
                            src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&h=450&fit=crop"
                        >
                            Double rewards on tip menus through Sunday night.
                        </x-mega-menu.feature>
                    </x-mega-menu.grid>

                    <x-mega-menu.separator />

                    <x-mega-menu.grid cols="3">
                        <x-mega-menu.media
                            href="#"
                            src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=640&h=360&fit=crop"
                            caption="Studio drop"
                        />
                        <x-mega-menu.media
                            href="#"
                            src="https://images.unsplash.com/photo-1488426862026-3ee34a7d66df?w=640&h=360&fit=crop"
                            caption="Behind the scenes"
                        />
                        <x-mega-menu.video
                            href="#"
                            src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                            poster="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=640&h=360&fit=crop"
                            caption="Weekly highlights"
                        />
                    </x-mega-menu.grid>
                </x-mega-menu>
            </div>
        </section>

        <section class="favorites-section">
            <div class="favorites-header"><h2>Anchored &amp; dropdown panels</h2></div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center;">

                <x-dropdown.menu placement="bottom-start">
                    <x-slot:trigger>
                        <x-atom.button.button variant="muted" text="Dropdown menu ▾" />
                    </x-slot:trigger>
                    <x-dropdown.item hint="⌘P">Profile</x-dropdown.item>
                    <x-dropdown.item href="#">Settings</x-dropdown.item>
                    <x-dropdown.item>Buy tokens</x-dropdown.item>
                    <x-dropdown.separator />
                    <x-dropdown.item :danger="true">Sign out</x-dropdown.item>
                </x-dropdown.menu>

                {{-- More menu (Astryx MoreMenu) — ⋯ icon button + dropdown --}}
                <x-dropdown.menu placement="bottom-end">
                    <x-slot:trigger>
                        <x-atom.button.icon-button label="More options" :tooltip="false">
                            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <circle cx="5" cy="12" r="2" /><circle cx="12" cy="12" r="2" /><circle cx="19" cy="12" r="2" />
                            </svg>
                        </x-atom.button.icon-button>
                    </x-slot:trigger>
                    <x-dropdown.item>Share</x-dropdown.item>
                    <x-dropdown.item>Copy link</x-dropdown.item>
                    <x-dropdown.separator />
                    <x-dropdown.item :danger="true">Report</x-dropdown.item>
                </x-dropdown.menu>

                <x-panels.popover placement="bottom-start" width="260px">
                    <x-slot:trigger>
                        <x-atom.button.button variant="muted" text="Anchored panel" />
                    </x-slot:trigger>
                    <strong style="display:block; margin-bottom:var(--space2);">Anchored panel</strong>
                    <p style="color:var(--text-muted); font-size:var(--text-lead); margin:0;">
                        Free-form popover content. Closes on outside click or <x-atom.kbd>Esc</x-atom.kbd>.
                    </p>
                </x-panels.popover>

                <x-panels.popover placement="bottom-start" :hover="true" width="240px">
                    <x-slot:trigger>
                        <x-atom.pill>Hover card</x-atom.pill>
                    </x-slot:trigger>
                    <div style="display:flex; gap:var(--space3); align-items:center;">
                        <span class="skeleton skeleton--circle" style="display:block; width:36px; height:36px;"></span>
                        <div>
                            <strong>kami_x</strong>
                            <div style="display:flex; align-items:center; gap:6px; color:var(--text-muted); font-size:var(--text-small);">
                                <x-atom.status-dot status="live" /> Live now
                            </div>
                        </div>
                    </div>
                </x-panels.popover>

                <button class="btn btn-muted" data-tooltip="Tooltips are pure CSS" type="button">Tooltip (hover)</button>
                <button class="btn btn-muted" data-tooltip="Positioned below" data-tooltip-pos="bottom" type="button">Tooltip below</button>
            </div>

            <div style="max-width:560px; margin-top:var(--space4); display:grid; gap:var(--space5);">
                <div>
                    <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">
                        Simple accordion — animated chevron, optional exclusive open.
                    </p>
                    <x-accordion exclusive>
                        <x-collapsible title="What is a collapsible?" :open="true">
                            A disclosure built on native <code>&lt;details&gt;</code>.
                            The chevron rotates on open; height animates where the browser supports it.
                        </x-collapsible>
                        <x-collapsible title="Can only one stay open?" meta="Exclusive mode">
                            Wrap items in <code>&lt;x-accordion exclusive&gt;</code> — opening one closes the others.
                        </x-collapsible>
                        <x-collapsible title="Keyboard friendly?">
                            Yes — focus the summary and press Enter or Space, same as a native disclosure.
                        </x-collapsible>
                    </x-accordion>
                </div>

                <div>
                    <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">
                        Panel variant — same surface as menus / chat settings.
                    </p>
                    <x-accordion exclusive>
                        <x-collapsible title="Account" meta="Profile & security" variant="panel" :open="true">
                            Email, password, and two-factor options live here.
                        </x-collapsible>
                        <x-collapsible title="Notifications" meta="Email & push" variant="panel">
                            Choose what you get pinged about — live follows, tips, messages.
                        </x-collapsible>
                        <x-collapsible title="Billing" meta="Tokens & invoices" variant="panel">
                            Packs, receipts, and saved payment methods.
                        </x-collapsible>
                    </x-accordion>
                </div>

                <div>
                    <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">
                        Ghost variant — flush list, good inside sidebars or mega menus.
                    </p>
                    <x-accordion>
                        <x-collapsible title="Categories" variant="ghost" :open="true">
                            Girls · Couples · Men · Trans
                        </x-collapsible>
                        <x-collapsible title="Tags" variant="ghost">
                            Interactive · Outdoor · Cosplay · ASMR
                        </x-collapsible>
                        <x-collapsible title="Regions" variant="ghost">
                            Europe · LatAm · Asia · North America
                        </x-collapsible>
                    </x-accordion>
                </div>
            </div>
        </section>

        {{-- ==================== Badges & indicators ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Badges &amp; indicators</h2></div>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space2); align-items:center; margin-bottom:var(--space4);">
                <x-atom.badge>Neutral</x-atom.badge>
                <x-atom.badge variant="primary">Primary</x-atom.badge>
                <x-atom.badge variant="success" dot="live">Live</x-atom.badge>
                <x-atom.badge variant="danger">Banned</x-atom.badge>
                <x-atom.badge variant="warning" dot="away">Away</x-atom.badge>
                <x-atom.badge variant="info">Beta</x-atom.badge>
                <x-atom.badge variant="money">+120 tk</x-atom.badge>
                <x-atom.kbd>⌘</x-atom.kbd><x-atom.kbd>K</x-atom.kbd>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space5); align-items:center; margin-bottom:var(--space4);">
                <x-atom.spinner size="sm" />
                <x-atom.spinner />
                <x-atom.spinner size="lg" style="color:var(--color-primary);" />
                <div style="flex:1; min-width:200px; display:grid; gap:var(--space2);">
                    <x-atom.progress :value="65" />
                    <x-atom.progress :value="90" variant="success" />
                    <x-atom.progress :value="25" variant="danger" />
                </div>
            </div>

            <div style="max-width:420px; display:grid; gap:var(--space2); margin-bottom:var(--space4);">
                <div style="display:flex; gap:var(--space3); align-items:center;">
                    <x-atom.skeleton variant="circle" width="40px" height="40px" />
                    <div style="flex:1; display:grid; gap:var(--space2);">
                        <x-atom.skeleton width="60%" />
                        <x-atom.skeleton width="90%" />
                    </div>
                </div>
                <x-atom.skeleton variant="rect" height="80px" />
            </div>

            <div style="display:grid; gap:var(--space3); max-width:640px;">
                <x-breadcrumbs :items="[
                    ['label' => 'Home', 'href' => '#'],
                    ['label' => 'Categories', 'href' => '#'],
                    ['label' => 'Ukrainian'],
                ]" />
                <x-breadcrumbs separator="chevron" :items="[
                    ['label' => 'Home', 'href' => '#'],
                    ['label' => 'Browse', 'href' => '#'],
                    ['label' => 'Europe', 'href' => '#'],
                    ['label' => 'Ukraine', 'href' => '#'],
                    ['label' => 'Kyiv'],
                ]" />
                <x-breadcrumbs separator="chevron" :max-visible="4" :items="[
                    ['label' => 'Home', 'href' => '#'],
                    ['label' => 'Library', 'href' => '#'],
                    ['label' => 'Collections', 'href' => '#'],
                    ['label' => '2024', 'href' => '#'],
                    ['label' => 'Summer', 'href' => '#'],
                    ['label' => 'Highlights'],
                ]" />
            </div>
        </section>

        {{-- ==================== Alerts & empty states ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Alerts &amp; empty states</h2></div>
            <div style="display:grid; gap:var(--space3); max-width:640px;">
                <x-alert variant="info">Tokens purchased here are usable on every partner site.</x-alert>
                <x-alert variant="success" title="Payout sent">Your weekly payout of 1,240 tk is on its way.</x-alert>
                <x-alert variant="warning" title="Verification pending" :dismissible="true">Your documents are being reviewed. Payouts unlock once approved.</x-alert>
                <x-alert variant="danger" title="Card declined">Your bank rejected the charge. Try another payment method.</x-alert>
            </div>
            <div style="border:1px dashed var(--border-default); border-radius:var(--radius2); margin-top:var(--space4); max-width:640px;">
                <x-empty-state title="No favorites yet" text="Models you follow will show up here so you never miss them live.">
                    <x-slot:icon><x-icons.heart /></x-slot:icon>
                    <x-atom.button.button variant="primary" text="Browse models" />
                </x-empty-state>
            </div>
        </section>

        {{-- ==================== Overflow rail & pagination ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Overflow rail &amp; pagination</h2></div>
            <x-overflow-list style="max-width:640px;">
                @foreach(['Categories', 'Favorites', 'New Models', 'VR Cams', 'Ukrainian', 'French', 'Big Tits', 'BDSM', 'Couples', 'Japanese', 'Fetish', 'Anal', 'Teen 18+', 'Mature', 'Ebony', 'Latina', 'Asian'] as $i => $cat)
                    <x-atom.pill :active="$i === 0">{{ $cat }}</x-atom.pill>
                @endforeach
            </x-overflow-list>

            <div style="display:grid; gap:var(--space4); margin-top:var(--space4); max-width:720px;">
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Pill (default)</p>
                    <x-pagination :current="4" :last="12" url="#page-{page}" />
                </div>
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Boxed</p>
                    <x-pagination variant="boxed" :current="4" :last="12" url="#page-{page}" />
                </div>
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Simple · Compact</p>
                    <div style="display:flex; flex-wrap:wrap; gap:var(--space4); align-items:center;">
                        <x-pagination variant="simple" :current="3" :last="20" url="#page-{page}" />
                        <x-pagination variant="compact" :current="2" :last="8" url="#page-{page}" />
                    </div>
                </div>
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Table bar — meta + rows selector</p>
                    <x-pagination.bar
                        :current="4"
                        :last="12"
                        :total="284"
                        :per-page="24"
                        :per-page-options="[12, 24, 48, 96]"
                        url="#page-{page}"
                        per-page-url="#per={per}"
                        variant="boxed"
                        size="sm"
                    />
                </div>
            </div>
        </section>

        {{-- ==================== Infinite scroll ==================== --}}
        <section class="favorites-section" id="infinite-scroll">
            <div class="favorites-header"><h2>Infinite scroll</h2></div>
            <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space4); max-width:640px;">
                Feed-friendly alternative to pagination. Auto mode uses an
                <code>IntersectionObserver</code> sentinel; <code>prefers-reduced-motion</code> falls back to the Load more button.
                Listen for <code>infinite-scroll:load</code>, or set <code>url</code> with a <code>{page}</code> placeholder.
            </p>

            <div style="display:grid; gap:var(--space5); max-width:720px;">
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Auto · scroll the panel</p>
                    <div style="max-height:320px; overflow:auto; border:1px solid var(--white-alpha-120); border-radius:var(--radius-element); padding:var(--space3);">
                        <x-infinite-scroll
                            class="infinite-scroll--demo"
                            data-demo
                            data-demo-max="4"
                            :page="1"
                            :has-more="true"
                            mode="auto"
                            aria-label="Demo feed"
                            style="--infinite-scroll-gap: var(--space2);"
                        >
                            @for ($i = 1; $i <= 6; $i++)
                                <article class="infinite-scroll-demo-card">
                                    <strong>Feed item {{ $i }}</strong>
                                    <span>Initial page</span>
                                </article>
                            @endfor
                        </x-infinite-scroll>
                    </div>
                </div>

                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Button-only</p>
                    <x-infinite-scroll
                        class="infinite-scroll--demo"
                        data-demo
                        data-demo-max="3"
                        mode="button"
                        button-label="Load more posts"
                        :page="1"
                        :has-more="true"
                        aria-label="Button feed"
                        style="--infinite-scroll-gap: var(--space2);"
                    >
                        @for ($i = 1; $i <= 3; $i++)
                            <article class="infinite-scroll-demo-card">
                                <strong>Post {{ $i }}</strong>
                                <span>Tap Load more</span>
                            </article>
                        @endfor
                    </x-infinite-scroll>
                </div>
            </div>
        </section>

        {{-- ==================== Drawers, confirms & command palette ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Drawers, confirms &amp; command palette</h2></div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center;">
                <x-atom.button.button variant="muted" text="Right drawer" onclick="drawers.open('demo-drawer')" />
                <x-atom.button.button variant="muted" text="Bottom sheet" onclick="drawers.open('demo-sheet')" />
                <x-atom.button.button variant="muted" text="Float drawer" onclick="drawers.open('demo-drawer-float')" />
                <x-atom.button.button variant="muted" text="Float sheet" onclick="drawers.open('demo-sheet-float')" />
                <x-atom.button.button variant="danger" text="Confirm dialog"
                    onclick="dialogs.confirm({title: 'Delete this photo?', message: 'This cannot be undone.', confirmText: 'Delete', danger: true}).then(ok => ok && toasts.success('The photo is gone.', { title: 'Deleted' }))" />
                <x-atom.button.button variant="muted" text="Command palette (⌘K)" onclick="commandPalette.open()" />
            </div>
        </section>

        {{-- ==================== Media — thumbnails, carousel, lightbox ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Media — thumbnails, carousel &amp; lightbox</h2></div>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:var(--space3); max-width:640px;" data-context-menu="demo-context">
                @foreach([11, 22, 33] as $i => $seed)
                    <a href="https://picsum.photos/seed/{{ $seed }}/1200/675" data-lightbox="demo" data-caption="Demo photo {{ $i + 1 }} — right-click for the context menu">
                        <x-thumbnail src="https://picsum.photos/seed/{{ $seed }}/480/270">
                            <x-slot:overlays>
                                @if($i === 0)
                                    <span class="thumbnail-overlay thumbnail-overlay--tl"><x-atom.status-dot status="live" /> LIVE</span>
                                @endif
                                <span class="thumbnail-overlay thumbnail-overlay--br">1{{ $i }}:3{{ $i }}</span>
                            </x-slot:overlays>
                        </x-thumbnail>
                    </a>
                @endforeach
            </div>
            <p style="color:var(--text-faint); font-size:var(--text-small); margin-top:var(--space2);">
                Click a thumbnail for the lightbox · right-click the grid for the context menu
            </p>
            <div style="max-width:480px; margin-top:var(--space4);">
                <x-carousel :dots="true" full>
                    @foreach([44, 55, 66] as $seed)
                        <img src="https://picsum.photos/seed/{{ $seed }}/960/400" alt="" style="border-radius:var(--radius2);">
                    @endforeach
                </x-carousel>
            </div>
        </section>

        {{-- ==================== Video player ==================== --}}
        <section class="favorites-section" id="video">
            <div class="favorites-header"><h2>Video player</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space3);">
                Custom HTML5 player — play/seek/volume, speed, PiP, fullscreen. Keyboard: Space, ←/→, M, F.
                Slot accepts <code>x-media.lock</code> for paywalled clips.
            </p>

            <div style="display:grid; gap:var(--space4); max-width:720px;">
                <x-video-player
                    src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                    poster="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.jpg"
                    title="Studio clip"
                />

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space3);">
                    <x-video-player
                        src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                        poster="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.jpg"
                        title="Locked preview"
                    >
                        <x-media.lock price="80" label="Unlock video" />
                    </x-video-player>

                    <x-video-player
                        src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                        poster="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.jpg"
                        title="Live replay"
                        :live="true"
                        ratio="portrait"
                    />
                </div>
            </div>
        </section>

        {{-- ==================== Data display ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Stats, description list &amp; static table</h2></div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space7); margin-bottom:var(--space5);">
                <x-stat value="12,480 tk" label="Earnings this week" delta="+18%" direction="up" />
                <x-stat value="342" label="New followers" delta="+4%" direction="up" />
                <x-stat value="97.2%" label="Uptime" />
                <x-stat value="1,204" label="Viewers now" delta="−7%" direction="down" />
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:var(--space5); max-width:840px;">
                <x-description-list :items="[
                    'Username' => 'kami_x',
                    'Country' => 'Ukraine',
                    'Languages' => 'English, Ukrainian',
                    'Member since' => 'March 2024',
                ]" />
                <div>
                    <x-table striped hover>
                        <thead><tr><th>Pack</th><th>Tokens</th><th>Price</th></tr></thead>
                        <tbody>
                            <tr><td>Starter</td><td>100</td><td>$9.99</td></tr>
                            <tr><td>Popular</td><td>550</td><td>$49.99</td></tr>
                            <tr><td>Best value</td><td>1,200</td><td>$99.99</td></tr>
                        </tbody>
                    </x-table>
                    <p style="color:var(--text-muted); font-size:var(--text-small); margin-top:var(--space2);">
                        Updated <x-timestamp :datetime="now()->subMinutes(3)->toIso8601String()" />
                    </p>
                </div>
            </div>
        </section>

        {{-- ==================== Structure — tree, calendar, resizable ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Tree, calendar &amp; resizable</h2></div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:var(--space5); align-items:start;">
                <x-tree style="max-width:260px;">
                    <x-tree.item label="Categories" :open="true">
                        <x-tree.item label="Fetish" href="#" />
                        <x-tree.item label="Couples" href="#" />
                        <x-tree.item label="VR" :open="false">
                            <x-tree.item label="180°" href="#" />
                            <x-tree.item label="360°" href="#" />
                        </x-tree.item>
                    </x-tree.item>
                    <x-tree.item label="Studios" href="#" />
                    <x-tree.item label="Settings" href="#" />
                </x-tree>
                <x-form.calendar name="demo_period" mode="range" />
            </div>
            <div class="resizable" data-resizable style="margin-top:var(--space5); max-width:640px; height:120px; border:1px solid var(--border-default); border-radius:var(--radius2);">
                <div class="resizable-pane" style="flex-basis:55%; display:grid; place-items:center; color:var(--text-muted); font-size:var(--text-small);" data-min="140">Drag the handle →</div>
                <div class="resizable-handle" data-resizable-handle></div>
                <div class="resizable-pane" style="display:grid; place-items:center; color:var(--text-muted); font-size:var(--text-small);">Second pane</div>
            </div>
        </section>

        {{-- ==================== Prose ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Prose — long-form content</h2></div>
            <div class="prose">
                <h2>How token payouts work</h2>
                <p>Earnings are calculated <strong>per token</strong> and settled weekly. The payout schedule depends on your <a href="#">studio agreement</a> and the payment method on file.</p>
                <blockquote>Payouts sent before Tuesday midnight arrive the same week.</blockquote>
                <ul>
                    <li>Minimum payout is <code>500 tk</code></li>
                    <li>Fees vary by method</li>
                </ul>
            </div>
        </section>

        {{-- ============================== Forms ============================== --}}
        <section class="favorites-section" id="forms">
            <div class="favorites-header"><h2>Form controls</h2></div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space4); max-width:820px;">
                <div>
                    <label class="form-label" for="demo-name">Text input</label>
                    <x-form.input name="demo-name" placeholder="e.g. kami" />
                </div>
                <div>
                    <label class="form-label" for="demo-email">Email</label>
                    <x-form.email name="demo-email" placeholder="you@studio.com" />
                </div>
                <div>
                    <label class="form-label" for="demo-phone">Phone</label>
                    <x-form.phone name="demo-phone" dial="+33" />
                </div>
                <div>
                    <label class="form-label" for="demo-password">Password</label>
                    <x-form.input type="password" name="demo-password" placeholder="••••••••" />
                </div>
                <div>
                    <label class="form-label" for="demo-select">Select</label>
                    <x-form.select name="demo-select" placeholder="Pick one…" :options="['a' => 'Option A', 'b' => 'Option B', 'c' => 'Option C']" />
                </div>
                <div>
                    <label class="form-label">Selector (searchable)</label>
                    <x-form.selector
                        name="demo-selector"
                        placeholder="Pick a niche…"
                        :options="['asmr' => 'ASMR', 'gaming' => 'Gaming', 'couples' => 'Couples', 'fetish' => 'Fetish', 'yoga' => 'Yoga']"
                    />
                </div>
                <div>
                    <label class="form-label">Country (search + flags)</label>
                    <x-form.country name="demo-country" selected="FR" />
                </div>
                <div>
                    <label class="form-label" for="demo-textarea">Textarea</label>
                    <x-form.textarea name="demo-textarea" placeholder="Write something…" :rows="3" />
                </div>
                <div>
                    <label class="form-label" for="demo_tokens">Number (steppers)</label>
                    <br>
                    <x-form.number name="demo_tokens" :value="100" :min="0" :max="10000" :step="50" />
                </div>
            </div>

            {{-- Dates (Astryx Date / DateTime / Date Range) --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space4); max-width:820px; margin-top:var(--space5);">
                <div>
                    <label class="form-label">Date</label>
                    <x-form.date name="demo-date" placeholder="Select date" />
                </div>
                <div>
                    <label class="form-label">Time</label>
                    <x-form.time name="demo-time" value="20:30" />
                </div>
                <div>
                    <label class="form-label">Date + time</label>
                    <x-form.datetime name="demo-dt" time="21:00" />
                </div>
                <div>
                    <label class="form-label">Date range</label>
                    <x-form.date-range name="demo-range" placeholder="Select range" />
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space5); align-items:center; margin-top:var(--space4);">
                <x-form.toggle name="demo-toggle" label="Toggle" :checked="true" />
                <x-atom.checkbox.checkbox name="demo-check" label="Checkbox" :checked="true" />
                <x-form.segmented :items="['day' => 'Day', 'week' => 'Week', 'month' => 'Month']" active="week" />
            </div>

            {{-- Input wrap (icon / suffix), password + strength, validation states --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space4); max-width:820px; margin-top:var(--space5);">
                <div>
                    <label class="form-label">Icon &amp; suffix (input-wrap)</label>
                    <x-form.input-wrap suffix="tk">
                        <x-slot:icon>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path d="M12 7v10M8 10h8" /></svg>
                        </x-slot:icon>
                        <x-form.input name="demo-amount" placeholder="Amount" />
                    </x-form.input-wrap>
                </div>
                <div>
                    <label class="form-label" for="demo-pwd2">Password + strength meter</label>
                    <x-form.password name="demo-pwd2" placeholder="••••••••" />
                    <div class="form-pwd-strength" data-level="3">
                        <div class="form-pwd-bar">
                            <span class="form-pwd-seg"></span><span class="form-pwd-seg"></span><span class="form-pwd-seg"></span><span class="form-pwd-seg"></span>
                        </div>
                    </div>
                </div>
                <x-form.field label="Valid state" state="valid" name="demo-valid">
                    <x-form.input name="demo-valid" value="kami_dev" />
                    <p class="form-help form-help-success">Username is available</p>
                </x-form.field>
                <x-form.field label="Error state" state="error" name="demo-error">
                    <x-form.input name="demo-error" value="a" />
                    <p class="form-error">Must be at least 3 characters</p>
                </x-form.field>
            </div>

            {{-- Sliders, tags, rating, PIN --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:var(--space4); max-width:820px; margin-top:var(--space5);">
                <div>
                    <label class="form-label">Slider</label>
                    <x-form.slider name="demo-tip" :value="65" />
                </div>
                <div>
                    <label class="form-label">Range slider</label>
                    <x-form.slider name="demo-age" mode="range" :value-min="20" :value-max="75" />
                </div>
                <div>
                    <label class="form-label">Star rating</label>
                    <x-form.rating name="demo-rating" :value="4" />
                </div>
                <div>
                    <label class="form-label">PIN / one-time code</label>
                    <x-form.pin name="demo-pin" :length="6" />
                </div>
            </div>

            {{-- Tags input + token picker --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:var(--space4); max-width:820px; margin-top:var(--space5); overflow:visible;">
                <div>
                    <label class="form-label">Tags (tokenizer)</label>
                    <x-form.tags name="demo-tags" :tags="['blonde','petite','french']" />
                </div>
                <div>
                    <label class="form-label">Token picker</label>
                    <x-form.token-picker
                        name="demo-tokens"
                        :stepper="true"
                        :options="[
                            ['value' => 100, 'label' => '100'],
                            ['value' => 500, 'label' => '500', 'bonus' => '+5%', 'selected' => true],
                            ['value' => 1000, 'label' => '1 000', 'bonus' => '+10%'],
                        ]"
                    />
                </div>
                <div>
                    <label class="form-label">Password strength</label>
                    <x-form.password name="demo-pwd-strength" placeholder="••••••••" />
                    <x-form.pwd-strength for="demo-pwd-strength" />
                </div>
                <div style="overflow:visible;">
                    <label class="form-label">Avatar upload</label>
                    <x-form.avatar-upload name="demo-avatar" />
                </div>
                <div style="overflow:visible; position:relative; z-index:2;">
                    <label class="form-label">Combobox</label>
                    <x-form.combobox name="demo-combo" value="meg" :open="true">
                        <x-slot:suggestions>
                            <div class="form-combo-section">Models</div>
                            <button type="button" class="form-combo-item active" data-combo-item data-value="meghan">
                                <span class="form-combo-avatar" aria-hidden="true">
                                    <img src="https://i.pravatar.cc/56?img=5" alt="">
                                </span>
                                <span class="form-combo-meta"><span><mark>meg</mark>han</span><small>Online · FR</small></span>
                                <span class="form-combo-kbd">↵</span>
                            </button>
                            <button type="button" class="form-combo-item" data-combo-item data-value="megan">
                                <span class="form-combo-avatar" aria-hidden="true">
                                    <img src="https://i.pravatar.cc/56?img=32" alt="">
                                </span>
                                <span class="form-combo-meta"><span><mark>meg</mark>an</span><small>Last seen 2h</small></span>
                            </button>
                        </x-slot:suggestions>
                    </x-form.combobox>
                </div>
                <div>
                    <label class="form-label">Select row</label>
                    <x-form.select-row name="demo-plan2" value="yearly" title="Yearly" subtitle="Billed annually" price="$79" save="save 33%" :selected="true" />
                    <x-form.select-row name="demo-plan2" value="monthly" title="Monthly" subtitle="Flexible" price="$12" />
                </div>
            </div>

            {{-- Radio list (plain + select rows) + toggle rows --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:var(--space4); max-width:820px; margin-top:var(--space5);">
                <div>
                    <x-form.radio-list
                        name="demo-notify"
                        label="Radio list"
                        description="Astryx-style plain radios — vertical or horizontal."
                        :items="[
                            ['value' => 'all', 'label' => 'All activity'],
                            ['value' => 'mentions', 'label' => 'Mentions only'],
                            ['value' => 'off', 'label' => 'Off'],
                        ]"
                        value="mentions"
                    />
                </div>
                <div>
                    <label class="form-label">Radio list — plans (select rows)</label>
                    <x-form.radio.group name="demo-plan" style="display:flex; flex-direction:column; gap:var(--space2);">
                        <x-form.radio.item name="demo-plan" value="monthly" title="Monthly" subtitle="Billed every month" price="$9.99" />
                        <x-form.radio.item name="demo-plan" value="yearly" title="Yearly" subtitle="Billed once a year" price="$79" save="save 33%" :checked="true" />
                    </x-form.radio.group>
                </div>
                <div>
                    <label class="form-label">Toggle rows</label>
                    <div style="display:flex; flex-direction:column; gap:var(--space1);">
                        <x-form.toggle-row name="demo-tr1" label="Email notifications" :checked="true" />
                        <x-form.toggle-row name="demo-tr2" label="Show me online" :checked="true" />
                        <x-form.toggle-row name="demo-tr3" label="Auto-play previews" />
                    </div>
                </div>
            </div>

            {{-- Dropzone + wizard steps --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:var(--space4); max-width:820px; margin-top:var(--space5);">
                <div>
                    <label class="form-label">Simple dropzone</label>
                    <x-form.dropzone name="demo-file" label="Click or drag your file" hint="PNG, JPG up to 10 MB" />
                </div>
                <div>
                    <label class="form-label">Wizard steps</label>
                    <x-form.steps>
                        <x-form.step :number="1" label="Account" :done="true" />
                        <x-form.step :number="2" label="Profile" :active="true" />
                        <x-form.step :number="3" label="Payout" />
                    </x-form.steps>
                    <div style="margin-top:var(--space3);">
                        <span class="form-status-pill">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12" /></svg>
                            id_front.jpg uploaded
                        </span>
                    </div>
                </div>
            </div>

            <div style="max-width:640px; margin-top:var(--space6);">
                <label class="form-label">Media uploader</label>
                <p style="margin:0 0 var(--space3); font-size:var(--text-small); color:var(--text-muted);">
                    YouTube-style flow — drop videos, images, or files; each becomes an upload job with live progress.
                    Demo mode simulates the transfer (set <code>upload-url</code> for real XHR uploads).
                </p>
                <x-form.uploader
                    name="content[]"
                    accept="image/*,video/*,.pdf,.zip,.mp4,.webm,.mov"
                    :demo="true"
                    :max-files="8"
                    :max-size="524288000"
                    label="Drag and drop video or image files to upload"
                    hint="MP4, WebM, MOV, PNG, JPG, PDF — up to 500 MB each. Uploads start immediately."
                    browse-label="Select files"
                />
            </div>

            <div style="max-width:640px; margin-top:var(--space6);">
                <label class="form-label">Multipart form — creator onboarding</label>
                <p style="margin:0 0 var(--space3); font-size:var(--text-small); color:var(--text-muted);">
                    Multi-step form with local draft restore + checkpoint events for email reminders.
                    Demo mode saves to <code>localStorage</code> only — point <code>checkpoint-url</code> at your API in production.
                    Try <strong>Save &amp; exit</strong>, reload the page, and your progress resumes.
                </p>

                <x-form.multipart id="creator-onboarding" :demo="true" storage-key="design.creator-onboarding">
                    <x-slot:header>
                        <h2>Creator onboarding</h2>
                        <p>Finish your profile to go live. We’ll email you a reminder if you leave mid-way.</p>
                    </x-slot:header>

                    <x-form.multipart.panel
                        step="profile"
                        title="Profile"
                        reminder="complete_profile"
                        description="How viewers will find you."
                    >
                        <x-form.field name="display_name" label="Display name" :required="true">
                            <x-form.input name="display_name" placeholder="MilaSunny" required />
                        </x-form.field>
                        <x-form.field name="bio" label="Bio" optional>
                            <x-form.textarea name="bio" placeholder="Tell fans what to expect…" rows="3" />
                        </x-form.field>
                        <x-form.row>
                            <x-form.field name="country" label="Country" :required="true">
                                <x-form.country name="country" selected="FR" />
                            </x-form.field>
                            <x-form.field name="language" label="Primary language" :required="true">
                                <x-form.select
                                    name="language"
                                    placeholder="Select…"
                                    :options="['en' => 'English', 'fr' => 'French', 'es' => 'Spanish']"
                                    required
                                />
                            </x-form.field>
                        </x-form.row>
                    </x-form.multipart.panel>

                    <x-form.multipart.panel
                        step="verify"
                        title="Verify"
                        reminder="verify_identity"
                        description="Required before payouts. Documents stay private."
                    >
                        <x-form.field name="legal_name" label="Legal name" :required="true">
                            <x-form.input name="legal_name" placeholder="As on your ID" required />
                        </x-form.field>
                        <x-form.field name="dob" label="Date of birth" :required="true">
                            <x-form.input type="date" name="dob" required />
                        </x-form.field>
                        <x-form.field name="id_doc" label="ID document">
                            <x-form.dropzone name="id_doc" accept="image/*,.pdf" label="Upload passport or ID" hint="PNG, JPG or PDF — max 10 MB" />
                        </x-form.field>
                    </x-form.multipart.panel>

                    <x-form.multipart.panel
                        step="preferences"
                        title="Preferences"
                        reminder="set_preferences"
                        description="Help us recommend the right audience."
                        :optional="true"
                    >
                        <x-form.field name="categories" label="Categories" help="Optional — you can change these later.">
                            <x-form.input name="categories" placeholder="e.g. gaming, ASMR, couples" />
                        </x-form.field>
                        <x-form.toggle-row name="newsletter" label="Email me tips to grow my channel" :checked="true" />
                    </x-form.multipart.panel>

                    <x-form.multipart.panel
                        step="payout"
                        title="Payout"
                        reminder="add_payout"
                        description="Where we send your earnings."
                    >
                        <x-form.field name="payout_method" label="Method" :required="true">
                            <x-form.select
                                name="payout_method"
                                placeholder="Select…"
                                :options="['paxum' => 'Paxum', 'wire' => 'Wire transfer', 'crypto' => 'Crypto']"
                                required
                            />
                        </x-form.field>
                        <x-form.field name="payout_email" label="Payout email" :required="true">
                            <x-form.input type="email" name="payout_email" placeholder="creator@studio.com" required />
                        </x-form.field>
                    </x-form.multipart.panel>
                </x-form.multipart>
            </div>
        </section>

        {{-- ==================== Cam-site widgets ==================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Cam-site widgets</h2></div>
            <div style="display:flex; flex-direction:column; gap:var(--space4); max-width:640px;">
                <x-goal-bar goalMessage="oil show at goal" :tokensNeeded="12000" :tokensEarned="7800" />
                <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center;">
                    <x-chip.broadcaster-chip />
                    <x-chip.token-balance-chip />
                    <span style="display:inline-flex; gap:var(--space2); align-items:center; font-size:var(--text-small); color:var(--text-muted);">
                        <x-atom.flag code="fr" /> <x-atom.flag code="ua" /> <x-atom.flag code="us" /> <x-atom.flag code="jp" /> <x-atom.flag code="de" />
                        country flags — <code>&lt;x-atom.flag code="fr" /&gt;</code>
                    </span>
                </div>
            </div>
        </section>

        {{-- ============================== Tabs ============================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Tabs</h2></div>
            <x-tab.tabs id="demo-tabs">
                <div class="tab-bar" role="tablist">
                    <x-tab.trigger id="one" :active="true">Overview</x-tab.trigger>
                    <x-tab.trigger id="two">Tokens</x-tab.trigger>
                    <x-tab.trigger id="three">Components</x-tab.trigger>
                </div>
                <x-tab.panel id="one" :active="true">
                    <p>Primitives define the raw scales — every component reads from them.</p>
                </x-tab.panel>
                <x-tab.panel id="two">
                    <p>Component tokens (<code>*.tokens.css</code>) map primitives to component roles.</p>
                </x-tab.panel>
                <x-tab.panel id="three">
                    <p>Component CSS only ever reads its own tokens plus globals.</p>
                </x-tab.panel>
            </x-tab.tabs>
        </section>

        {{-- ============================== Cards ============================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Cards</h2></div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:var(--space4);">
                <x-card variant="primary">
                    <h3>Primary card</h3>
                    <p style="color:var(--text-muted); font-size:var(--text-small);">Base surface, border, radius from tokens.</p>
                </x-card>
                <x-card variant="primary-border-glow">
                    <h3>Border glow</h3>
                    <p style="color:var(--text-muted); font-size:var(--text-small);">Effect layer via <code>effects.css</code>.</p>
                </x-card>
                <x-card variant="corner-glow">
                    <h3>Corner glow</h3>
                    <p style="color:var(--text-muted); font-size:var(--text-small);">Same structure, different skin.</p>
                </x-card>
            </div>
        </section>

        {{-- ============================== Avatars ============================== --}}
        <section class="favorites-section">
            <div class="favorites-header"><h2>Avatars</h2></div>
            <div style="display:flex; align-items:center; gap:var(--space4);">
                <x-user-avatar size="sm" />
                <x-user-avatar size="md" name="Kami Dev" />
                <x-user-avatar size="lg" url="https://i.pravatar.cc/128?img=12" name="MilaSunny" />
                <x-user-avatar size="md" :notification="true" />
                <x-user-avatar size="md" name="Kira Noir" status="online" />
                <x-user-avatar size="md" url="https://i.pravatar.cc/88?img=32" status="live" :interactive="true" />
                <x-user-avatar.avatar-group :avatars="[null, null, null]" :total="12" size="sm" />
            </div>
        </section>

        {{-- ============================== Admin data table ============================== --}}
        <section class="favorites-section" id="admin">
            <div class="favorites-header"><h2>Admin — data table</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space3);">
                Full showcase: search, toolbar actions, multi-select + bulk bar, status pills, score bars,
                row quick actions, striped dense rows, sticky header, and per-page pagination.
                Selection emits <code>dt:bulk</code> → <code>{ action, ids }</code>.
            </p>

            <x-data-table
                id="admin-table"
                :searchable="true"
                search-placeholder="Search users, roles, status…"
                :selectable="true"
                :striped="true"
                :dense="true"
                :sticky-header="true"
                :resizable="true"
                :page="4"
                :last-page="12"
                :total="284"
                :per-page="24"
                :per-page-options="[12, 24, 48, 96]"
                page-url="#admin?page={page}"
                per-page-url="#admin?per={per}"
                current-sort="score"
                current-dir="desc"
                :columns="[
                    ['key' => 'name', 'label' => 'User', 'sortable' => true],
                    ['key' => 'role', 'label' => 'Role'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'joined', 'label' => 'Joined', 'sortable' => true],
                    ['key' => 'score', 'label' => 'Score', 'align' => 'right', 'sortable' => true],
                    ['key' => 'actions', 'label' => 'Actions', 'align' => 'right', 'width' => '1%'],
                ]"
            >
                <x-slot:toolbar>
                    <x-atom.pill size="sm" :active="true">All</x-atom.pill>
                    <x-atom.pill size="sm">Active</x-atom.pill>
                    <x-atom.pill size="sm">Pending</x-atom.pill>
                    <x-atom.pill size="sm" variant="outline">Suspended</x-atom.pill>
                    <x-atom.button.button size="sm" variant="primary" text="Add user" />
                    <x-atom.button.button size="sm" variant="muted" text="Import CSV" />
                </x-slot:toolbar>

                <x-slot:bulk>
                    <x-atom.button.button size="sm" variant="muted" text="Export" data-dt-bulk="export" />
                    <x-atom.button.button size="sm" variant="muted" text="Message" data-dt-bulk="message" />
                    <x-atom.button.button size="sm" variant="danger" text="Suspend" data-dt-bulk="suspend" />
                    <x-atom.button.button size="sm" variant="danger" text="Delete" data-dt-bulk="delete" />
                </x-slot:bulk>

                <x-slot:tbody>
                    @foreach ([
                        ['id' => 'u1', 'name' => 'Ada Lovelace', 'email' => 'ada@kami.dev', 'role' => 'Admin', 'status' => 'Active', 'tone' => 'success', 'joined' => 'Mar 2024', 'score' => 98, 'bar' => 98, 'barTone' => 'green'],
                        ['id' => 'u2', 'name' => 'Linus Torvalds', 'email' => 'linus@kami.dev', 'role' => 'Member', 'status' => 'Pending', 'tone' => 'warning', 'joined' => 'Jan 2025', 'score' => 87, 'bar' => 87, 'barTone' => 'yellow'],
                        ['id' => 'u3', 'name' => 'Grace Hopper', 'email' => 'grace@kami.dev', 'role' => 'Moderator', 'status' => 'Active', 'tone' => 'success', 'joined' => 'Aug 2023', 'score' => 92, 'bar' => 92, 'barTone' => 'green'],
                        ['id' => 'u4', 'name' => 'Alan Turing', 'email' => 'alan@kami.dev', 'role' => 'Member', 'status' => 'Suspended', 'tone' => 'danger', 'joined' => 'Nov 2024', 'score' => 74, 'bar' => 74, 'barTone' => 'red'],
                        ['id' => 'u5', 'name' => 'Katherine Johnson', 'email' => 'kathy@kami.dev', 'role' => 'Editor', 'status' => 'Active', 'tone' => 'success', 'joined' => 'Feb 2024', 'score' => 95, 'bar' => 95, 'barTone' => 'green'],
                        ['id' => 'u6', 'name' => 'Margaret Hamilton', 'email' => 'margaret@kami.dev', 'role' => 'Admin', 'status' => 'Active', 'tone' => 'success', 'joined' => 'May 2023', 'score' => 99, 'bar' => 99, 'barTone' => 'green'],
                        ['id' => 'u7', 'name' => 'Tim Berners-Lee', 'email' => 'tim@kami.dev', 'role' => 'Member', 'status' => 'Pending', 'tone' => 'warning', 'joined' => 'Jun 2025', 'score' => 81, 'bar' => 81, 'barTone' => 'yellow'],
                        ['id' => 'u8', 'name' => 'Hedy Lamarr', 'email' => 'hedy@kami.dev', 'role' => 'Moderator', 'status' => 'Active', 'tone' => 'success', 'joined' => 'Sep 2024', 'score' => 90, 'bar' => 90, 'barTone' => 'green'],
                    ] as $user)
                        <tr
                            class="dt__row"
                            data-dt-row-id="{{ $user['id'] }}"
                            data-dt-searchable="{{ $user['name'] }} {{ $user['email'] }} {{ $user['role'] }} {{ $user['status'] }}"
                        >
                            <x-data-table.check :value="$user['id']" />
                            <td class="dt__td">
                                <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                                    <x-user-avatar :name="$user['name']" size="sm" />
                                    <div style="min-width:0;">
                                        <div class="dt-cell--bold">{{ $user['name'] }}</div>
                                        <div style="color:var(--dt-text-muted); font-size:0.7rem;">{{ $user['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="dt__td">
                                <x-atom.pill size="sm">{{ $user['role'] }}</x-atom.pill>
                            </td>
                            <td class="dt__td">
                                <span class="dt-badge dt-badge--{{ $user['tone'] }}">{{ $user['status'] }}</span>
                            </td>
                            <td class="dt__td" style="color:var(--dt-text-muted);">{{ $user['joined'] }}</td>
                            <td class="dt__td" data-align="right">
                                <span class="dt-bar">
                                    <span class="dt-bar__track" aria-hidden="true">
                                        <span class="dt-bar__fill dt-bar__fill--{{ $user['barTone'] }}" style="width:{{ $user['bar'] }}%"></span>
                                    </span>
                                    <span class="dt-cell--mono">{{ $user['score'] }}</span>
                                </span>
                            </td>
                            <td class="dt__td" data-align="right">
                                <x-data-table.actions>
                                    <x-atom.button.button size="sm" variant="muted" text="Edit" />
                                    <x-atom.button.button size="sm" variant="muted" text="View" />
                                    <x-atom.button.button size="sm" variant="danger" text="Ban" />
                                </x-data-table.actions>
                            </td>
                        </tr>
                    @endforeach
                </x-slot:tbody>
            </x-data-table>

            <script>
                document.getElementById('admin-table')?.addEventListener('dt:bulk', (e) => {
                    const { action, ids } = e.detail || {};
                    window.toasts?.info?.(`${action}: ${(ids || []).join(', ') || 'none'}`, { timer: 3500 });
                });
            </script>
        </section>

        {{-- ============================== Charts ============================== --}}
        <section class="favorites-section" id="charts">
            <div class="favorites-header"><h2>Charts</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space3);">
                Chart.js cards themed for admin dashboards — line, bar, and doughnut. Use <code>&lt;x-chart&gt;</code>.
            </p>

            <div class="chart-grid" style="max-width:1100px;">
                <x-chart
                    id="chart-revenue"
                    type="line"
                    title="Token revenue"
                    meta="Last 7 days · platform"
                    :height="280"
                    :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                    :datasets="[
                        ['label' => 'Sold', 'data' => [420, 510, 480, 620, 590, 740, 690], 'color' => 'series-1'],
                        ['label' => 'Redeemed', 'data' => [280, 300, 340, 360, 410, 390, 450], 'color' => 'series-2'],
                    ]"
                />

                <x-chart
                    id="chart-signups"
                    type="bar"
                    title="New signups"
                    meta="By channel"
                    :height="280"
                    :labels="['Organic', 'Ads', 'Referral', 'Affiliate', 'Other']"
                    :datasets="[
                        ['label' => 'Users', 'data' => [186, 142, 98, 64, 28], 'color' => 'series-4'],
                    ]"
                />

                <x-chart
                    id="chart-mix"
                    type="doughnut"
                    title="Traffic mix"
                    meta="This week"
                    :height="280"
                    :labels="['Live', 'VOD', 'Chat', 'Store']"
                    :datasets="[
                        ['label' => 'Share', 'data' => [42, 23, 21, 14]],
                    ]"
                />
            </div>
        </section>

        {{-- ============================== Overlays / feedback ============================== --}}
        <section class="favorites-section" id="overlays">
            <div class="favorites-header"><h2>Modal, toasts &amp; banners</h2></div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space3);">
                <x-atom.button.button variant="primary" text="Open modal" data-action="modal" onclick="modals.open('demo-modal')" />
                <x-atom.button.button variant="success" text="Success toast" onclick="toasts.success('Document saved successfully')" />
                <x-atom.button.button variant="danger" text="Error toast" onclick="toasts.error('Failed to upload file.')" />
                <x-atom.button.button variant="warning" text="Warning toast" onclick="toasts.warning('Storage almost full.')" />
                <x-atom.button.button variant="muted" text="Info + Undo" onclick="toasts.info('Item deleted', { actions: [{ label: 'Undo', primary: true, onClick: () => toasts.success('Restored.') }] })" />
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3);">
                <x-atom.button.button variant="muted" text="Info banner" data-demo-banner="info" />
                <x-atom.button.button variant="primary" text="Promo banner" data-demo-banner="promo" />
                <x-atom.button.button variant="warning" text="Maintenance" data-demo-banner="maintenance" />
                <x-atom.button.button variant="danger" text="Outage" data-demo-banner="outage" />
                <x-atom.button.button variant="success" text="Resolved" data-demo-banner="resolved" />
                <x-atom.button.button variant="money" text="Bottom signup banner" data-demo-banner="signup" />
            </div>
            <p style="color:var(--text-faint); font-size:var(--text-small); margin-top:var(--space2);">
                <code>window.toasts</code> · <code>window.banners</code> — top banners use <code>.banner-top-root</code>;
                sticky signup uses <code>banners.showSignupPromo()</code> or <code>&lt;x-banner.signup-promo /&gt;</code>.
            </p>
            <script>
                document.querySelectorAll('[data-demo-banner]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const kind = btn.getAttribute('data-demo-banner');
                        if (kind === 'signup') {
                            window.banners?.showSignupPromo?.('Inscrivez-vous pour interagir avec les créateurs !');
                            return;
                        }
                        const demos = {
                            info: { type: 'info', content: 'A dismissible top banner — used for announcements and promos.', timer: 8000 },
                            promo: {
                                type: 'success',
                                template: 'promo',
                                badge: '50% OFF',
                                content: '<strong>Weekend deal</strong> — double tokens on every pack until Sunday.',
                                timer: 10000,
                                actions: [{
                                    label: 'Buy tokens',
                                    htmlElement: 'button',
                                    type: 'primary',
                                    onClick: () => toasts.success('Opening shop…'),
                                }],
                            },
                            maintenance: { type: 'warning', template: 'maintenance', content: 'Scheduled maintenance tonight 02:00–04:00 UTC. Private shows may be interrupted.', timer: 0 },
                            outage: { type: 'danger', template: 'outage', content: 'Payments are temporarily unavailable. We are on it.', timer: 0 },
                            resolved: { type: 'success', template: 'resolved', content: 'All systems are back online. Thanks for your patience.', timer: 8000 },
                        };
                        if (window.banners && demos[kind]) window.banners.create(demos[kind]);
                    });
                });
            </script>
        </section>

        {{-- ============================== Guest auth ============================== --}}
        <section class="favorites-section" id="auth-guest">
            <div class="favorites-header"><h2>Guest auth</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Non-creator login + create-account modals, plus a sticky bottom signup promo bar.
                Open with <code>data-auth-open="login|signup"</code> or <code>modals.open('auth-login'|'auth-signup')</code>.
                Creator registration stays a separate flow (link in the signup footer).
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3);">
                <x-atom.button.button variant="primary" text="Open login" data-auth-open="login" />
                <x-atom.button.button variant="muted" text="Open create account" data-auth-open="signup" />
                <x-atom.button.button variant="money" text="Show bottom signup bar" onclick="banners.showSignupPromo()" />
            </div>
        </section>

        <x-modal.auth.login />
        <x-modal.auth.signup />
        <x-banner.signup-promo />

        {{-- ============================== Comments ============================== --}}
        <section class="favorites-section" id="comments">
            <div class="favorites-header"><h2>Comments</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Basic thread — composer, up/down votes, and <strong>one level of replies</strong>.
                Votes toggle; replies nest under the parent (no nested replies of replies).
            </p>

            <div style="max-width:640px;">
                <x-comments.thread title="Discussion" :count="4">
                    <x-comments.item
                        id="c1"
                        author="Mila Sunny"
                        avatar="https://i.pravatar.cc/64?img=12"
                        time="2h ago"
                        body="Loved the private show earlier — tip menu felt snappy. Anyone else catching the late set?"
                        :score="24"
                        :vote="1"
                    >
                        <x-slot:replies>
                            <x-comments.item
                                id="c1r1"
                                author="Théo B."
                                avatar="https://i.pravatar.cc/64?img=33"
                                time="1h ago"
                                body="Same — the gift rail is cleaner in this build."
                                :score="6"
                                :vote="0"
                                :replyable="false"
                            />
                            <x-comments.item
                                id="c1r2"
                                author="You"
                                avatar="https://i.pravatar.cc/64?img=5"
                                time="40m ago"
                                body="Yeah the vote rail on comments is new too."
                                :score="2"
                                :vote="1"
                                :replyable="false"
                            />
                        </x-slot:replies>
                    </x-comments.item>

                    <x-comments.item
                        id="c2"
                        author="Kira Noir"
                        avatar="https://i.pravatar.cc/64?img=32"
                        time="3h ago"
                        body="Small ask: sticky timestamps on long threads would help when catching up."
                        :score="11"
                        :vote="0"
                    />

                    <x-comments.item
                        id="c3"
                        author="Alex Blaze"
                        avatar="https://i.pravatar.cc/64?img=45"
                        time="5h ago"
                        body="Downvoting low-effort spam works better than hiding everything behind mod queues."
                        :score="-2"
                        :vote="-1"
                    />
                </x-comments.thread>
            </div>
        </section>

        {{-- ============================== Reactions ============================== --}}
        <section class="favorites-section" id="reactions">
            <div class="favorites-header"><h2>Reactions</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Twitch-style emotes — Unicode <em>and</em> custom image/GIF packs with shortcodes (<code>:kamiHype:</code>),
                pack tabs, and search. Pass your CDN assets via the <code>sets</code> prop (site pack + global pack).
            </p>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space5); align-items:flex-start;">
                <x-gallery.post
                    author="Hillary_Collins1"
                    avatar="https://i.pravatar.cc/64?img=12"
                    time="12m ago"
                    image="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=640&h=800&fit=crop"
                    alt="Gallery post"
                    caption="Drop a kamiHype if you’re hopping in the room."
                >
                    <x-slot:reactions>
                        <x-reactions.bar :items="[
                            ['key' => 'kamiLove', 'label' => 'kamiLove', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f496.png', 'count' => 128, 'active' => true],
                            ['key' => 'kamiParty', 'label' => 'kamiParty', 'src' => 'https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'animated' => true, 'count' => 46],
                            ['key' => 'fire', 'label' => 'fire', 'emoji' => '🔥', 'count' => 19],
                        ]" />
                    </x-slot:reactions>
                </x-gallery.post>

                <div style="display:grid; gap:var(--space3); max-width:320px;">
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0;">
                        Exclusive mode (one reaction per user):
                    </p>
                    <x-reactions.bar
                        :exclusive="true"
                        :items="[
                            ['key' => 'kamiPog', 'label' => 'kamiPog', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f62e.png', 'count' => 12],
                            ['key' => 'kamiHype', 'label' => 'kamiHype', 'src' => 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/72x72/1f389.png', 'count' => 5, 'active' => true],
                        ]"
                    />
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0;">
                        Open the picker — <strong>Kami</strong> pack has GIF reactions, <strong>Global</strong> is Unicode. Search by shortcode.
                    </p>
                    <x-reactions.bar />
                </div>
            </div>
        </section>

        {{-- ============================== AI Chat + Studio ============================== --}}
        <section class="favorites-section" id="ai-chat">
            <div class="favorites-header"><h2>AI chat & studio</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Astryx-inspired chat, shared chat dock for people + AI models, saved configs,
                create-model wizard, and CivitAI-style studio (txt2img / img2img / ControlNet / history).
                Full workspace: <a href="{{ route('ai') }}">/ai</a>.
            </p>

            <div style="margin-bottom:var(--space6);">
                <x-ai.chat.layout class="ai-chat--no-aside" title="Quick prompt" model-label="RealVisXL V5.0">
                    <x-slot:sidebar>
                        <ul class="ai-chat-conv-list">
                            <x-ai.chat.conversation id="d1" title="Demo chat" meta="Just now" :active="true" />
                            <x-ai.chat.conversation id="d2" title="Style tests" meta="Yesterday" />
                        </ul>
                    </x-slot:sidebar>

                    <div class="ai-chat-empty">
                        <strong>Prompt a model</strong>
                        <p>Describe a shot. Demo replies locally — listen for <code>ai-chat:send</code> to hit your API.</p>
                    </div>

                    <x-slot:composer>
                        <x-ai.chat.composer
                            :models="['realvis' => 'RealVisXL V5.0', 'flux' => 'Flux Dev']"
                            model="realvis"
                        />
                    </x-slot:composer>
                </x-ai.chat.layout>
            </div>

            <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">
                Thinking orb — plays while the AI girlfriend is composing, generating a photo, or filming a clip.
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space6); align-items:end; margin-bottom:var(--space5);">
                <x-ai.orb mode="thinking" size="lg" />
                <x-ai.orb mode="image" size="lg" />
                <x-ai.orb mode="video" size="lg" />
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space5);">
                <x-ai.thinking mode="thinking" />
                <x-ai.thinking mode="image" />
                <x-ai.thinking mode="video" />
            </div>

            <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">
                AI models open in the <strong>same chat dock</strong> as people — <code>chats.openAi(id)</code> / <code>aiDock.open(id)</code>.
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space5);">
                <button type="button" class="btn btn-primary btn-sm" data-demo-ai-dock="cfg-luna">Open Luna in dock</button>
                <button type="button" class="btn btn-muted btn-sm" data-demo-ai-dock="cfg-nova">Open Nova in dock</button>
                <button type="button" class="btn btn-muted btn-sm" onclick="aiDock?.closeAll()">Close AI chats</button>
                <a href="{{ route('ai') }}#ai-configs" class="btn btn-muted btn-sm">Manage configs</a>
            </div>

            <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">
                Studio preview — open <a href="{{ route('ai') }}#ai-studio">/ai#ai-studio</a> for the full library.
            </p>
            <div style="max-width:100%; overflow:auto;">
                <a href="{{ route('ai') }}#ai-studio" class="btn btn-primary">Open model studio</a>
                <a href="{{ route('ai') }}#ai-chat" class="btn btn-muted" style="margin-left:8px;">Open AI chat</a>
            </div>
            <script>
                document.querySelectorAll('[data-demo-ai-dock]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-demo-ai-dock');
                        if (id && window.chats) window.chats.openAi(id);
                    });
                });
            </script>
        </section>

        {{-- ============================== Wallet ============================== --}}
        <section class="favorites-section" id="wallet">
            <div class="favorites-header"><h2>Wallet</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Token balance, pack picker, and activity. Checkout lives on
                <a href="{{ route('payment') }}">/payment</a> — styled shell only;
                the third-party gateway processes the charge, then you credit the wallet.
            </p>

            <div class="wallet" style="max-width:480px; margin-bottom:var(--space6);">
                <x-wallet.balance :balance="32" meta="Available for tips, privates, and unlocks.">
                    <a href="{{ route('payment') }}" class="btn btn-money btn-sm">Buy tokens</a>
                    <button type="button" class="btn btn-muted btn-sm" data-action="modal" data-modal="buy-tokens">Quick buy</button>
                </x-wallet.balance>

                <x-wallet.history title="Recent activity">
                    <x-wallet.tx type="credit" title="Token pack — 100" meta="Card · Paymentico · Today" :amount="100" />
                    <x-wallet.tx type="debit" title="Private show tip" meta="Streamer · Yesterday" :amount="40" />
                    <x-wallet.tx type="debit" title="Gallery unlock" meta="2 days ago" :amount="15" />
                    <x-wallet.tx type="credit" title="Promo bonus" meta="Welcome offer · Last week" :amount="25" />
                </x-wallet.history>
            </div>

            <div style="max-width:720px;">
                <p style="color:var(--text-muted); font-size:var(--text-small); margin:0 0 var(--space3);">Pack grid (used on the payment page)</p>
                <x-wallet.packs>
                    <x-wallet.pack :amount="50" price="6,99 €" />
                    <x-wallet.pack :amount="100" price="11,99 €" badge="Popular" :checked="true" save="Save 6,01 €" />
                    <x-wallet.pack :amount="300" price="29,99 €" badge="Best value" save="Save 24,01 €" />
                    <x-wallet.pack :amount="1000" price="79,99 €" save="Save 100,01 €" />
                </x-wallet.packs>
            </div>
        </section>

        {{-- ============================== Tipping ============================== --}}
        <section class="favorites-section" id="tipping">
            <div class="favorites-header"><h2>Tipping interfaces</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Three clean patterns on the same charcoal chrome: <strong>live tip menu</strong>,
                <strong>support creator</strong>, and <strong>token shop</strong>.
                Amount dock emits <code>tip:amount</code> / <code>tip:submit</code>.
            </p>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:var(--space5); align-items:start;">
                {{-- 1) Livestream tip menu --}}
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Live tip menu</p>
                    <x-tipping.panel id="tip-live">
                        <x-slot:tabs>
                            <button type="button" class="tipping-tab is-active" data-tipping-tab="menu">Tip menu</button>
                            <button type="button" class="tipping-tab" data-tipping-tab="toys">Toys</button>
                            <button type="button" class="tipping-tab" data-tipping-tab="extras">Extras</button>
                            <button type="button" class="tipping-tab-close" aria-label="Close">×</button>
                        </x-slot:tabs>

                        <x-slot:promo>
                            <span>−10% on the whole tip menu for Fan-club members.</span>
                            <button type="button" class="tipping-promo-cta">Fan-club · ◆</button>
                        </x-slot:promo>

                        <div class="tipping-cols">
                            <span>Activity</span>
                            <span>Tokens</span>
                        </div>
                        <div class="tipping-translate">
                            <span aria-hidden="true">🌐</span>
                            <a href="#">Translate tip menu</a>
                        </div>

                        <div class="tipping-list" data-tipping-panel="menu">
                            <x-tipping.item label="Show feet 👣" :tokens="49" />
                            <x-tipping.item label="Favorite vibe 💗" :tokens="51" />
                            <x-tipping.item label="Flash 👀" :tokens="75" />
                            <x-tipping.item label="Oil show ✨" :tokens="120" />
                            <x-tipping.item label="Toy control 1 min 🎮" :tokens="150" />
                            <x-tipping.item label="Private invite 🔒" :tokens="300" />
                        </div>
                        <div class="tipping-list" data-tipping-panel="toys" hidden>
                            <x-tipping.item label="Soft pulse 💗" :tokens="30" />
                            <x-tipping.item label="Wave pattern 🌊" :tokens="60" />
                            <x-tipping.item label="Max intensity ⚡" :tokens="100" />
                        </div>
                        <div class="tipping-list" data-tipping-panel="extras" hidden>
                            <x-tipping.item label="Name on screen 🏷️" :tokens="40" />
                            <x-tipping.item label="Song request 🎵" :tokens="80" />
                        </div>

                        <x-slot:dock>
                            <x-tipping.amounts
                                :presets="[20, 50, 100, 200, 300]"
                                :value="20"
                                :balance="0"
                                cta="Buy tokens"
                                action="buy"
                                custom-label="Custom amount"
                                need-more-label="You need more tokens"
                                balance-label="tokens left"
                            />
                        </x-slot:dock>
                    </x-tipping.panel>
                </div>

                {{-- 2) Support creator --}}
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Support creator</p>
                    <x-tipping.panel id="tip-support" class="tipping--support">
                        <x-slot:support>
                            <x-user-avatar name="Maya" url="https://i.pravatar.cc/112?img=5" size="lg" />
                            <div class="tipping-support-copy">
                                <h3>Support Maya</h3>
                                <p>Say thanks with tokens — optional note, no tip menu required.</p>
                            </div>
                        </x-slot:support>

                        <div class="tipping-note">
                            <label for="tip-note">Message (optional)</label>
                            <textarea id="tip-note" placeholder="Loved the show tonight…" maxlength="140"></textarea>
                        </div>

                        <div class="tipping-list tipping-list--compact">
                            <x-tipping.item label="Coffee ☕" :tokens="20" />
                            <x-tipping.item label="Rose 🌹" :tokens="50" />
                            <x-tipping.item label="Super fan 💎" :tokens="200" />
                        </div>

                        <x-slot:dock>
                            <x-tipping.amounts
                                :presets="[10, 25, 50, 100, 250]"
                                :value="50"
                                :balance="320"
                                cta="Send tip"
                                action="tip"
                                custom-label="Custom tip"
                                balance-label="tokens available"
                            />
                        </x-slot:dock>
                    </x-tipping.panel>
                </div>

                {{-- 3) Shop / buy tokens --}}
                <div>
                    <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space2);">Token shop</p>
                    <x-tipping.panel id="tip-shop" :wide="true" style="max-width:420px;">
                        <div class="tipping-support-head" style="grid-template-columns:1fr;">
                            <div class="tipping-support-copy">
                                <h3>Buy tokens</h3>
                                <p>Pick a pack — best value highlighted. Checkout opens the payment modal.</p>
                            </div>
                        </div>

                        <div class="tipping-shop">
                            <x-wallet.packs>
                                <x-wallet.pack :amount="50" price="6,99 €" />
                                <x-wallet.pack :amount="100" price="11,99 €" badge="Popular" :checked="true" save="Save 6 €" />
                                <x-wallet.pack :amount="300" price="29,99 €" badge="Best value" save="Save 24 €" />
                                <x-wallet.pack :amount="1000" price="79,99 €" save="Save 100 €" />
                            </x-wallet.packs>
                        </div>

                        <x-slot:dock>
                            <div class="tipping-amounts-wrap">
                                <div class="tipping-actions" style="grid-template-columns:1fr;">
                                    <button type="button" class="tipping-buy" data-action="modal" data-modal="buy-tokens">
                                        Continue to checkout
                                    </button>
                                    <div class="tipping-status">
                                        <span>Balance after pack: <strong>+100 tokens</strong></span>
                                        <span class="tipping-status-meta">Cards &amp; local methods via gateway</span>
                                    </div>
                                </div>
                            </div>
                        </x-slot:dock>
                    </x-tipping.panel>
                </div>
            </div>

            <script>
                document.querySelectorAll('#tipping [data-tipping]').forEach((root) => {
                    root.addEventListener('tip:submit', (e) => {
                        const { action, amount } = e.detail || {};
                        window.toasts?.success?.(`${action}: ${amount} tokens`, { timer: 2800 });
                        if (action === 'buy') window.modals?.open?.('buy-tokens');
                    });
                });
            </script>
        </section>

        {{-- ============================== Creator sections ============================== --}}
        <section class="favorites-section" id="creator-sections">
            <div class="favorites-header"><h2>Creator sections</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Editable profile modules creators can own — socials, ads/promos, custom links, and text.
                Hit <strong>Edit sections</strong> to rename, reorder, remove, or add blocks. Persist via
                <code>creator-sections:change</code>.
            </p>

            <div style="max-width:420px;">
                <x-creator.sections>
                    <x-creator.section id="cs-about" title="About" type="text">
                        <x-creator.text :editable="true">
                            Night owl · private shows on weekends · tip menu always open. DMs for customs.
                        </x-creator.text>
                    </x-creator.section>

                    <x-creator.section id="cs-socials" title="Find me" type="socials">
                        <x-creator.socials>
                            <x-creator.social network="x" href="#" label="X" />
                            <x-creator.social network="instagram" href="#" label="Instagram" />
                            <x-creator.social network="tiktok" href="#" label="TikTok" />
                            <x-creator.social network="discord" href="#" label="Discord" />
                        </x-creator.socials>
                    </x-creator.section>

                    <x-creator.section id="cs-links" title="Links" type="links">
                        <x-creator.links>
                            <x-creator.link href="#">Wishlist</x-creator.link>
                            <x-creator.link href="#">Fan club</x-creator.link>
                            <x-creator.link href="#">Schedule</x-creator.link>
                        </x-creator.links>
                    </x-creator.section>

                    <x-creator.section id="cs-ad" title="Partners" type="ad">
                        <x-creator.ad
                            href="#"
                            image="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&h=450&fit=crop"
                            title="Toy drop — 15% off this week"
                            text="Affiliate / sponsor slot. Creators swap image, copy, and CTA."
                            badge="Ad"
                            cta="Shop now"
                        />
                    </x-creator.section>
                </x-creator.sections>
            </div>
        </section>

        {{-- ============================== Forum / imageboard ============================== --}}
        <section class="favorites-section" id="forum">
            <div class="favorites-header"><h2>Forum / imageboard</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                4chan-style board components: nav strip, catalog grid, index previews, full threads, posts with file thumbs,
                greentext, <code>&gt;&gt;quotes</code>, and a reply form. Click a post number to quote into the form;
                click a thumb to expand in place. Listen for <code>forum:submit</code> to wire your backend.
            </p>

            <x-forum.board
                code="g"
                title="Technology"
                description="Hardware, software, and whatever you’re shipping. Imageboard layout — re-skin with --forum-* tokens."
                banner="https://images.unsplash.com/photo-1518770660439-4636190af475?w=960&h=240&fit=crop"
                :threads="1284"
                :posts="48201"
            >
                <x-slot:nav>
                    <x-forum.nav :boards="[
                        ['code' => 'a', 'href' => '#forum', 'label' => 'a'],
                        ['code' => 'g', 'href' => '#forum', 'label' => 'g', 'active' => true],
                        ['code' => 'mu', 'href' => '#forum', 'label' => 'mu'],
                        ['code' => 'v', 'href' => '#forum', 'label' => 'v'],
                        ['code' => 'vg', 'href' => '#forum', 'label' => 'vg'],
                    ]">
                        <x-slot:aside>
                            <a href="#forum-catalog">Catalog</a>
                            <a href="#forum-index">Index</a>
                            <a href="#forum-thread">Thread</a>
                        </x-slot:aside>
                    </x-forum.nav>
                </x-slot:nav>

                <x-slot:actions>
                    <a href="#forum-catalog" class="is-active">Catalog</a>
                    <a href="#forum-index">Index</a>
                    <a href="#forum-form">Start a Thread</a>
                </x-slot:actions>

                <x-slot:rules>
                    <x-forum.rules>
                        <ul>
                            <li>No doxxing, raiding, or illegal content.</li>
                            <li>Keep /g/ on-topic — tech, tools, workflows.</li>
                            <li>Images optional on replies; required for new threads in this demo.</li>
                        </ul>
                    </x-forum.rules>
                </x-slot:rules>

                <h3 id="forum-catalog" style="margin: var(--space4) 0 var(--space3); font-size: var(--text-lead);">Catalog</h3>
                <x-forum.catalog>
                    <x-forum.catalog-thread
                        id="9001"
                        href="#forum-thread"
                        thumb="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=300&h=200&fit=crop"
                        subject="mechanical keyboard thread"
                        teaser="drop your layouts, lubes, and regret purchases"
                        :replies="214"
                        :images="88"
                        :sticky="true"
                    />
                    <x-forum.catalog-thread
                        id="9002"
                        href="#forum-thread"
                        thumb="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=300&h=200&fit=crop"
                        subject="homelab show & tell"
                        teaser="rack arrives, power bill doubles, still worth it"
                        :replies="96"
                        :images="41"
                    />
                    <x-forum.catalog-thread
                        id="9003"
                        href="#forum-thread"
                        thumb="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=300&h=200&fit=crop"
                        subject="AI tooling that doesn’t suck"
                        teaser="local models, editors, and pipelines worth keeping"
                        :replies="153"
                        :images="27"
                    />
                    <x-forum.catalog-thread
                        id="9004"
                        href="#forum-thread"
                        subject="txt-only: hiring /g/ for weekend scripts"
                        teaser="need a bash one-liner that sorts photos by EXIF"
                        :replies="12"
                        :images="0"
                        :locked="true"
                    />
                </x-forum.catalog>

                <h3 id="forum-index" style="margin: var(--space5) 0 var(--space3); font-size: var(--text-lead);">Index (OP + recent replies)</h3>
                <x-forum.index>
                    <x-forum.index-thread id="9001" :omitted="208" href="#forum-thread">
                        <x-forum.post
                            id="9001"
                            :op="true"
                            :sticky="true"
                            name="Anonymous"
                            subject="mechanical keyboard thread"
                            time="07/24/26(Fri)14:02:11"
                            file-name="board.jpg"
                            file-size="240 KB"
                            file-dims="1200x800"
                            thumb="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=250&h=180&fit=crop"
                            image="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&fit=crop"
                            :backlinks="[9002, 9005]"
                        >
                            <p>Post your boards. No vendor spam.</p>
                            <p class="forum-greentext">&gt;bought another 65%</p>
                            <p class="forum-greentext">&gt;still looking for the one</p>
                        </x-forum.post>

                        <x-forum.post
                            id="9210"
                            name="Anonymous"
                            trip="!KBD4LYFE"
                            time="07/24/26(Fri)18:44:02"
                            file-name="desk.jpg"
                            file-size="180 KB"
                            file-dims="900x600"
                            thumb="https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=125&h=90&fit=crop"
                            image="https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=900&fit=crop"
                        >
                            <p><a class="forum-quote-link" href="#p9001" data-forum-quote-link>&gt;&gt;9001</a></p>
                            <p>Gasket mount changed everything. Lubed switches were worth a Sunday.</p>
                        </x-forum.post>
                    </x-forum.index-thread>
                </x-forum.index>

                <h3 id="forum-thread" style="margin: var(--space5) 0 var(--space3); font-size: var(--text-lead);">Thread view</h3>
                <x-forum.thread id="9001" board="g">
                    <x-slot:toolbar>
                        <a href="#forum">Return</a>
                        <span>/</span>
                        <a href="#forum-catalog">Catalog</a>
                        <span>/</span>
                        <a href="#forum-form">Bottom</a>
                        <span style="margin-left:auto;">Thread No.9001</span>
                    </x-slot:toolbar>

                    <x-forum.post
                        id="9001"
                        :op="true"
                        :sticky="true"
                        name="Anonymous"
                        subject="mechanical keyboard thread"
                        time="07/24/26(Fri)14:02:11"
                        file-name="board.jpg"
                        file-size="240 KB"
                        file-dims="1200x800"
                        thumb="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=250&h=180&fit=crop"
                        image="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1200&fit=crop"
                        :backlinks="[9002, 9005]"
                    >
                        <p>Post your boards. No vendor spam.</p>
                        <p class="forum-greentext">&gt;bought another 65%</p>
                        <p class="forum-greentext">&gt;still looking for the one</p>
                    </x-forum.post>

                    <x-forum.post
                        id="9002"
                        name="Anonymous"
                        time="07/24/26(Fri)14:11:40"
                    >
                        <p><a class="forum-quote-link" href="#p9001" data-forum-quote-link>&gt;&gt;9001</a></p>
                        <p class="forum-greentext">&gt;still looking for the one</p>
                        <p>It’s the second board after you stop chasing specs.</p>
                    </x-forum.post>

                    <x-forum.post
                        id="9005"
                        name="mod"
                        trip="!!Staff"
                        time="07/24/26(Fri)15:03:22"
                        file-name="cable.png"
                        file-size="64 KB"
                        file-dims="640x400"
                        thumb="https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=125&h=90&fit=crop"
                        image="https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=900&fit=crop"
                    >
                        <p><a class="forum-quote-link" href="#p9001" data-forum-quote-link>&gt;&gt;9001</a></p>
                        <p>Sticky for the week. Keep affiliate links out.</p>
                    </x-forum.post>

                    <x-slot:form>
                        <div id="forum-form">
                            <x-forum.form mode="reply" thread-id="9001" />
                        </div>
                    </x-slot:form>
                </x-forum.thread>

                <h3 style="margin: var(--space5) 0 var(--space3); font-size: var(--text-lead);">New thread form</h3>
                <x-forum.form mode="thread" />

                <div style="margin-top: var(--space4);">
                    <x-forum.pager :pages="5" :current="1" href-pattern="#forum" />
                </div>
            </x-forum.board>

            <script>
                document.getElementById('forum')?.addEventListener('forum:submit', (e) => {
                    const d = e.detail || {};
                    window.toasts?.success?.(
                        d.mode === 'thread'
                            ? `Thread drafted: ${(d.subject || 'no subject')} (${(d.comment || '').slice(0, 40)}…)`
                            : `Reply drafted to #${d.threadId} (${(d.comment || '').slice(0, 40)}…)`
                    );
                });
            </script>
        </section>

        {{-- ============================== Explore feed ============================== --}}
        <section class="favorites-section" id="explore">
            <div class="favorites-header"><h2>Explore</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Scroll <strong>inside</strong> the panel. On gallery / manga cards, swipe <strong>sideways</strong> (or use the wheel) to read every page — keep scrolling down when you reach the last page.
            </p>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space3);">
                <x-atom.button.button
                    variant="muted"
                    text="Strip (snap)"
                    onclick="(() => { const r=document.querySelector('#explore-demo'); const f=r?.querySelector('[data-explore-feed]'); r?.setAttribute('data-explore-layout','strip'); f?.classList.remove('explore-feed--grid'); f?.classList.add('explore-feed--strip'); })()"
                />
                <x-atom.button.button
                    variant="muted"
                    text="Grid"
                    onclick="(() => { const r=document.querySelector('#explore-demo'); const f=r?.querySelector('[data-explore-feed]'); r?.setAttribute('data-explore-layout','grid'); f?.classList.remove('explore-feed--strip'); f?.classList.add('explore-feed--grid'); })()"
                />
            </div>

            <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space3);">
                Tip: hover the frame and use the wheel / touch to flip through items.
            </p>

            <x-explore id="explore-demo" layout="strip" storage-key="kami.explore.demo">
                <x-slot:types>
                    <x-explore.types :types="[
                        ['id' => 'live', 'label' => 'Live', 'count' => 128, 'on' => true],
                        ['id' => 'image', 'label' => 'Images', 'count' => 840, 'on' => true],
                        ['id' => 'gallery', 'label' => 'Galleries', 'count' => 210, 'on' => true],
                        ['id' => 'gif', 'label' => 'GIFs', 'count' => 96, 'on' => true],
                        ['id' => 'video', 'label' => 'Videos', 'count' => 312, 'on' => true],
                        ['id' => 'manga', 'label' => 'Manga', 'count' => 54, 'on' => true],
                        ['id' => 'ai', 'label' => 'AI', 'count' => 77, 'on' => true],
                    ]" />
                </x-slot:types>

                <x-slot:categories>
                    <x-explore.categories :categories="[
                        ['id' => 'amateur', 'label' => 'Amateur', 'count' => 420],
                        ['id' => 'cosplay', 'label' => 'Cosplay', 'count' => 188],
                        ['id' => 'outdoor', 'label' => 'Outdoor', 'count' => 96],
                        ['id' => 'asmr', 'label' => 'ASMR', 'count' => 64],
                        ['id' => 'gaming', 'label' => 'Gaming', 'count' => 141],
                        ['id' => 'art', 'label' => 'Art', 'count' => 73],
                    ]" />
                </x-slot:categories>

                <x-slot:feed>
                    <x-explore.feed layout="strip">
                        <x-explore.card
                            type="live"
                            href="#"
                            title="Late set — tip menu open"
                            author="Hillary_Collins1"
                            avatar="https://i.pravatar.cc/64?img=12"
                            src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=720&h=960&fit=crop"
                            :viewers="1842"
                            :categories="[['id' => 'amateur', 'label' => 'Amateur'], ['id' => 'asmr', 'label' => 'ASMR']]"
                        />
                        <x-explore.card
                            type="image"
                            href="#"
                            title="Studio still"
                            author="Kira Noir"
                            avatar="https://i.pravatar.cc/64?img=32"
                            src="https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=720&h=960&fit=crop"
                            :categories="[['id' => 'art', 'label' => 'Art']]"
                        />
                        <x-explore.card
                            type="gallery"
                            href="#"
                            title="Weekend reel"
                            author="Mila Sunny"
                            avatar="https://i.pravatar.cc/64?img=5"
                            :items="[
                                'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=720&h=960&fit=crop',
                            ]"
                            :categories="[['id' => 'outdoor', 'label' => 'Outdoor'], ['id' => 'amateur', 'label' => 'Amateur']]"
                        />
                        <x-explore.card
                            type="gif"
                            href="#"
                            title="Loop teaser"
                            author="Alex Blaze"
                            avatar="https://i.pravatar.cc/64?img=45"
                            src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                            poster="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=720&h=960&fit=crop"
                            :categories="[['id' => 'cosplay', 'label' => 'Cosplay']]"
                        />
                        <x-explore.card
                            type="video"
                            href="#"
                            title="Behind the stream"
                            author="Théo B."
                            avatar="https://i.pravatar.cc/64?img=33"
                            src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4"
                            poster="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=720&h=960&fit=crop"
                            duration="2:14"
                            :categories="[['id' => 'gaming', 'label' => 'Gaming']]"
                        />
                        <x-explore.card
                            type="manga"
                            href="#"
                            title="Chapter 12 — neon alley"
                            author="InkRoom"
                            avatar="https://i.pravatar.cc/64?img=20"
                            :items="[
                                'https://images.unsplash.com/photo-1578632292335-df3abbb0d586?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1613376026476-5f1f9f0953b5?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=720&h=960&fit=crop',
                                'https://images.unsplash.com/photo-1550684848-fac1c5b4e853?w=720&h=960&fit=crop',
                            ]"
                            :categories="[['id' => 'art', 'label' => 'Art'], ['id' => 'cosplay', 'label' => 'Cosplay']]"
                        />
                        <x-explore.card
                            type="ai"
                            href="#"
                            title="Luna — soft portrait config"
                            author="AI Studio"
                            avatar="https://i.pravatar.cc/64?img=48"
                            src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=720&h=960&fit=crop"
                            :categories="[['id' => 'art', 'label' => 'Art'], ['id' => 'amateur', 'label' => 'Amateur']]"
                        />
                    </x-explore.feed>
                </x-slot:feed>
            </x-explore>

            <script>
                (() => {
                    const root = document.getElementById('explore-demo');
                    if (!root) return;
                    let page = 1;
                    const pool = [
                        { type: 'live', title: 'Suggested live', author: 'Nova', img: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=720&h=960&fit=crop', cats: 'amateur', badge: 'LIVE' },
                        { type: 'image', title: 'Suggested still', author: 'Ren', img: 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=720&h=960&fit=crop', cats: 'art', badge: 'PHOTO' },
                        { type: 'manga', title: 'Suggested chapter', author: 'Panel', img: 'https://images.unsplash.com/photo-1578632292335-df3abbb0d586?w=720&h=960&fit=crop', cats: 'art', badge: '24 pages' },
                        { type: 'ai', title: 'Suggested AI render', author: 'Studio', img: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=720&h=960&fit=crop', cats: 'amateur', badge: 'AI' },
                        { type: 'gallery', title: 'Suggested album', author: 'Mila', img: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=720&h=960&fit=crop', cats: 'outdoor', badge: '9 photos' },
                        { type: 'video', title: 'Suggested clip', author: 'Théo', img: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=720&h=960&fit=crop', cats: 'gaming', badge: '1:02' },
                    ];
                    root.addEventListener('explore:need-more', (e) => {
                        const done = e.detail?.done;
                        if (page >= 5) {
                            const status = root.querySelector('[data-explore-status]');
                            if (status) {
                                status.hidden = false;
                                status.textContent = 'You’re caught up — tweak filters for more.';
                            }
                            done?.();
                            return;
                        }
                        const batch = [0, 1].map((i) => {
                            const item = pool[(page + i) % pool.length];
                            return `<article class="explore-card explore-card--${item.type}" data-explore-card data-type="${item.type}" data-categories="${item.cats}">
                                <a class="explore-card-media" href="#" data-explore-open>
                                    <img src="${item.img}" alt="" loading="lazy">
                                    <div class="explore-card-badges"><span class="explore-card-badge explore-card-badge--${item.type}">${item.badge}</span></div>
                                    <div class="explore-card-meta-float">
                                        <h3 class="explore-card-title">${item.title}</h3>
                                        <div class="explore-card-sub"><span class="explore-card-author">${item.author}</span></div>
                                    </div>
                                </a>
                                <div class="explore-card-foot"><div class="explore-card-cats"><span class="explore-card-cat">${item.cats}</span></div></div>
                            </article>`;
                        }).join('');
                        page += 1;
                        window.explore?.appendCards(root, batch);
                        done?.();
                    });
                })();
            </script>
        </section>

        {{-- ============================== Stories ============================== --}}
        <section class="favorites-section" id="stories">
            <div class="favorites-header"><h2>Stories</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Instagram-style story rings, groups, and pinned highlights.
                Tap a ring to open the viewer — progress segments, hold to pause, tap edges to step, swipe between people, reply from the bar.
            </p>

            <div class="stories-demo">
                <x-stories label="Stories">
                    <x-stories.ring
                        id="you"
                        name="You"
                        avatar="https://i.pravatar.cc/128?img=11"
                        state="add"
                        :self="true"
                    />
                    <x-stories.ring
                        id="maya"
                        name="Maya"
                        avatar="https://i.pravatar.cc/128?img=5"
                        state="unseen"
                        :items="[
                            ['id' => 'm1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=720&h=1280&fit=crop', 'caption' => 'Golden hour', 'time' => '2h', 'durationMs' => 5000],
                            ['id' => 'm2', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=720&h=1280&fit=crop', 'caption' => 'Backstage', 'time' => '1h', 'durationMs' => 5000],
                            ['id' => 'm3', 'type' => 'video', 'src' => 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4', 'poster' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=720&h=1280&fit=crop', 'caption' => 'Loop', 'time' => '45m'],
                        ]"
                    />
                    <x-stories.ring
                        id="kira"
                        name="Kira"
                        avatar="https://i.pravatar.cc/128?img=32"
                        state="unseen"
                        :items="[
                            ['id' => 'k1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=720&h=1280&fit=crop', 'caption' => 'Studio lights', 'time' => '3h'],
                            ['id' => 'k2', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=720&h=1280&fit=crop', 'time' => '2h'],
                        ]"
                    />
                    <x-stories.ring
                        id="alex"
                        name="Alex"
                        avatar="https://i.pravatar.cc/128?img=45"
                        state="live"
                        :items="[
                            ['id' => 'a1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=720&h=1280&fit=crop', 'caption' => 'Going live in 5', 'time' => 'Now'],
                        ]"
                    />
                    <x-stories.ring
                        id="ren"
                        name="Ren"
                        avatar="https://i.pravatar.cc/128?img=15"
                        state="seen"
                        :items="[
                            ['id' => 'r1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=720&h=1280&fit=crop', 'caption' => 'Yesterday’s set', 'time' => '18h'],
                        ]"
                    />
                    <x-stories.ring
                        id="close-friends"
                        name="Nova"
                        avatar="https://i.pravatar.cc/128?img=20"
                        state="close"
                        :items="[
                            ['id' => 'n1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1578632292335-df3abbb0d586?w=720&h=1280&fit=crop', 'caption' => 'Close friends only', 'time' => '4h'],
                            ['id' => 'n2', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=720&h=1280&fit=crop', 'time' => '3h'],
                        ]"
                    />
                </x-stories>

                <x-stories.pinned title="Highlights" meta="Pinned">
                    <x-stories.ring
                        id="hl-travel"
                        name="Maya"
                        avatar="https://i.pravatar.cc/128?img=5"
                        cover="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=256&h=256&fit=crop"
                        state="pinned"
                        label="Travel"
                        :items="[
                            ['id' => 't1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=720&h=1280&fit=crop', 'caption' => 'Coast road', 'time' => 'Pinned'],
                            ['id' => 't2', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=720&h=1280&fit=crop', 'caption' => 'Trail morning', 'time' => 'Pinned'],
                        ]"
                    />
                    <x-stories.ring
                        id="hl-fits"
                        name="Maya"
                        avatar="https://i.pravatar.cc/128?img=5"
                        cover="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=256&h=256&fit=crop"
                        state="pinned"
                        label="Fits"
                        :items="[
                            ['id' => 'f1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=720&h=1280&fit=crop', 'caption' => 'Lookbook', 'time' => 'Pinned'],
                        ]"
                    />
                    <x-stories.ring
                        id="hl-set"
                        name="Kira"
                        avatar="https://i.pravatar.cc/128?img=32"
                        cover="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=256&h=256&fit=crop"
                        state="pinned"
                        label="On set"
                        :items="[
                            ['id' => 's1', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=720&h=1280&fit=crop', 'time' => 'Pinned'],
                            ['id' => 's2', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=720&h=1280&fit=crop', 'time' => 'Pinned'],
                            ['id' => 's3', 'type' => 'image', 'src' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=720&h=1280&fit=crop', 'time' => 'Pinned'],
                        ]"
                    />
                    <x-stories.ring
                        id="hl-add"
                        name="New"
                        state="add"
                        label="New"
                        :self="true"
                        :items="[]"
                    />
                </x-stories.pinned>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-top:var(--space4);">
                <x-atom.button.button
                    variant="primary"
                    text="Open Maya’s story"
                    onclick="window.stories?.openFromRing(document.querySelector('[data-story-id=maya]'))"
                />
                <x-atom.button.button
                    variant="muted"
                    text="Reset seen rings"
                    onclick="(() => { localStorage.removeItem('kami.stories.seen'); location.hash='#stories'; location.reload(); })()"
                />
            </div>
        </section>

        {{-- ============================== Chat dock ============================== --}}
        <section class="favorites-section" id="chat" style="padding-bottom: 520px;">
            <div class="favorites-header"><h2>Chat dock</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Facebook-style collapsible chats, fixed to the bottom-right.
                Open a few windows, minimize with the header or <strong>−</strong>, restore from the avatar heads, close with <strong>×</strong>.
                Extra windows beyond what fits are minimized into the head stack (up to 5 open on wide screens).
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space3); position:relative; z-index:1;">
                <x-atom.button.button variant="primary" text="Open live chat" data-demo-chat="hillary" />
                <x-atom.button.button variant="muted" text="Open online chat" data-demo-chat="theo" />
                <x-atom.button.button variant="muted" text="Open 3rd chat" data-demo-chat="jackie" />
                <x-atom.button.button variant="muted" text="Open 4th chat" data-demo-chat="kira" />
                <x-atom.button.button variant="muted" text="Open 5th chat" data-demo-chat="mila" />
                <x-atom.button.button variant="muted" text="Fill head stack (12)" data-demo-chat-stack />
                <x-atom.button.button variant="warning" text="Simulate unread" data-demo-chat-unread />
                <x-atom.button.button variant="danger" text="Close all" onclick="chats.closeAll()" />
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space3); position:relative; z-index:1;">
                <x-atom.button.button variant="primary" text="AI girlfriend + thinking" data-demo-gf-thinking="thinking" />
                <x-atom.button.button variant="muted" text="AI generating photo" data-demo-gf-thinking="image" />
                <x-atom.button.button variant="muted" text="AI filming clip" data-demo-gf-thinking="video" />
            </div>
            <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space4);">
                Tip: open chats sit over the bottom of the page — scroll this section so the buttons stay above the dock.
                Heads stack on the right (max 10); overflow becomes <strong>…</strong>.
                Girlfriend demos show the glowing orb via <code>chats.setThinking(id, mode)</code>.
            </p>
            <script>
                document.querySelectorAll('[data-demo-chat]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-demo-chat');
                        const demos = {
                            hillary: {
                                id: 'hillary',
                                name: 'Hillary_Collins1',
                                avatar: 'https://i.pravatar.cc/128?img=12',
                                status: 'live',
                                messages: [
                                    {
                                        from: 'them',
                                        image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=500&fit=crop',
                                        text: "Don't leave me wanting to have a fun time together, I'll wait for you 🔥",
                                        time: '05:54',
                                    },
                                ],
                            },
                            theo: {
                                id: 'theo',
                                name: 'Théo Bachelier',
                                avatar: 'https://i.pravatar.cc/128?img=33',
                                status: 'online',
                                messages: [
                                    { from: 'them', text: 'Hey — you around tonight?', time: '17:10' },
                                    { from: 'me', text: 'Just finishing up. Give me 10?', time: '17:12' },
                                    { from: 'them', text: 'Perfect 👍', time: '17:13' },
                                ],
                            },
                            jackie: {
                                id: 'jackie',
                                name: 'Jackie Rostagni',
                                avatar: 'https://i.pravatar.cc/128?img=45',
                                status: 'online',
                                messages: [
                                    { from: 'me', text: 'Loved the show earlier', time: '12:01' },
                                    { from: 'them', text: 'Thank you babe 💕 private later?', time: '12:04' },
                                ],
                            },
                            kira: {
                                id: 'kira',
                                name: 'Kira Noir',
                                avatar: 'https://i.pravatar.cc/128?img=32',
                                status: 'offline',
                                subtitle: 'Last seen 2h ago',
                                unread: 2,
                                messages: [
                                    { from: 'them', text: 'Missed you in the room', time: '09:20' },
                                ],
                            },
                            mila: {
                                id: 'mila',
                                name: 'Mila Sunny',
                                avatar: 'https://i.pravatar.cc/128?img=47',
                                status: 'live',
                                messages: [
                                    { from: 'them', text: 'Private show starting soon 💫', time: '18:02' },
                                    { from: 'me', text: 'On my way!', time: '18:03' },
                                ],
                            },
                        };
                        if (window.chats && demos[id]) window.chats.open(demos[id]);
                    });
                });

                document.querySelector('[data-demo-chat-unread]')?.addEventListener('click', () => {
                    if (!window.chats) return;
                    window.chats.open({
                        id: 'kira',
                        name: 'Kira Noir',
                        avatar: 'https://i.pravatar.cc/128?img=32',
                        status: 'offline',
                        subtitle: 'Last seen 2h ago',
                        messages: [{ from: 'them', text: 'Missed you in the room', time: '09:20' }],
                    });
                    window.chats.minimize('kira');
                    window.chats.receive('kira', { text: 'You free for a private later?' });
                    window.chats.receive('kira', { text: 'Ping 👀' });
                });

                document.querySelector('[data-demo-chat-stack]')?.addEventListener('click', () => {
                    if (!window.chats) return;
                    const names = [
                        'Hillary_Collins1', 'Théo Bachelier', 'Jackie Rostagni', 'Kira Noir',
                        'Mila Sunny', 'Alex Blaze', 'Nova Rain', 'Lena Fox',
                        'Chris Vale', 'Sam Ortiz', 'Riley Quinn', 'Jordan Lee',
                    ];
                    names.forEach((name, i) => {
                        const id = `stack-${i}`;
                        window.chats.open({
                            id,
                            name,
                            avatar: `https://i.pravatar.cc/128?img=${12 + i}`,
                            status: i % 3 === 0 ? 'live' : i % 3 === 1 ? 'online' : 'offline',
                            messages: [{ from: 'them', text: `Hey from ${name.split(' ')[0]}`, time: '12:0' + (i % 10) }],
                        });
                        window.chats.minimize(id);
                        if (i >= 9) window.chats.receive(id, { text: 'New ping' });
                    });
                });

                document.querySelectorAll('[data-demo-gf-thinking]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        if (!window.chats) return;
                        const mode = btn.getAttribute('data-demo-gf-thinking') || 'thinking';
                        const id = 'ai-gf';
                        window.chats.open({
                            id,
                            name: 'Luna',
                            avatar: 'https://i.pravatar.cc/128?img=5',
                            status: 'online',
                            subtitle: 'AI girlfriend',
                            messages: [
                                { from: 'them', text: 'Hey babe… tell me what you’re in the mood for 💕', time: '21:04' },
                                { from: 'me', text: mode === 'video' ? 'Film me a little clip?' : mode === 'image' ? 'Send me a selfie?' : 'Miss you tonight', time: '21:05' },
                            ],
                        });
                        window.chats.setThinking(id, mode);
                        const delay = mode === 'video' ? 2600 : mode === 'image' ? 2000 : 1400;
                        window.setTimeout(() => {
                            if (mode === 'image') {
                                window.chats.receive(id, {
                                    text: 'Here… just for you 📸',
                                    image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=500&fit=crop',
                                });
                            } else if (mode === 'video') {
                                window.chats.receive(id, {
                                    text: 'Made you a little clip 🎬 (demo still)',
                                    image: 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=400&h=500&fit=crop',
                                });
                            } else {
                                window.chats.receive(id, { text: 'Mmm I was just thinking about you too… come closer 💭' });
                            }
                        }, delay);
                    });
                });
            </script>
        </section>

        {{-- ============================== Color tokens ============================== --}}
        {{-- ============================== Kit gaps (P0/P1) ============================== --}}
        <section class="favorites-section" id="kit">
            <div class="favorites-header"><h2>Kit — new primitives</h2></div>
            <p style="color:var(--text-muted); max-width:52rem; margin:0 0 var(--space4);">
                Count badges, media lock, age gate, headers, filters, list rows, follow, live meta, skeletons, mobile chrome.
            </p>

            <x-page-header
                eyebrow="Creator"
                title="Maya · Live studio"
                description="Reusable page header with actions."
            >
                <x-slot:actions>
                    <x-follow-button />
                    <x-atom.button.button variant="primary" text="Send tip" />
                </x-slot:actions>
            </x-page-header>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); align-items:center; margin-bottom:var(--space4);">
                <span style="position:relative; display:inline-flex;">
                    <x-icons.bell />
                    <span style="position:absolute; top:-6px; right:-8px;"><x-atom.count-badge :count="3" /></span>
                </span>
                <x-atom.count-badge :count="128" />
                <x-atom.count-badge :count="1" variant="dot" />
                <x-live-meta :viewers="1842" duration="01:12:04" quality="HD" />
            </div>

            <x-filter-bar :count="2" style="margin-bottom:var(--space4);">
                <x-atom.pill variant="accent" :active="true" aria-pressed="true">Amateur</x-atom.pill>
                <x-atom.pill :active="true" aria-pressed="true">Cosplay</x-atom.pill>
                <x-atom.pill>Outdoor</x-atom.pill>
                <x-atom.pill>ASMR</x-atom.pill>
            </x-filter-bar>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:var(--space4); margin-bottom:var(--space4); max-width:720px;">
                <x-thumbnail
                    src="https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=480&h=640&fit=crop"
                    ratio="portrait"
                    style="position:relative;"
                >
                    <x-media.lock price="120" label="Unlock gallery" />
                </x-thumbnail>
                <div style="display:flex; flex-direction:column; gap:var(--space3);">
                    <x-list-row title="Maya" meta="Started a private show" time="2m" href="#kit">
                        <x-slot:leading><x-user-avatar name="Maya" url="https://i.pravatar.cc/64?img=5" size="sm" /></x-slot:leading>
                        <x-slot:trailing><x-atom.count-badge :count="1" /></x-slot:trailing>
                    </x-list-row>
                    <x-list-row title="Kira" meta="Sent you a tip menu" time="18m" href="#kit">
                        <x-slot:leading><x-user-avatar name="Kira" url="https://i.pravatar.cc/64?img=32" size="sm" /></x-slot:leading>
                    </x-list-row>
                    <x-skeleton variant="list" :rows="2" />
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:var(--space3); margin-bottom:var(--space4); max-width:640px;">
                <x-skeleton variant="card" />
                <x-skeleton variant="feed-row" />
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:var(--space3); margin-bottom:var(--space4);">
                <x-atom.button.button variant="muted" text="Open age gate" onclick="localStorage.removeItem('kami.age-gate.ok'); window.modals?.open?.('age-gate')" />
                <x-follow-button state="pending" />
                <x-follow-button :following="true" />
            </div>

            <x-sticky-actions class="is-preview">
                <x-atom.button.button variant="primary" text="Send tip · 50" />
                <x-follow-button />
            </x-sticky-actions>

            <p style="color:var(--text-faint); font-size:var(--text-small); margin:0 0 var(--space3);">
                Floating pill dock — matches panels / chat menus. On device it pins to the bottom (≤1020px).
            </p>
            <x-bottom-nav class="is-preview">
                <x-bottom-nav.item href="#kit" label="Accueil" icon="home" :active="true" />
                <x-bottom-nav.item href="#kit" label="Flux" icon="feed" />
                <x-bottom-nav.item href="#kit" label="Favoris" icon="heart" />
                <x-bottom-nav.item href="#kit" label="Historique" icon="clock" />
                <x-bottom-nav.item href="#kit" label="Messages" icon="chat" :badge-dot="true" />
            </x-bottom-nav>

            <x-modal.age-gate id="age-gate" :auto="false" />
        </section>

        <section class="favorites-section" id="tokens">
            <div class="favorites-header"><h2>Palette</h2></div>

            <h3 style="font-size:var(--text-small); color:var(--text-muted); margin-bottom:var(--space2);">Neutrals — shades of black &amp; white</h3>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space2); margin-bottom:var(--space4);">
                @foreach (['--black', '--gray2', '--gray5', '--gray8', '--gray10', '--silver2', '--silver6', '--silver10', '--white1'] as $token)
                    <div style="width:88px;">
                        <div style="height:44px; border-radius:var(--radius2); border:1px solid var(--border-strong); background:var({{ $token }});"></div>
                        <div style="font-size:var(--text-small); color:var(--text-muted); margin-top:var(--space1);">{{ $token }}</div>
                    </div>
                @endforeach
            </div>

            <h3 style="font-size:var(--text-small); color:var(--text-muted); margin-bottom:var(--space2);">Main colors — the brand voice (max 3)</h3>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space2); margin-bottom:var(--space4);">
                @foreach (['--main1', '--main2', '--main3'] as $token)
                    <div style="width:120px;">
                        <div style="height:56px; border-radius:var(--radius2); background:var({{ $token }});"></div>
                        <div style="font-size:var(--text-small); color:var(--text-muted); margin-top:var(--space1);">{{ $token }}</div>
                    </div>
                @endforeach
            </div>

            <h3 style="font-size:var(--text-small); color:var(--text-muted); margin-bottom:var(--space2);">System colors — fixed vocabulary for statuses &amp; badges</h3>
            <div style="display:flex; flex-wrap:wrap; gap:var(--space2);">
                @foreach (['--lime', '--fuchsia', '--azure', '--neon-pink', '--neon-purple', '--red-bright', '--orange-bright'] as $token)
                    <div style="width:100px;">
                        <div style="height:44px; border-radius:var(--radius2); background:var({{ $token }});"></div>
                        <div style="font-size:var(--text-small); color:var(--text-muted); margin-top:var(--space1);">{{ $token }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-shell>
@endsection
