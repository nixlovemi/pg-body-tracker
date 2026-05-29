@php
/**
 * View variables:
 *  - $PAGE_TITLE: string
 *  - $CLIENT: App\Models\Client
 *  - $FIELDS: array<int, array<string, mixed>>
 *  - $SUBMIT_URL: string
 */
@endphp

@extends('layout.login-base', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('LOGIN_BASE_CONTENT')
    <div class="text-center mb-4">
        <h4>{{ __('messages.pages.checkin.followup.formTitle') }}</h4>
        <p class="text-muted mb-0">
            {{ __('messages.pages.checkin.followup.formDescription', ['clientName' => $CLIENT->getName()]) }}
        </p>
    </div>

    <form action="{{ $SUBMIT_URL }}" method="POST" class="user">
        @csrf
        <input type="hidden" name="f-form-link-signature" value="{{ $FORM_LINK_SIGNATURE ?? '' }}" />
        <input type="hidden" name="f-form-link-expires" value="{{ $FORM_LINK_EXPIRES ?? '' }}" />

        <div class="form-group">
            <label for="f-weight">{{ __('messages.pages.checkin.followup.weightLabel') }}</label>
            @php
                $currentWeight = $CLIENT->getCurrentWeight();
                $weight = old('f-weight') ?: ($currentWeight ? number_format($currentWeight, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')) : '');
            @endphp
            <input
                type="text"
                class="form-control form-control-user jq-mask-money"
                id="f-weight"
                name="f-weight"
                maxlength="5"
                data-thousands="{{ __('messages.thousandSeparator') }}"
                data-decimal="{{ __('messages.decimalSeparator') }}"
                data-precision="1"
                value="{{ $weight }}"
                required
            />
        </div>

        @foreach (($FIELDS ?? []) as $field)
            @php
                $fieldKey = $field['field_key'] ?? '';
                $Field = \App\Helpers\CheckinFields\CheckinFieldRegistry::make((string) ($field['field_type'] ?? ''), $field);
                $fieldLabel = $Field?->getDisplayLabel() ?? ($field['label'] ?? $fieldKey);
                $required = (bool) ($field['required'] ?? false);
                $oldValue = old('f-' . $fieldKey);
            @endphp

            @if ($Field && !empty($fieldKey))
                <div class="form-group">
                    <label for="f-{{ $fieldKey }}">
                        {{ __($fieldLabel) }}
                        @if ($required)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    @include($Field->getFollowupInputView(), ['Field' => $Field, 'fieldKey' => $fieldKey, 'required' => $required, 'oldValue' => $oldValue])
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn primary btn-user btn-block">
            {{ __('messages.pages.checkin.followup.submitButton') }}
        </button>
    </form>
@endsection
