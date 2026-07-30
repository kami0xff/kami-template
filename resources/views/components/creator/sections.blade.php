{{--
    Creator custom sections — editable modules for ads, socials, links, text.

    Usage:
        <x-creator.sections :editing="false">
            <x-creator.section title="Socials" type="socials">
                <x-creator.socials>
                    <x-creator.social network="x" href="https://x.com/…" label="X" />
                </x-creator.socials>
            </x-creator.section>
        </x-creator.sections>

    Props:
        editing — start in edit mode
        editable — show toolbar / allow creator edits (default true)
--}}
@props([
    'editing' => false,
    'editable' => true,
])

<div
    {{ $attributes->class([
        'creator-sections',
        'creator-sections--editing' => $editing,
    ]) }}
    data-creator-sections
    @if($editable) data-creator-editable @endif
>
    @if($editable)
        <div class="creator-sections-toolbar">
            <button type="button" class="btn btn-muted btn-sm" data-creator-toggle-edit>
                {{ $editing ? 'Done' : 'Edit sections' }}
            </button>

            <div class="creator-add-menu" data-creator-add-menu hidden>
                <button type="button" class="btn btn-primary btn-sm" data-creator-add-toggle>
                    Add section ▾
                </button>
                <div class="creator-add-panel" role="menu">
                    <button type="button" class="creator-add-item" data-creator-add="socials" role="menuitem">Socials</button>
                    <button type="button" class="creator-add-item" data-creator-add="ad" role="menuitem">Ad / promo</button>
                    <button type="button" class="creator-add-item" data-creator-add="links" role="menuitem">Custom links</button>
                    <button type="button" class="creator-add-item" data-creator-add="text" role="menuitem">Text</button>
                </div>
            </div>
        </div>
    @endif

    <div class="creator-sections-list" data-creator-sections-list>
        {{ $slot }}
    </div>
</div>
