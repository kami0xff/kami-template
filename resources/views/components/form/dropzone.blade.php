@props([
    'name' => 'file',
    'accept' => '',
    'multiple' => false,
    'label' => 'Click or drag your file',
    'hint' => '',
])

<label {{ $attributes->class(['form-dropzone']) }}>
    <input
        type="file"
        name="{{ $name }}"
        @if($accept) accept="{{ $accept }}" @endif
        @if($multiple) multiple @endif
        hidden
    >
    <span class="form-dropzone-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </span>
    <div class="form-dropzone-text">
        <strong>{{ $label }}</strong>
        @if($hint)
            <span>{{ $hint }}</span>
        @endif
    </div>
</label>
