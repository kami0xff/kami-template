@props([
    'goalMessage' => null,
    'tokensNeeded' => null,
    'tokensEarned' => 0,
    'progress' => null,
])

@php
    // Calculate progress if not provided
    if ($progress === null && $tokensNeeded && $tokensNeeded > 0) {
        $progress = min(100, round(($tokensEarned / $tokensNeeded) * 100, 1));
    }
    $progress = $progress ?? 0;

    // Parse goal message to extract tokens if embedded (e.g., "11737 jt oil my boobs")
    $displayMessage = $goalMessage;
    $displayTokens = $tokensNeeded;

    // Try to extract token amount from message if not provided separately
    if (!$displayTokens && $goalMessage && preg_match('/^(\d+)\s*/', $goalMessage, $matches)) {
        $displayTokens = (int) $matches[1];
        $displayMessage = trim(preg_replace('/^\d+\s*/', '', $goalMessage));
    }
@endphp

@if($goalMessage)
<div class="goal-bar-container">
    <div class="goal-bar">
        {{-- Progress Fill --}}
        <div class="goal-bar-fill" style="width: {{ $progress }}%;"></div>

        {{-- Content --}}
        <div class="goal-bar-content">
            {{-- Left: icons + Text --}}
            <div class="goal-bar-left">
                <div class="goal-bar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <circle cx="12" cy="12" r="6"/>
                        <circle cx="12" cy="12" r="2"/>
                    </svg>
                </div>
                <div class="goal-bar-text">
                    <span class="goal-bar-label">{{ __('common.goal') }}:</span>
                    @if($displayTokens)
                        <span class="goal-bar-tokens">{{ number_format($displayTokens) }}</span>
                    @endif
                    <span class="goal-bar-message">{{ $displayMessage }}</span>
                </div>
            </div>

            {{-- Right: Percentage --}}
            <div class="goal-bar-percentage">
                {{ $progress }}%
            </div>
        </div>
    </div>
</div>

@endif
