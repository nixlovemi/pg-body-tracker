@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.hero-section')
    @include('site.partials.features-section')
    @include('site.partials.about-section')
    @include('site.partials.why-section')
    @include('site.partials.versions-section')
@endsection

