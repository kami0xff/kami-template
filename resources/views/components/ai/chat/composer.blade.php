{{--
    AI chat composer (Astryx ChatComposer-inspired).

    Props:
        models — [id => label] or list of arrays
        model  — selected model id
        placeholder
--}}
@props([
    'models' => [],
    'model' => null,
    'placeholder' => 'Describe what you want to generate…',
])

@php
    $normalized = [];
    foreach ($models as $key => $opt) {
        if (is_array($opt)) {
            $normalized[] = ['value' => (string) ($opt['value'] ?? $opt['id'] ?? ''), 'label' => (string) ($opt['label'] ?? $opt['name'] ?? '')];
        } else {
            $normalized[] = ['value' => (string) $key, 'label' => (string) $opt];
        }
    }
    $selected = (string) ($model ?? ($normalized[0]['value'] ?? ''));
@endphp

<form {{ $attributes->class(['ai-composer']) }} data-ai-composer>
    <div class="ai-composer-toolbar">
        @if(count($normalized))
            <label class="ai-composer-model">
                <span aria-hidden="true">◈</span>
                <select name="model" data-ai-model>
                    @foreach($normalized as $opt)
                        <option value="{{ $opt['value'] }}" @selected($opt['value'] === $selected)>{{ $opt['label'] }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        <span style="color:var(--ai-faint); font-size:var(--text-small);">
            Uses your configured checkpoint + LoRAs
        </span>
    </div>

    <div class="ai-composer-shell">
        <textarea
            class="ai-composer-input"
            name="prompt"
            rows="1"
            placeholder="{{ $placeholder }}"
            data-ai-input
        ></textarea>

        <div class="ai-composer-actions">
            <button type="button" class="ai-composer-icon-btn" title="Attach reference" data-ai-attach aria-label="Attach">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21.4 11.6 12 21a5 5 0 0 1-7.1-7.1l9.2-9.2a3.5 3.5 0 0 1 5 5L10 18.8a1.8 1.8 0 0 1-2.5-2.5l8.1-8.2"/>
                </svg>
            </button>
            <button type="submit" class="ai-composer-send" data-ai-send aria-label="Send">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/>
                </svg>
            </button>
        </div>
    </div>
</form>
