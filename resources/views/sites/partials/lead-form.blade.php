{{--
    Lead capture form — POSTs to /lead on the site's own domain, which relays
    the lead to the admin project's API (see LeadController).

    Works on fully static pages: submits via fetch when JS is available and
    falls back to a plain POST + redirect (?lead=thanks) without it. The
    inline script also captures the current page path and any utm_* params
    at submit time.

    Include with:
        @include('site::partials.lead-form', [
            'form' => 'newsletter',                 // identifier sent to the API
            'heading' => 'Get new articles by email',
            'text' => null,                         // optional intro line
            'button' => 'Subscribe',
            'thanks' => null,                       // optional custom thank-you line
            'withName' => false,                    // add a name field
            'withMessage' => false,                 // add a textarea (contact form)
        ])
--}}
@php
    $form = $form ?? 'newsletter';
    $heading = $heading ?? __('Get new articles by email');
    $text = $text ?? null;
    $button = $button ?? __('Subscribe');
    $thanks = $thanks ?? __('Thanks — you are on the list!');
    $withName = $withName ?? false;
    $withMessage = $withMessage ?? false;
@endphp

<section class="lead-form" id="lead-{{ $form }}" data-lead-form>
    <h2>{{ $heading }}</h2>
    @if($text)
        <p>{{ $text }}</p>
    @endif

    <form method="POST" action="/lead">
        <input type="hidden" name="form" value="{{ $form }}">
        <input type="hidden" name="page" value="">

        {{-- Honeypot: hidden from humans; bots that fill it are dropped. --}}
        <div class="lf-hp" aria-hidden="true">
            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        @if($withName)
            <input type="text" name="name" placeholder="{{ __('Your name') }}" autocomplete="name" maxlength="120">
        @endif

        <input type="email" name="email" placeholder="{{ __('you@example.com') }}" required autocomplete="email" maxlength="255">

        @if($withMessage)
            <textarea name="message" rows="4" placeholder="{{ __('Your message') }}" maxlength="5000"></textarea>
        @endif

        {{-- data-umami-event: tracked automatically as a conversion event
             when the site has Umami analytics configured. --}}
        <button type="submit" data-umami-event="lead-{{ $form }}">{{ $button }}</button>
    </form>

    <p class="lead-thanks" hidden>{{ $thanks }}</p>
    <p class="lead-error" hidden>{{ __('Something went wrong — please check your email address and try again.') }}</p>
</section>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-lead-form]').forEach(function (box) {
        var form = box.querySelector('form');
        var thanks = box.querySelector('.lead-thanks');
        var error = box.querySelector('.lead-error');

        // No-JS fallback round-trip lands back here with ?lead=thanks.
        if (new URLSearchParams(location.search).get('lead') === 'thanks') {
            form.hidden = true;
            thanks.hidden = false;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            error.hidden = true;

            // Attribution captured client-side: static HTML is baked once,
            // but the visitor's URL (path + utm params) is live.
            form.querySelector('[name=page]').value = location.pathname;
            var params = new URLSearchParams(location.search);
            ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (key) {
                if (params.get(key) && !form.querySelector('[name=' + key + ']')) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = params.get(key);
                    form.appendChild(input);
                }
            });

            fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: new FormData(form)
            }).then(function (response) {
                if (!response.ok) throw new Error('lead failed');
                form.hidden = true;
                thanks.hidden = false;
            }).catch(function () {
                error.hidden = false;
            });
        });
    });
});
</script>
@endonce
