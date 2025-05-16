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
        'H1_TEXT' => '419',
        'H2_TEXT' => __('messages.pages.signedExpired.title'),
        'P_TEXT' => __('messages.pages.signedExpired.message'),
        'BUTTON_TEXT' => __('messages.pages.signedExpired.buttonBackToHome'),
        'BUTTON_URL' => route('app.login')
    ])
@endsection

@section('CORE_FOOTER_CUSTOM_JS')
@endsection
