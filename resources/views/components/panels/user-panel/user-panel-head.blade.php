{{--
    Account menu header — avatar + name + handle.
    Matches floating charcoal panel chrome (chat settings).
--}}
@props([
    'username' => 'Account',
    'handle' => null,
    'avatar' => null,
    'avatarSize' => 'md',
])

<div {{ $attributes->class(['user-panel-head']) }}>
    <x-user-avatar :url="$avatar" :name="$username" :size="$avatarSize" />
    <div class="user-panel-head-copy">
        <span class="user-panel-head-name">{{ $username }}</span>
        @if($handle)
            <span class="user-panel-head-handle">{{ $handle }}</span>
        @endif
    </div>
</div>
