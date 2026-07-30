{{-- Generation history / queue panel --}}
@props([
    'title' => 'Generation history',
])

<section {{ $attributes->class(['ai-history']) }} data-ai-history>
    <div class="ai-history-head">
        <h3 class="ai-history-title">{{ $title }}</h3>
        <button type="button" class="btn btn-muted btn-xs" data-ai-history-clear>Clear</button>
    </div>
    <div class="ai-history-list" data-ai-history-list>
        <p class="ai-studio-empty">No generations yet. Hit Generate in the studio.</p>
    </div>
</section>
