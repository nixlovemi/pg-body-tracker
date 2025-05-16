@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? '404'
])

@section('CORE_HEADER_CUSTOM_CSS')
@endsection

@section('CORE_BODY_CONTENT')
    @include('layout.partials.not-found-base', [
        'H1_TEXT' => '404',
        'H2_TEXT' => __('messages.pages.404.title'),
        'P_TEXT' => __('messages.pages.404.message'),
        'BUTTON_TEXT' => __('messages.pages.404.buttonBackToHome'),
        'BUTTON_URL' => route('app.dashboard.index')
    ])
@endsection

@section('CORE_FOOTER_CUSTOM_JS')
@endsection
