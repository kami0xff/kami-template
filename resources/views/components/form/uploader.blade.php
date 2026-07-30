{{--
    Media uploader — YouTube-style dropzone + per-file upload jobs.

    Props:
        name         — input name (default "files[]")
        accept       — MIME / extension filter (default images + video + pdf)
        multiple     — allow multiple files (default true)
        maxFiles     — hard cap (0 = unlimited)
        maxSize      — max bytes per file (0 = unlimited); e.g. 524288000 for 500 MB
        label        — dropzone title
        hint         — supporting line under the title
        browseLabel  — CTA button label
        uploadUrl    — POST endpoint for real XHR uploads (FormData field = name)
        demo         — simulate progress when no uploadUrl (default false)
        concurrency  — parallel uploads (default 2)

    Events (bubble from root):
        upload:added     { job, file, root }
        upload:progress  { job, loaded, total, percent, root }
        upload:complete  { job, response, root }
        upload:error     { job, error, root }
        upload:cancel    { job, root }
        upload:remove    { job, root }

    Behavior: UploadManager
--}}
@props([
    'name' => 'files[]',
    'accept' => 'image/*,video/*,.pdf,.zip',
    'multiple' => true,
    'maxFiles' => 0,
    'maxSize' => 0,
    'label' => 'Drag and drop files to upload',
    'hint' => 'Videos, images, or documents. Your upload starts as soon as you drop.',
    'browseLabel' => 'Select files',
    'uploadUrl' => null,
    'demo' => false,
    'concurrency' => 2,
])

<div
    {{ $attributes->class(['form-uploader'])->merge([
        'data-uploader' => true,
        'data-name' => $name,
        'data-max-files' => (string) $maxFiles,
        'data-max-size' => (string) $maxSize,
        'data-concurrency' => (string) $concurrency,
    ]) }}
    @if($uploadUrl) data-upload-url="{{ $uploadUrl }}" @endif
    @if($demo) data-demo @endif
>
    <div class="form-uploader-drop" data-uploader-drop tabindex="0" role="button"
         aria-label="{{ $label }}">
        <input
            type="file"
            name="{{ $name }}"
            @if($accept) accept="{{ $accept }}" @endif
            @if($multiple) multiple @endif
            hidden
            data-uploader-input
        >

        <div class="form-uploader-drop-inner">
            <span class="form-uploader-drop-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 16V4" />
                    <path d="m7 9 5-5 5 5" />
                    <path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5" />
                </svg>
            </span>
            <p class="form-uploader-drop-title">{{ $label }}</p>
            @if($hint)
                <p class="form-uploader-drop-hint">{{ $hint }}</p>
            @endif
            <span class="form-uploader-browse">
                <span class="btn btn-muted btn-sm">{{ $browseLabel }}</span>
            </span>
        </div>
    </div>

    <ul class="form-uploader-jobs" data-uploader-jobs aria-live="polite"></ul>
</div>
