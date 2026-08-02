@extends('layouts.app')

{{-- Title/description come from config('seo.pages.home') — edit them there.
     Anything set via seo()->title(...) after this call still wins. --}}
@php seo()->page('home') @endphp

@section('canonical', url()->current())

@section('content')
<div style="min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem;">
    <h1>{{ __('Hello World') }}</h1>
    <p>
        Your project is ready. Replace <code>resources/views/pages/home.blade.php</code>
        with your own home page.
    </p>
</div>
@endsection
