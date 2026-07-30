@props([
    'modelId',
    'modelName' => null,
    'profileUrl' => null,
    'imageUrl' => null,
    'compact' => false,
    'includeSchema' => true,
])

@php
    use App\Models\ModelHeatmap;
    
    $grid = ModelHeatmap::getHeatmapGrid($modelId);
    $summary = ModelHeatmap::getSummary($modelId);
    $bestTimes = ModelHeatmap::getBestTimes($modelId, 5);
    $scheduleBlocks = ModelHeatmap::getScheduleBlocks($modelId, 40);
    
    // Monday-first order: map display index to day_of_week (0=Sun,...6=Sat)
    $dayOrder = [1, 2, 3, 4, 5, 6, 0]; // Mon, Tue, Wed, Thu, Fri, Sat, Sun
    $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $dayLabelsFull = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    // Generate JSON-LD if we have the required data
    $jsonLd = null;
    if ($includeSchema && $modelName && $profileUrl && $summary['has_data']) {
        $jsonLd = ModelHeatmap::getJsonLdScript($modelId, $modelName, $profileUrl, $imageUrl);
    }
@endphp

{{-- JSON-LD Structured Data for Broadcast Schedule --}}
@if($jsonLd)
{!! $jsonLd !!}
@endif

@if($summary['has_data'])
<div class="heatmap-card">
    {{-- Header --}}
    <div class="heatmap-card-header">
        <div>
            <h3 class="heatmap-card-title">
                <svg class="heatmap-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                {{ __('schedule.online_schedule') }}
            </h3>
            @if($summary['best_day'])
                <p class="heatmap-card-subtitle">
                    {{ __('schedule.most_active') }} {{ __($summary['best_day']) }}
                    @if($summary['best_hour']) {{ __('schedule.around') }} {{ $summary['best_hour'] }} @endif
                </p>
            @endif
        </div>
    </div>

    {{-- Grid --}}
    <div class="heatmap-card-body">
        <div class="heatmap-grid-wrapper">
            {{-- Day headers (Monday first) --}}
            <div class="heatmap-day-row">
                <div class="heatmap-hour-cell"></div>
                @foreach($dayLabels as $index => $day)
                    <div class="heatmap-day-cell" title="{{ $dayLabelsFull[$index] }}">{{ $day }}</div>
                @endforeach
            </div>
            
            {{-- Hour rows (columns reordered: Mon-Sun) --}}
            @foreach($grid as $hour => $row)
                @if(!$compact || $hour % 2 === 0)
                <div class="heatmap-grid-row">
                    <div class="heatmap-hour-cell">{{ sprintf('%02d', $hour) }}</div>
                    @foreach($dayOrder as $displayIdx => $dbDay)
                        @php $slot = $row[$dbDay] ?? $row[0]; @endphp
                        <div 
                            class="heatmap-slot heat-level-{{ $slot['heat_level'] }}"
                            title="{{ $dayLabelsFull[$displayIdx] }} {{ sprintf('%02d:00', $slot['hour']) }} - {{ round($slot['percentage']) }}% {{ __('common.online') }}"
                        ></div>
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>
        
        {{-- Legend --}}
        <div class="heatmap-legend-row">
            <span class="heatmap-legend-text">{{ __('schedule.less') }}</span>
            <div class="heatmap-legend-slot heat-level-0"></div>
            <div class="heatmap-legend-slot heat-level-1"></div>
            <div class="heatmap-legend-slot heat-level-2"></div>
            <div class="heatmap-legend-slot heat-level-3"></div>
            <div class="heatmap-legend-slot heat-level-4"></div>
            <span class="heatmap-legend-text">{{ __('schedule.more') }}</span>
        </div>
    </div>

    {{-- Schedule blocks --}}
    @if(!$compact && !empty($scheduleBlocks))
    <div class="heatmap-card-footer">
        <h4 class="heatmap-schedule-title">{{ __('schedule.typical_schedule') }}</h4>
        <div class="heatmap-schedule-list">
            @foreach(array_slice($scheduleBlocks, 0, 4) as $block)
                <div class="heatmap-schedule-item">
                    <span class="schedule-item-day">{{ __($block['day_name']) }}</span>
                    <span class="schedule-item-time">{{ sprintf('%02d:00', $block['start_hour']) }} - {{ sprintf('%02d:00', $block['end_hour']) }}</span>
                    <span class="schedule-item-pct">{{ round($block['avg_percentage']) }}%</span>
                </div>
            @endforeach
        </div>
    </div>
    @elseif(!$compact && $bestTimes->isNotEmpty())
    <div class="heatmap-card-footer">
        <h4 class="heatmap-schedule-title">{{ __('schedule.best_times') }}</h4>
        <div class="heatmap-best-list">
            @foreach($bestTimes->take(4) as $time)
                <div class="heatmap-best-item">
                    <span class="best-item-day">{{ __($time->day_name) }}</span>
                    <span class="best-item-time">{{ $time->formatted_hour }}</span>
                    <span class="best-item-pct">{{ round($time->online_percentage) }}%</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@else
<div class="heatmap-card heatmap-card--empty">
    <div class="heatmap-empty-content">
        <svg class="heatmap-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <p class="heatmap-empty-text">{{ __('schedule.online_schedule') }}</p>
        <p class="heatmap-empty-subtext">{{ __('schedule.schedule_data_coming_soon') }}</p>
    </div>
</div>
@endif
