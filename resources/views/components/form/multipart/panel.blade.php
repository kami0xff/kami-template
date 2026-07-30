{{--
    One step/panel inside <x-form.multipart>.

    Props:
        step        — stable slug (profile, verify, payout…) — used in API + emails
        title       — stepper label
        description — optional lead copy shown above fields
        reminder    — email-reminder template key (defaults to step)
        optional    — step can be skipped (Continue without validating)
--}}
@props([
    'step',
    'title' => null,
    'description' => null,
    'reminder' => null,
    'optional' => false,
])

<section
    {{ $attributes->class(['form-multipart-panel'])->merge([
        'data-multipart-panel' => true,
        'data-step' => $step,
        'data-title' => $title ?? \Illuminate\Support\Str::headline($step),
        'data-reminder' => $reminder ?? $step,
    ]) }}
    @if($optional) data-optional @endif
    hidden
    role="group"
    aria-labelledby="multipart-{{ $step }}-title"
>
    <div class="form-multipart-panel-head">
        <h3 class="form-multipart-panel-title" id="multipart-{{ $step }}-title">
            {{ $title ?? \Illuminate\Support\Str::headline($step) }}
        </h3>
        @if($description)
            <p class="form-multipart-panel-desc">{{ $description }}</p>
        @endif
    </div>

    <div class="form-multipart-panel-body">
        {{ $slot }}
    </div>
</section>
