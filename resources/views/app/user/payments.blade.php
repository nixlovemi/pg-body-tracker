@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
$USER = $SysUtils::getLoggedInUser() ?? null;
$CURRENT_PLAN = $USER?->getCurrentPlan() ?? null;
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <h4>{{ $PAGE_TITLE }}</h4>

    <x-card title="{{ __('messages.pages.premium.paymentHistory.cardTitle') }}">
        @if ($CURRENT_PLAN && $CURRENT_PLAN->isCanceledButActiveUntilEndDate())
            <div class="alert alert-warning" role="alert">
                {{ __('messages.pages.premium.paymentHistory.cancelledButActiveNotice', ['date' => $CURRENT_PLAN->getFormattedEndDate()]) }}
            </div>
        @endif

        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.pages.premium.paymentHistory.labelCurrentPlan') }}">
                        {{ __('messages.pages.premium.paymentHistory.labelCurrentPlan') }}
                    </label>
                    <input type="text" class="form-control form-control-user"
                        value="{{ $USER?->getPlanTypeLabel() ?? '' }}" disabled readonly
                    />
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.pages.premium.paymentHistory.labelPlanStartDate') }}">
                        {{ __('messages.pages.premium.paymentHistory.labelPlanStartDate') }}
                    </label>
                    <input type="text" class="form-control form-control-user"
                        value="{{ $CURRENT_PLAN?->getFormattedStartDate() ?? '' }}" disabled readonly
                    />
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.pages.premium.paymentHistory.labelPlanEndDate') }}">
                        {{ __('messages.pages.premium.paymentHistory.labelPlanEndDate') }}
                    </label>
                    <input type="text" class="form-control form-control-user"
                        value="{{ $CURRENT_PLAN?->getFormattedEndDate() ?? '' }}" disabled readonly
                    />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label mb-0" title="{{ __('messages.pages.premium.paymentHistory.labelPaymentTable') }}">
                        {{ __('messages.pages.premium.paymentHistory.labelPaymentTable') }}
                    </label>

                    <livewire:table
                        :config="App\Tables\PaymentsTable::class"
                        :configParams="['userId' => $USER->id]"
                    />
                </div>
            </div>
        </div>
    </x-card>
@endsection
