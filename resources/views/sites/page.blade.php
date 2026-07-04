{{-- Renders a markdown static page (content/pages/*.md). --}}
@extends('site::layout')

@section('site-content')
<article>
    {!! $doc->html !!}
</article>
@endsection
