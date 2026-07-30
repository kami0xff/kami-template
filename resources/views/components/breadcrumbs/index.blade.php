{{--
    Breadcrumb trail (Astryx-inspired).

    <x-breadcrumbs :items="[
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Models', 'href' => '/models'],
        ['label' => 'Kami'],
    ]" />

    Props:
        items       — [{ label, href? }, ...]  last item is current page
        separator   — slash | chevron | any custom string
        max-visible — collapse middle crumbs when the trail is long (e.g. 4)
--}}
@props([
    'items' => [],
    'separator' => 'slash',
    'maxVisible' => null,
])

@php
    $all = array_values(array_filter($items, fn ($i) => !empty($i['label'])));
    $count = count($all);
    $max = $maxVisible !== null ? max(2, (int) $maxVisible) : null;

    $visible = $all;
    $collapsed = false;
    if ($max !== null && $count > $max) {
        $collapsed = true;
        // Keep first + last (max - 1) items; middle becomes an ellipsis.
        $tail = array_slice($all, -($max - 1));
        $visible = array_merge([$all[0]], [['label' => '…', 'ellipsis' => true]], $tail);
    }

    $sepKind = match ($separator) {
        'chevron' => 'chevron',
        'slash' => 'slash',
        default => 'custom',
    };
@endphp

<nav {{ $attributes->class(['breadcrumbs', $sepKind === 'chevron' ? 'breadcrumbs--chevron' : null]) }} aria-label="Breadcrumb">
    <ol class="breadcrumbs-list">
        @foreach($visible as $item)
            <li class="breadcrumbs-item">
                @if(!$loop->first)
                    <span class="breadcrumbs-separator" aria-hidden="true">
                        @if($sepKind === 'chevron')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        @elseif($sepKind === 'slash')
                            /
                        @else
                            {{ $separator }}
                        @endif
                    </span>
                @endif

                @if(!empty($item['ellipsis']))
                    <span class="breadcrumbs-ellipsis" aria-hidden="true">…</span>
                @elseif(!empty($item['href']) && !$loop->last)
                    <a class="breadcrumbs-link" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                @else
                    <span class="breadcrumbs-current" @if($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
