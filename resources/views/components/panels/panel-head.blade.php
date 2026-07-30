{{-- panel-head.blade.php --}}
{{-- Props: $title, $subtitle, $avatarUrl (optional), $avatarSize (sm|md) --}}
@props(['title', 'subtitle' => null, 'avatarUrl' => null, 'avatarSize' => 'md', 'displayAvatar' => false])

<div {{ $attributes->merge(['class' => 'panel-head']) }}>
    @if($displayAvatar)
        <x-user-avatar :url="$avatarUrl" :size="$avatarSize" />
    @endif
    <div class="panel-head-info">
        <div class="panel-head-title">{{ $title }}</div>
        @if($subtitle)
            <div class="panel-head-subtitle">{{ $subtitle }}</div>
        @endif
    </div>
</div>
