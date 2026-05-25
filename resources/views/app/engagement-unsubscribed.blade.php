@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? __('messages.pages.engagement.unsubscribe.title')
])

@section('CORE_HEADER_CUSTOM_CSS')
@endsection

@section('CORE_BODY_CONTENT')
    @include('layout.partials.not-found-base', [
        'H1_TEXT' => __('messages.pages.engagement.unsubscribe.done'),
        'H2_TEXT' => __('messages.pages.engagement.unsubscribe.title'),
        'P_TEXT' => __('messages.pages.engagement.unsubscribe.message'),
        'BUTTON_TEXT' => __('messages.pages.engagement.unsubscribe.buttonBackToLogin'),
        'BUTTON_URL' => route('app.login')
    ])
@endsection

@section('CORE_FOOTER_CUSTOM_JS')
@endsection
