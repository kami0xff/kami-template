@props(['modelId', 'modelName' => 'this model', 'affiliateUrl' => null])

@php
    use App\Models\ModelTipMenu;
    use App\Models\TipActionType;
    
    $tipMenu = ModelTipMenu::getForModel($modelId);
    
    // If no custom menu, show default actions with suggested prices
    if ($tipMenu->isEmpty()) {
        $tipMenu = TipActionType::active()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($action) {
                return (object) [
                    'actionType' => $action,
                    'token_price' => (int) round(($action->suggested_min_tokens + $action->suggested_max_tokens) / 2 / 5) * 5,
                ];
            });
    }
    
    // Group by category
    $groupedMenu = $tipMenu->groupBy(fn($item) => $item->actionType->category);
    
    $categoryLabels = [
        'tease' => __('tips.tease'),
        'dance' => __('tips.dance'),
        'interactive' => __('tips.interactive'),
        'special' => __('tips.special_shows'),
        'outfit' => __('tips.outfit'),
        'general' => __('tips.general'),
    ];
    
    $categoryIcons = [
        'tease' => '😈',
        'dance' => '💃',
        'interactive' => '🎮',
        'special' => '✨',
        'outfit' => '👙',
        'general' => '⭐',
    ];
@endphp

<div class="tip-menu-card">
    {{-- Header --}}
    <div class="tip-menu-header">
        <div class="tip-menu-header-content">
            <h3 class="tip-menu-title">
                <span class="tip-menu-emoji">🎁</span>
                {{ __('tips.tip_menu') }}
            </h3>
            <p class="tip-menu-subtitle">{{ __('tips.make_her_day') }}</p>
        </div>
        <div class="tip-menu-badge">
            <span>{{ $tipMenu->count() }} {{ __('tips.actions') }}</span>
        </div>
    </div>

    {{-- Content --}}
    <div class="tip-menu-content">
        @foreach ($groupedMenu as $category => $items)
            <div class="tip-menu-category">
                <h4 class="tip-menu-category-title">
                    <span class="tip-menu-category-icon">{{ $categoryIcons[$category] ?? '⭐' }}</span>
                    {{ $categoryLabels[$category] ?? ucfirst($category) }}
                </h4>
                <div class="tip-menu-items">
                    @foreach ($items as $item)
                        <div class="tip-menu-item">
                            <div class="tip-menu-item-info">
                                <span class="tip-menu-item-emoji">{{ $item->actionType->emoji }}</span>
                                <span class="tip-menu-item-name">{{ $item->actionType->translated_name }}</span>
                            </div>
                            <div class="tip-menu-item-price">
                                <span class="tip-menu-item-tokens">{{ $item->token_price }}</span>
                                <svg class="tip-menu-coin" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- CTA --}}
    <div class="tip-menu-cta">
        <div class="tip-menu-cta-content">
            <p class="tip-menu-cta-title">💎 {{ __('tips.want_to_tip') }} {{ $modelName }}?</p>
            <p class="tip-menu-cta-text">{{ __('tips.register_tokens_description') }}</p>
        </div>
        
        @if ($affiliateUrl)
            <a href="{{ $affiliateUrl }}" target="_blank" rel="nofollow noopener" class="tip-menu-cta-btn">
                {{ __('tips.get_tokens') }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        @endif
    </div>
</div>
