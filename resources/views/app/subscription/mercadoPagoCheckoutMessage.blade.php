@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $MESSAGE: string
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow text-center">
                    <div class="card-body py-5">

                        {{-- Ícone baseado na mensagem --}}
                        @php
                            $icon = 'fa-hourglass-half text-warning'; // default
                            if (str_contains($MESSAGE, 'ativa') || str_contains($MESSAGE, 'active')) {
                                $icon = 'fa-check-circle text-success';
                            } elseif (str_contains($MESSAGE, 'cancelada') || str_contains($MESSAGE, 'cancelled')) {
                                $icon = 'fa-times-circle text-danger';
                            } elseif (str_contains($MESSAGE, 'processamento') || str_contains($MESSAGE, 'processing')) {
                                $icon = 'fa-hourglass-half text-warning';
                            }
                        @endphp

                        <div class="mb-4">
                            <i class="fas {{ $icon }} fa-5x"></i>
                        </div>

                        <h3 class="mb-3 text-primary">
                            {{ __('messages.pages.premium.titleThankYou') }}
                        </h3>

                        <p class="lead text-muted mb-4">
                            {{ $MESSAGE }}
                        </p>

                        <a href="{{ route('app.dashboard.index') }}" class="btn btn-primary px-4">
                            {{ __('messages.pages.404.buttonBackToHome') }}
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
