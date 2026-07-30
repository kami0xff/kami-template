{{--
    Story ring — avatar with state halo (unseen / seen / live / add / pinned / close).

    Props:
        id       — group id (required for open)
        name     — display name under ring
        avatar   — avatar URL
        cover    — optional cover (pinned highlights)
        state    — unseen | seen | live | add | pinned | close
        label    — override label under ring (e.g. highlight title)
        self     — “Your story”
        items    — array of story media payloads
                   [{ id, type: image|video, src, poster?, durationMs?, caption?, time? }]

    New stories (items present + unseen) get the animated gradient highlight circle.
--}}
@props([
    'id' => null,
    'name' => '',
    'avatar' => null,
    'cover' => null,
    'state' => null,
    'label' => null,
    'self' => false,
    'items' => [],
])

@php
    $groupId = $id ?: \Illuminate\Support\Str::slug($name ?: 'story') . '-' . substr(md5(($avatar ?? '') . ($cover ?? '')), 0, 6);
    $img = $cover ?: $avatar;
    $caption = $label ?? ($self ? 'Your story' : $name);
    $initials = $name
        ? collect(explode(' ', trim($name)))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('')
        : '?';
    $itemCount = is_countable($items) ? count($items) : 0;
    $resolvedState = $state;
    if ($resolvedState === null) {
        if ($self && $itemCount === 0) {
            $resolvedState = 'add';
        } elseif ($itemCount > 0) {
            $resolvedState = 'unseen';
        } else {
            $resolvedState = 'seen';
        }
    }
    $isNew = $itemCount > 0 && in_array($resolvedState, ['unseen', 'close', 'live'], true);
    $payload = [
        'id' => $groupId,
        'name' => $name,
        'avatar' => $avatar,
        'cover' => $cover,
        'state' => $resolvedState,
        'label' => $label,
        'self' => (bool) $self,
        'items' => $items,
    ];
@endphp

<button
    type="button"
    @class([
        'stories-ring',
        'stories-ring--' . $resolvedState,
        'stories-ring--new' => $isNew && $resolvedState === 'unseen',
        'stories-ring--self' => $self,
    ])
    data-story-ring
    data-story-id="{{ $groupId }}"
    data-story-state="{{ $resolvedState }}"
    data-story-new="{{ $isNew ? '1' : '0' }}"
    data-story-group='@json($payload)'
    aria-label="{{ $self && $resolvedState === 'add' ? 'Add to your story' : 'View story from ' . $name }}"
    {{ $attributes }}
>
    <span class="stories-ring-halo" aria-hidden="true">
        <span class="stories-ring-avatar">
            @if($img)
                <img src="{{ $img }}" alt="" loading="lazy">
            @else
                <span class="stories-ring-avatar-fallback">{{ mb_strtoupper($initials) }}</span>
            @endif

            @if($resolvedState === 'add' || ($self && $itemCount === 0))
                <span class="stories-ring-add" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
                </span>
            @endif

            @if($resolvedState === 'live')
                <span class="stories-ring-live">Live</span>
            @endif
        </span>
    </span>
    <span class="stories-ring-label">{{ $caption }}</span>
</button>
