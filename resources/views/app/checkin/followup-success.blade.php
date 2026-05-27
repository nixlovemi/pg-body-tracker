@php
/**
 * View variables:
 *  - $PAGE_TITLE: string
 *  - $CLIENT: App\Models\Client
 */
@endphp

@extends('layout.login-base', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('LOGIN_BASE_CONTENT')
    @php
        $successTitle = $SUCCESS_TITLE ?? __('messages.pages.checkin.followup.thankYouTitle');
        $successDescription = $SUCCESS_DESCRIPTION ?? __('messages.pages.checkin.followup.thankYouDescription', ['clientName' => $CLIENT->getName()]);
    @endphp

    <div class="text-center">
        <h4 class="mb-3">{{ $successTitle }}</h4>
        <p class="text-muted">
            {{ $successDescription }}
        </p>

        <a href="{{ route('site.home') }}" class="btn btn-light border mt-2">
            {{ __('messages.pages.checkin.followup.backButton') }}
        </a>
    </div>
@endsection
