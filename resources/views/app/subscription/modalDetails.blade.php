@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $TITLE: string
    - $PAYMENT: $PaymentGateway->getPaymentById
    - $PREAPPROVAL: $PaymentGateway->getPreapprovalById
    - $USER_PLAN: $UserPlan
*/

@endphp

@extends('layout.modal', [
    'divId' => 'subscription-modal-details' . date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ $TITLE ?? 'MODAL' }}
    </h5>
@endsection

@section('MODAL_BODY')
    <div class="container mt-4">
        @if (!$USER_PLAN)
            <x-card title="{{ __('messages.infoModalTitle') }}">
                <div class="alert alert-info">
                    {{ __('messages.pages.premium.modalDetails.noActivePlan') }}
                </div>
            </x-card>
        @else
            <x-card title="{{ __('messages.infoModalTitle') }}">
                <p><strong>{{ __('messages.pages.premium.modalDetails.labelPlanStatus') }}:</strong> {{ $USER_PLAN->getStatuslabel() }}</p>
                <p><strong>{{ __('messages.pages.premium.modalDetails.labelPlanValidity') }}:</strong> {{ $USER_PLAN->getFormattedStartDate() }} - {{ $USER_PLAN->getFormattedEndDate() }}</p>
                <p><strong>{{ __('messages.pages.premium.modalDetails.labelPlanType') }}:</strong> {{ $USER_PLAN->getPlanTypeLabel() }}</p>
            </x-card>

            @php $MPHelper = new \App\Helpers\Payments\MercadoPago(); @endphp
            <x-card title="{{ __('messages.pages.premium.subscription') }}">
                @if ($PREAPPROVAL)
                    <p><strong>Status da Assinatura:</strong> {{ $MPHelper->getPreapprovalStatusLabel($PREAPPROVAL->status) }}</p>
                    <p><strong>Plano:</strong> {{ $PREAPPROVAL->reason }}</p>
                    <p><strong>Valor Recorrente:</strong> R$ {{ $SysUtils::formatDbToNumber($PREAPPROVAL->auto_recurring->transaction_amount, 2) }}</p>
                    <p><strong>Próxima Cobrança:</strong> {{ $SysUtils::timezoneDate($PREAPPROVAL->next_payment_date, __('messages.dateFormat')) }}</p>
                @else
                    <p class="text-muted">Nenhuma assinatura registrada.</p>
                @endif
            </x-card>

            <x-card title="{{ __('messages.pages.premium.paymentHistory.menuTitle') }}">
                @if ($PAYMENT)
                    <p><strong>{{ __('messages.pages.premium.modalDetails.labelPaymentStatus') }}:</strong> {{ $MPHelper->getPaymentStatusLabel($PAYMENT->status) }}</p>
                    <p><strong>{{ __('messages.pages.premium.modalDetails.labelPaymentDetails') }}:</strong> {{ $MPHelper->getPaymentStatusDetailLabel($PAYMENT->status_detail) }}</p>
                    <p><strong>{{ __('messages.pages.premium.modalDetails.labelPaymentPrice') }}:</strong> {{ __('messages.currency') }} {{ $SysUtils::formatDbToNumber($PAYMENT->transaction_amount, 2) }}</p>
                    <p><strong>{{ __('messages.pages.premium.modalDetails.labelPaymentDate') }}:</strong> {{ $PAYMENT->date_approved ? $SysUtils::timezoneDate($PAYMENT->date_approved, __('messages.fullDateFormat')) : '---' }}</p>
                    <p><strong>{{ __('messages.pages.premium.modalDetails.labelPaymentType') }}:</strong> {{ $MPHelper->getPaymentMethodLabel($PAYMENT->payment_method_id) }}</p>
                @else
                    <p class="text-muted">Nenhum pagamento registrado.</p>
                @endif
            </x-card>
        @endif
    </div>
@endsection
