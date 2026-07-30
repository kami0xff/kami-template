@extends('caumont.layout')

@section('title', 'Villa Estérel — Cap d\'Antibes · Caumont')
@section('body_attrs', 'data-mode="sale"')

@push('styles')
    <link rel="stylesheet" href="{{ vite_asset('assets/caumont-property.css') }}">
@endpush

@push('scripts')
    <script type="module" src="{{ vite_asset('assets/caumont-property.js') }}"></script>
@endpush

@section('content')
    @include('caumont.villa-esterel')
@endsection
