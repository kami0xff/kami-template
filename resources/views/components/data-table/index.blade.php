@props([
    'columns' => [],
    'rows' => null,
    'sortRoute' => null,
    'currentSort' => null,
    'currentDir' => 'desc',
    'searchable' => false,
    'searchValue' => '',
    'searchPlaceholder' => 'Search...',
    'striped' => false,
    'dense' => true,
    'stickyHeader' => true,
    'resizable' => true,
    'selectable' => false,
    'emptyText' => 'No data available',
    'id' => 'dt-' . uniqid(),
    // Standalone footer (when $rows is not a Laravel paginator)
    'page' => null,
    'lastPage' => null,
    'total' => null,
    'perPage' => 24,
    'perPageOptions' => [12, 24, 48, 96],
    'pageUrl' => '?page={page}',
    'perPageUrl' => '?per={per}&page=1',
])

{{--
    Reusable DataTable — admin-ready.

    Selection + bulk actions:
        <x-data-table :selectable="true" :columns="..." id="users">
            <x-slot:bulk>
                <x-atom.button.button size="sm" variant="danger" text="Delete" data-dt-bulk="delete" />
                <x-atom.button.button size="sm" variant="muted" text="Export" data-dt-bulk="export" />
            </x-slot:bulk>
            <x-slot:tbody>
                <tr class="dt__row" data-dt-row-id="42">...</tr>
            </x-slot:tbody>
        </x-data-table>

    Row quick actions (in a cell):
        <x-data-table.actions>
            <x-atom.button.button size="sm" variant="muted" text="Edit" />
            <x-atom.button.button size="sm" variant="danger" text="Ban" />
        </x-data-table.actions>
--}}

@php
    $colCount = count($columns) + ($selectable ? 1 : 0);
    $isPaginator = $rows && is_object($rows) && method_exists($rows, 'hasPages');
    $showPager = false;
    $pagerCurrent = 1;
    $pagerLast = 1;
    $pagerTotal = 0;
    $pagerPer = (int) $perPage;
    $pagerUrl = $pageUrl;
    $pagerPerUrl = $perPageUrl;

    if ($isPaginator && ($rows->hasPages() || $rows->total() > 0)) {
        $showPager = true;
        $pagerCurrent = $rows->currentPage();
        $pagerLast = $rows->lastPage();
        $pagerTotal = $rows->total();
        $pagerPer = $rows->perPage();

        $pagerUrl = $rows->url($rows->currentPage());
        if (preg_match('/([?&])page=\d+/', $pagerUrl)) {
            $pagerUrl = preg_replace('/([?&])page=\d+/', '$1page={page}', $pagerUrl);
        } else {
            $pagerUrl .= (str_contains($pagerUrl, '?') ? '&' : '?') . 'page={page}';
        }

        $pagerPerUrl = preg_replace('/([?&])per=\d+/', '', $pagerUrl);
        $pagerPerUrl = preg_replace('/([?&])page=\{page\}/', '$1page=1', $pagerPerUrl);
        $pagerPerUrl .= (str_contains($pagerPerUrl, '?') ? '&' : '?') . 'per={per}';
        $pagerPerUrl = preg_replace('/\?&/', '?', $pagerPerUrl);
        $pagerPerUrl = preg_replace('/&&+/', '&', $pagerPerUrl);
    } elseif ($page !== null || $total !== null) {
        $showPager = true;
        $pagerCurrent = max(1, (int) ($page ?? 1));
        $pagerLast = max(1, (int) ($lastPage ?? 1));
        $pagerTotal = max(0, (int) ($total ?? 0));
    }
@endphp

<div
    {{ $attributes->class([
        'dt',
        'dt--dense' => $dense,
        'dt--striped' => $striped,
        'dt--selectable' => $selectable,
    ]) }}
    id="{{ $id }}"
    data-datatable
    @if($selectable) data-dt-selectable @endif
    @if($resizable) data-dt-resizable @endif
    @if($stickyHeader) data-dt-sticky @endif
