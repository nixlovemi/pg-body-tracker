@inject('AppDashboardPresenter', 'App\Presenters\AppDashboardPresenter')

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
    @if (($AVALIATION_COUNT ?? 0) <= 0)
        @php
            $clientDone = (($CLIENT_COUNT ?? 0) > 0);
        @endphp

        <style>
            .onboarding-stepper {
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .onboarding-step-item {
                flex: 1 1 0;
                min-width: 180px;
            }

            .onboarding-step-dot {
                width: 30px;
                height: 30px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.9rem;
                border: 2px solid #6c757d;
                color: #6c757d;
                background: #fff;
                flex-shrink: 0;
            }

            .onboarding-step-dot.is-done {
                border-color: #28a745;
                background: #28a745;
                color: #fff;
            }

            .onboarding-step-dot.is-current {
                border-color: #007bff;
                color: #007bff;
            }

            .onboarding-step-line {
                width: 52px;
                height: 2px;
                background: #d9dee3;
                margin-top: 14px;
                flex-shrink: 0;
            }

            @media (max-width: 767.98px) {
                .onboarding-stepper {
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .onboarding-step-line {
                    width: 2px;
                    height: 20px;
                    margin-top: 0;
                    margin-left: 14px;
                }
            }
        </style>

        <div class="card mb-4 border-left-info">
            <div class="card-body">
                <h5 class="mb-2">{{ __('messages.pages.dashboard.onboarding.title') }}</h5>
                <p class="mb-3 text-muted">{{ __('messages.pages.dashboard.onboarding.description') }}</p>

                <div class="onboarding-stepper mb-3">
                    <div class="onboarding-step-item">
                        <div class="d-flex align-items-start">
                            <span class="onboarding-step-dot {{ $clientDone ? 'is-done' : 'is-current' }}">
                                {!! $clientDone ? '&#10003;' : '1' !!}
                            </span>
                            <div class="ml-2 pt-1">
                                {{ __('messages.pages.dashboard.onboarding.stepClient') }}
                            </div>
                        </div>
                    </div>

                    <div class="onboarding-step-line"></div>

                    <div class="onboarding-step-item">
                        <div class="d-flex align-items-start">
                            <span class="onboarding-step-dot {{ $clientDone ? 'is-current' : '' }}">2</span>
                            <div class="ml-2 pt-1">
                                {{ __('messages.pages.dashboard.onboarding.stepAvaliation') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    @if (($CLIENT_COUNT ?? 0) <= 0)
                        <a href="{{ route('app.client.add') }}" class="btn btn-sm btn-primary">
                            {{ __('messages.pages.dashboard.onboarding.ctaClient') }}
                        </a>
                    @else
                        <a href="{{ route('app.avaliation.index') }}" class="btn btn-sm btn-primary">
                            {{ __('messages.pages.dashboard.onboarding.ctaAvaliation') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        @foreach ($AppDashboardPresenter::getDashboardCardData() as $cardClass)
            @php
                $card = new $cardClass();
            @endphp
            <div class="col-12 col-lg-4 col-xl-3 mb-3">
                <x-dashboard-card
                    :card="$card"
                />
            </div>
        @endforeach
    </div>
@endsection
