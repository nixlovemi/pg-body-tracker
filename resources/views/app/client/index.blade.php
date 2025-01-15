@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    Client
@endsection
