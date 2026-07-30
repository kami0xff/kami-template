{{--
    Modal / dialog shell (Astryx: Dialog).

    <x-modal.modal id="demo" title="Example" maxWidth="440px">
        Body copy…
        <x-slot:footer>
            <x-atom.button.button variant="muted" text="Cancel" onclick="modals.close('demo')" />
            <x-atom.button.button variant="primary" text="Confirm" onclick="modals.close('demo')" />
        </x-slot:footer>
    </x-modal.modal>

    Complex layouts (buy-tokens, auth) that already include .modal-body
    should pass :padded="false".
--}}
@props([
    'id',
    'title' => '',
    'icon' => null,
    'maxWidth' => null,
    'padded' => true,
])

<div
    {{ $attributes->class(['modal-overlay']) }}
    id="{{ $id }}"
    role="presentation"
>
    <div
        @class(['modal'])
        role="dialog"
        aria-modal="true"
        @if($title) aria-labelledby="{{ $id }}-title" @endif
        @if($maxWidth) style="max-width: {{ $maxWidth }}" @endif
    >

        <button
            type="button"
            class="modal-close"
            aria-label="{{ __('Close') }}"
            data-modal-close
            data-target="{{ $id }}"
            onclick="modals.close('{{ $id }}')"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        @if(isset($head))
            {{ $head }}
        @else
            <div class="modal-head">
                <div class="modal-head-title">
                    @if($icon)
                        {{ $icon }}
                    @endif
                    <h2 id="{{ $id }}-title">{{ $title }}</h2>
                </div>
            </div>
        @endif

        @if($padded)
            <div class="modal-body">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif

        @isset($footer)
            <div class="modal-footer">
                {{ $footer }}
            </div>
        @endisset

    </div>
</div>
