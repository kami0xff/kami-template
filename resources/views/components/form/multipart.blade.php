{{--
    Multipart / wizard form with checkpoint persistence.

    Props:
        id             — stable form id (required for storage + API)
        action         — final submit URL (optional)
        method         — final submit method (default POST)
        checkpointUrl  — POST/PATCH endpoint for progress saves
        storageKey     — localStorage key (default: multipart.{id})
        autosaveMs     — debounce for local + remote save (default 1200)
        resume         — restore draft on load (default true)
        demo           — skip network; persist local only + emit events (design)

    Slots:
        default — one or more <x-form.multipart.panel>
        header  — optional content above the stepper

    Events (bubble): multipart:step | multipart:checkpoint | multipart:complete
                     multipart:save-exit | multipart:error

    Checkpoint payload (for email-reminder backends):
        { id, step, reminder, index, total, progress, values, completedSteps, updatedAt }
--}}
@props([
    'id' => 'multipart',
    'action' => '',
    'method' => 'POST',
    'checkpointUrl' => null,
    'storageKey' => null,
    'autosaveMs' => 1200,
    'resume' => true,
    'demo' => false,
])

@php
    $actualMethod = strtoupper($method);
    $formMethod = in_array($actualMethod, ['GET', 'POST']) ? $actualMethod : 'POST';
    $key = $storageKey ?: 'multipart.'.$id;
@endphp

<form
    method="{{ $formMethod }}"
    @if($action) action="{{ $action }}" @endif
    {{ $attributes->class(['form', 'form-multipart'])->merge([
        'id' => $id,
        'data-multipart' => true,
        'data-multipart-id' => $id,
        'data-storage-key' => $key,
        'data-autosave-ms' => (string) $autosaveMs,
    ]) }}
    @if($checkpointUrl) data-checkpoint-url="{{ $checkpointUrl }}" @endif
    @if($resume) data-resume @endif
    @if($demo) data-demo @endif
    novalidate
>
    @if($actualMethod !== 'GET')
        @csrf
    @endif
    @if(! in_array($actualMethod, ['GET', 'POST']))
        @method($actualMethod)
    @endif

    <input type="hidden" name="_multipart_step" value="" data-multipart-step-field>
    <input type="hidden" name="_multipart_progress" value="0" data-multipart-progress-field>

    <div class="form-multipart-header">
        @isset($header)
            <div class="form-multipart-intro">{{ $header }}</div>
        @endisset

        <div class="form-multipart-meta">
            <div class="form-steps" data-multipart-steps aria-label="Form progress"></div>
            <div class="form-save" data-multipart-save hidden>
                <span class="form-save-icon" aria-hidden="true"></span>
                <span data-multipart-save-label>Saved</span>
            </div>
        </div>

        <div class="form-multipart-progress" aria-hidden="true">
            <div class="form-multipart-progress-fill" data-multipart-progress-bar style="width:0%"></div>
        </div>
    </div>

    <div class="form-multipart-panels" data-multipart-panels>
        {{ $slot }}
    </div>

    <div class="form-multipart-nav" data-multipart-nav>
        <button type="button" class="btn btn-muted" data-multipart-prev hidden>Back</button>
        <button type="button" class="btn btn-muted" data-multipart-save-exit>Save &amp; exit</button>
        <span class="form-multipart-nav-spacer"></span>
        <button type="button" class="btn btn-primary" data-multipart-next>Continue</button>
        <button type="submit" class="btn btn-primary" data-multipart-finish hidden>Finish onboarding</button>
    </div>
</form>
