@extends('caumont.layout')

@section('title', 'Caumont — Luxury Property Management & Real Estate')

@push('styles')
    <link rel="stylesheet" href="{{ vite_asset('assets/caumont-home.css') }}">
@endpush

@push('scripts')
    <script type="module" src="{{ vite_asset('assets/caumont-home.js') }}"></script>
@endpush

@section('content')
    @include('caumont.home')
@endsection
