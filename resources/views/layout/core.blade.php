<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
    <head>
        <title>{{ $PAGE_TITLE ?? '' }} | {{ env('SITE_DISPLAY_NAME') }}</title>

        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />

        @livewireStyles
        @yield('CORE_HEADER_CUSTOM_CSS')
        @include('layout.partials.core-styles-css')
    </head>

    <body
        id="page-top"
        data-js-modal-error-title="{{ __('messages.jsAlertErrorTitle') }}"
        data-js-modal-info-title="{{ __('messages.jsAlertInfoTitle') }}"
        data-js-modal-success-title="{{ __('messages.jsAlertSuccessTitle') }}"
        data-js-modal-confirm-title="{{ __('messages.jsAlertConfirmTitle') }}"
        data-js-modal-confirm-yes="{{ __('messages.jsAlertConfirmYes') }}"
        data-js-modal-confirm-close="{{ __('messages.buttonClose') }}"
        data-js-ajax-error-msg="{{ __('messages.jsAjaxErrorMsg') }}"
        data-js-ajax-unexpected-error="{{ __('messages.jsAjaxUnexpectedError') }}"
    >

        @yield('CORE_BODY_CONTENT')
        @include('layout.partials.core-js')
    </body>
</html>
