{{--
    Skeleton compositions for loading states.

    <x-skeleton.card />
    <x-skeleton.feed-row />
    <x-skeleton.list :rows="4" />
--}}
@props([
    'variant' => 'card', // card | feed-row | list
    'rows' => 3,
])

@if($variant === 'feed-row')
    <div {{ $attributes->class(['skeleton-comp', 'skeleton-comp--feed-row']) }}>
        <x-atom.skeleton variant="rect" width="100%" height="180px" />
        <div class="skeleton-comp-meta">
            <x-atom.skeleton variant="circle" width="36px" height="36px" />
            <div class="skeleton-comp-lines">
                <x-atom.skeleton variant="line" width="55%" />
                <x-atom.skeleton variant="line" width="35%" />
            </div>
        </div>
    </div>
@elseif($variant === 'list')
    <div {{ $attributes->class(['skeleton-comp', 'skeleton-comp--list']) }}>
        @for($i = 0; $i < (int) $rows; $i++)
            <div class="skeleton-comp-meta">
                <x-atom.skeleton variant="circle" width="40px" height="40px" />
                <div class="skeleton-comp-lines">
                    <x-atom.skeleton variant="line" width="48%" />
                    <x-atom.skeleton variant="line" width="72%" />
                </div>
            </div>
        @endfor
    </div>
@else
    <div {{ $attributes->class(['skeleton-comp', 'skeleton-comp--card']) }}>
        <x-atom.skeleton variant="rect" width="100%" height="140px" />
        <div class="skeleton-comp-lines" style="padding:12px;">
            <x-atom.skeleton variant="line" width="70%" />
            <x-atom.skeleton variant="line" width="45%" />
        </div>
    </div>
@endif