>
    {{-- Toolbar --}}
    @if($searchable || isset($toolbar) || ($selectable && isset($bulk)))
        <div class="dt__toolbar">
            @if($searchable)
                <div class="dt__search">
                    <svg class="dt__search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input
                        type="text"
                        class="dt__search-input"
                        placeholder="{{ $searchPlaceholder }}"
                        value="{{ $searchValue }}"
                        data-dt-search
                    >
                </div>
            @endif

            @if($selectable && isset($bulk))
                <div class="dt__bulk" data-dt-bulk-bar hidden>
                    <span class="dt__bulk-count" data-dt-bulk-count>0 selected</span>
                    <div class="dt__bulk-actions">
                        {{ $bulk }}
                    </div>
                    <button type="button" class="dt__bulk-clear" data-dt-clear-selection>Clear</button>
                </div>
            @endif

            @if(isset($toolbar))
                <div class="dt__toolbar-actions">
                    {{ $toolbar }}
                </div>
            @endif
        </div>
    @endif

    {{-- Table --}}
    <div class="dt__wrap">
        <table class="dt__table">
            <thead class="dt__head">
                <tr>
                    @if($selectable)
                        <th class="dt__th dt__th--check" scope="col">
                            <label class="dt__check">
                                <input type="checkbox" data-dt-select-all aria-label="Select all rows">
                                <span class="dt__check-box" aria-hidden="true"></span>
                            </label>
                        </th>
                    @endif
                    @foreach($columns as $col)
                        @php
                            $key = $col['key'] ?? '';
                            $label = $col['label'] ?? $key;
                            $sortable = $col['sortable'] ?? false;
                            $align = $col['align'] ?? 'left';
                            $width = $col['width'] ?? null;
                            $isActive = $sortable && $currentSort === $key;
                            $nextDir = ($isActive && $currentDir === 'desc') ? 'asc' : 'desc';
                        @endphp
                        <th
                            class="dt__th {{ $sortable ? 'dt__th--sortable' : '' }} {{ $isActive ? 'dt__th--active' : '' }}"
                            data-align="{{ $align }}"
                            @if($width) style="width:{{ $width }}" @endif
                            @if($sortable)
                                data-dt-sort="{{ $key }}"
                                data-dt-dir="{{ $isActive ? $currentDir : '' }}"
                            @endif
                        >
                            @if($sortable && $sortRoute)
                                <a
                                    href="{{ $sortRoute }}{{ str_contains($sortRoute, '?') ? '&' : '?' }}sort={{ $key }}&dir={{ $nextDir }}{{ request('q') ? '&q=' . urlencode(request('q')) : '' }}{{ request('project') ? '&project=' . request('project') : '' }}"
                                    class="dt__sort-link"
                                >
                                    <span>{{ $label }}</span>
                                    <span class="dt__sort-arrows">
                                        <svg class="dt__arrow dt__arrow--up {{ $isActive && $currentDir === 'asc' ? 'dt__arrow--active' : '' }}" viewBox="0 0 8 5" aria-hidden="true"><path d="M4 0L8 5H0z"/></svg>
                                        <svg class="dt__arrow dt__arrow--down {{ $isActive && $currentDir === 'desc' ? 'dt__arrow--active' : '' }}" viewBox="0 0 8 5" aria-hidden="true"><path d="M4 5L0 0h8z"/></svg>
                                    </span>
                                </a>
                            @else
                                {{ $label }}
                            @endif
                            @if($resizable)
                                <span class="dt__resize-handle" data-dt-resize></span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="dt__body">
                @if(isset($tbody))
                    {{ $tbody }}
                @elseif($rows && method_exists($rows, 'count') && $rows->count() > 0)
                    {{ $slot }}
                @else
                    <tr class="dt__empty-row">
                        <td colspan="{{ max(1, $colCount) }}" class="dt__empty">
                            {{ $emptyText }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    @if(isset($footer))
        <div class="dt__footer">
            {{ $footer }}
        </div>
    @elseif($showPager)
        <div class="dt__footer">
            <x-pagination.bar
                :current="$pagerCurrent"
                :last="$pagerLast"
                :total="$pagerTotal"
                :per-page="$pagerPer"
                :per-page-options="$perPageOptions"
                :url="$pagerUrl"
                :per-page-url="$pagerPerUrl"
                variant="boxed"
                size="sm"
            />
        </div>
    @elseif($rows && method_exists($rows, 'total'))
        <div class="dt__footer">
            <span class="dt__info">{{ number_format($rows->total()) }} rows</span>
        </div>
    @endif
</div>
