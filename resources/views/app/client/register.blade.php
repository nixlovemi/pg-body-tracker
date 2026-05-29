@inject('mClient', 'App\Models\Client')
@inject('Constants', 'App\Helpers\Constants')
@inject('Permissions', 'App\Helpers\Permissions')
@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $TYPE: App\Helpers\Constants::FORM_ACTIONS
    - $ACTION: string
    - $CLIENT: App\Models\Client|null
*/

$canEdit = ($Constants::FORM_VIEW !== $TYPE && $Permissions::checkPermission($Permissions::ACL_CLIENT_EDIT));
$isEditingOrViewing = in_array($TYPE, [$Constants::FORM_VIEW, $Constants::FORM_EDIT]);
$isFirstClient = $IS_FIRST_CLIENT ?? false;
$prefillClient = $PREFILL_CLIENT ?? [];
$prefillSelf = (int) request()->input('prefillSelf', 0) === 1;
$LoggedUser = $SysUtils::getLoggedInUser();
$isPremiumPlan = $LoggedUser?->hasPremiumPlan() ?? false;
$canManageCheckin = $canEdit && $Permissions::checkPermission($Permissions::ACL_CHECKIN_EDIT);
$checkinSummary = $CHECKIN_SUMMARY ?? null;
$insightsFreeCardPresented = $PATIENT_INSIGHTS_FREE_CARD_PRESENTED ?? null;
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <h4>{{ $PAGE_TITLE }}</h4>

    <form id="client-form" action="{{ $ACTION }}" method="POST">
        @csrf
        <input type="hidden" name="f-cid" value="{{ $CLIENT?->codedId }}" />
        <input type="hidden" name="f-cedit" value="{{ $canEdit }}" />
        <input
            type="hidden"
            name="f-onboarding-create-first-avaliation"
            id="f-onboarding-create-first-avaliation"
            value="{{ old('f-onboarding-create-first-avaliation', $prefillSelf ? '1' : '0') }}"
        />

        @if ($Constants::FORM_ADD === $TYPE && $isFirstClient)
            <div class="alert alert-info">
                <strong>{{ __('messages.pages.client.register.selfShortcutTitle') }}</strong>
                <div class="mt-1">{{ __('messages.pages.client.register.selfShortcutDescription') }}</div>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-primary" id="btn-fill-self-client">
                        {{ __('messages.pages.client.register.selfShortcutFill') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary ml-1" id="btn-fill-self-and-start-avaliation">
                        {{ __('messages.pages.client.register.selfShortcutFillAndStart') }}
                    </button>
                </div>
                <small class="d-block mt-2 text-muted">
                    {{ __('messages.pages.client.register.selfShortcutAfterSaveHint') }}
                </small>
            </div>
        @endif

        <x-card title="{{ __('messages.pages.client.register.cardInfo') }}">
            <div class="form-row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.first_name') }}">
                            * {{ __('messages.models.Client.fields.first_name') }}
                        </label>
                        <input type="text" class="form-control form-control-user"
                            {{ ($canEdit) ?: 'disabled' }}
                            id="f-name" name="f-name" maxlength="60" value="{{ old('f-name') ?: ($CLIENT?->first_name ?: ($prefillSelf ? ($prefillClient['first_name'] ?? '') : '')) }}"
                        />
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.last_name') }}">
                            * {{ __('messages.models.Client.fields.last_name') }}
                        </label>
                        <input type="text" class="form-control form-control-user"
                            {{ ($canEdit) ?: 'disabled' }}
                            id="f-surname" name="f-surname" maxlength="80" value="{{ old('f-surname') ?: ($CLIENT?->last_name ?: ($prefillSelf ? ($prefillClient['last_name'] ?? '') : '')) }}"
                        />
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.birthdate') }}">
                            * {{ __('messages.models.Client.fields.birthdate') }}
                        </label>
                        @php
                        $birthdate = old('f-birth') ?: ($CLIENT?->birthdate ? $SysUtils::reformatDate($CLIENT?->birthdate, 'Y-m-d', __('messages.dateFormat')) : '');
                        @endphp
                        <input type="text" class="form-control form-control-user jq-datepicker"
                            {{ ($canEdit) ?: 'disabled' }}
                            id="f-birth" name="f-birth" maxlength="10" value="{{ $birthdate }}"
                        />
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.gender') }}">
                            * {{ __('messages.models.Client.fields.gender') }}
                        </label>
                        <select
                            {{ ($canEdit) ?: 'disabled' }}
                            class="form-control form-control-user"
                            id="f-bsex"
                            name="f-bsex"
                        >
                            @php
                            $clientGender = old("f-bsex") ?: $CLIENT?->gender;
                            @endphp

                            @foreach (array_merge(
                                ['' => __('messages.selectEmptyOption') ],
                                $mClient::fGetGenders()
                            ) as $gender => $display)
                                <option
                                    value="{{ $gender }}"
                                    {{ $gender !== $clientGender ? '': 'selected' }}
                                >{{ $display }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label" title="Email">
                            Email
                        </label>
                        <input type="text" class="form-control form-control-user"
                            {{ ($canEdit) ?: 'disabled' }}
                            id="f-email" name="f-email" maxlength="255"
                            value="{{ old('f-email') ?: $CLIENT?->email }}"
                        />
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.phone') }}">
                            {{ __('messages.models.Client.fields.phone') }}
                        </label>
                        <input type="text" class="form-control form-control-user"
                            {{ ($canEdit) ?: 'disabled' }}
                            id="f-phone" name="f-phone" maxlength="35" value="{{ old('f-phone') ?: $CLIENT?->phone }}"
                        />
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="{{ __('messages.pages.client.register.cardMeasures') }}" closed="true">
            <div class="form-row">
                <div @class([
                        'col-12',
                        'col-md-4' => $isEditingOrViewing,
                        'col-md-6' => $Constants::FORM_ADD === $TYPE
                    ])
                >
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.height') }} (cm)">
                            * {{ __('messages.models.Client.fields.height') }} (cm)
                        </label>
                        <select
                            {{ ($canEdit) ?: 'disabled' }}
                            class="form-control form-control-user"
                            id="f-height"
                            name="f-height"
                        >
                            @php
                            $clientHeight = old("f-height") ?: $CLIENT?->height_cm;
                            @endphp

                            @for ($i = 50; $i <= 300; $i++)
                                @if ($i === 50)
                                    <option value="">{{ __('messages.selectEmptyOption') }}</option>
                                @endif

                                <option
                                    value="{{ $i }}"
                                    {{ $i == $clientHeight ? 'selected': '' }}
                                >{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div @class([
                        'col-12',
                        'col-md-4' => $isEditingOrViewing,
                        'col-md-6' => $Constants::FORM_ADD === $TYPE
                    ])
                >
                    <div class="form-group">
                        <label class="form-label" title="{{ __('messages.models.Client.fields.weight') }} (kg)">
                            * {{ __('messages.models.Client.fields.weight') }} (kg)
                        </label>
                        @php
                        $clientWeight = old("f-weight") ?: number_format($CLIENT?->weight_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator'));
                        @endphp
                        <input type="text" class="form-control form-control-user jq-mask-money"
                            id="f-weight" name="f-weight" maxlength="7" {{ ($canEdit) ?: 'disabled' }}
                            data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                            data-precision="1" value="{{ $clientWeight }}"
                        />
                    </div>
                </div>

                @if ($isEditingOrViewing)
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.client.register.labelActualWeight') }} (kg)">
                                * {{ __('messages.pages.client.register.labelActualWeight') }} (kg)
                            </label>
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                disabled maxlength="7" {{ ($canEdit) ?: 'disabled' }}
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ number_format($CLIENT?->getCurrentWeight(), 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')) }}"
                            />
                        </div>
                    </div>
                @endif
            </div>
        </x-card>

        @if ($isEditingOrViewing)
            <x-card title="{{ $insightsFreeCardPresented['card_title'] ?? __('messages.pages.client.register.cardInsights') }}" closed="true">
                @if ($insightsFreeCardPresented)

                    <div class="alert alert-light border mb-3">
                        <div>
                            <strong>{{ __('messages.pages.client.register.insightsCurrentStatus') }}:</strong>
                            <span class="badge badge-{{ $insightsFreeCardPresented['status_badge_class'] }}">{{ $insightsFreeCardPresented['status_label'] }}</span>
                        </div>
                        @if ($insightsFreeCardPresented['show_risk_percent'])
                            <div class="mt-1">
                                <strong>{{ __('messages.pages.client.register.insightsRiskPercent') }}:</strong>
                                {{ $insightsFreeCardPresented['risk_percent_display'] }}%
                            </div>
                        @endif

                        @if ($insightsFreeCardPresented['show_analysis_coverage'])
                            <div class="mt-1">
                                <strong>{{ __('messages.pages.client.register.insightsAnalysisCoverage') }}:</strong>
                                {{ $insightsFreeCardPresented['analysis_coverage_display'] }}%
                            </div>
                        @endif
                    </div>

                    @if ($insightsFreeCardPresented['show_low_confidence_alert'])
                        <div class="alert alert-warning mb-3">
                            <strong>{{ __('messages.pages.client.register.insightsLowConfidenceTitle') }}</strong>
                            <div class="mt-1">
                                {{ __('messages.pages.client.register.insightsLowConfidenceBody', [
                                    'minConfidence' => $insightsFreeCardPresented['min_confidence_display'],
                                ]) }}
                            </div>
                        </div>
                    @endif

                    @if ($insightsFreeCardPresented['show_reasons'])
                        <div class="mb-3">
                            <strong>{{ __('messages.pages.client.register.insightsReasonsTitle') }}</strong>
                            <ul class="mb-0 mt-2 pl-3">
                                @foreach ($insightsFreeCardPresented['reasons'] as $reason)
                                    <li class="mb-1">
                                        <strong>{{ $reason['label'] }}:</strong>
                                        {{ $reason['message'] }}

                                        @if ($reason['has_indicator_text'])
                                            <div class="small text-muted mt-1">
                                                {{ __('messages.pages.client.register.insightsIndicatorPrefix') }} {{ $reason['indicator_text'] }}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($insightsFreeCardPresented['show_history'])
                        <div class="mb-3">
                            <strong>{{ __('messages.pages.client.register.insightsHistoryTitle') }}</strong>
                            <div class="mt-2 d-flex flex-wrap" style="gap: 8px;">
                                @foreach ($insightsFreeCardPresented['history'] as $historyItem)
                                    <span class="badge badge-{{ $historyItem['badge_class'] }}" title="{{ $historyItem['tooltip'] }}">
                                        {{ $historyItem['snapshot_date_label'] }} - {{ $historyItem['status_label'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($insightsFreeCardPresented['show_suggestions'])
                        <div class="mb-3">
                            <strong>{{ $insightsFreeCardPresented['suggestions_title'] }}</strong>
                            <ul class="mb-0 mt-2 pl-3">
                                @foreach ($insightsFreeCardPresented['suggestions'] as $suggestion)
                                    <li class="mb-1">{{ $suggestion }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($insightsFreeCardPresented['show_cta'])
                        <div class="alert alert-info mt-3 mb-0">
                            <strong>{{ $insightsFreeCardPresented['cta_title'] }}</strong>
                            <div class="mt-1">{{ $insightsFreeCardPresented['cta_body'] }}</div>
                            <a href="{{ route('app.subscription.upgrade') }}" class="btn btn-sm btn-primary mt-2">
                                {{ $insightsFreeCardPresented['cta_button_label'] }}
                            </a>
                        </div>
                    @endif
                @else
                    <div class="alert alert-light border mb-0">
                        {{ __('messages.pages.client.register.insightsNoData') }}
                    </div>
                @endif
            </x-card>

            <x-card title="{{ __('messages.pages.client.register.cardGoals') }}" closed="true">
                <div id='dv-card-client-goals'>
                    @include('app.client.partials.cardGoalsContent', [
                        'GOAL' => $CLIENT?->getCurrentGoal(),
                        'CAN_EDIT' => $canEdit
                    ])
                </div>
            </x-card>

            <x-card title="{{ __('messages.pages.client.register.cardAvaliations') }}" closed="true">
                @if ($canEdit && $Permissions::checkPermission($Permissions::ACL_AVALIATION_EDIT))
                <a
                    href="javascript:;" id="btn-client-new-avaliation"
                    class="btn btn-light btn-user btn-sm"
                    style="position:absolute; margin-top:6px;"
                >
                    {{ __('messages.pages.client.register.btnNewAvaliation') }}
                </a>
                @endif

                <div id='dv-card-client-avaliations'>
                    <livewire:table
                        :config="App\Tables\AvaliationsTable::class"
                        :configParams="[
                            'clientId' => $CLIENT?->id,
                            'canEdit' => $canEdit
                        ]"
                    />
                </div>
            </x-card>

            @if ($canManageCheckin)
                <x-card title="{{ __('messages.pages.client.register.cardCheckin') }}" closed="true">
                    @if ($isPremiumPlan)
                        <p class="mb-2">
                            {{ __('messages.pages.client.register.checkinDescription') }}
                        </p>

                        @if ($checkinSummary)
                            <div class="alert alert-light border mb-3">
                                <strong>{{ __('messages.pages.client.register.checkinSummaryTitle') }}</strong>

                                <div class="mt-2">
                                    <div><strong>{{ __('messages.pages.client.register.checkinSummaryStatus') }}:</strong> {{ $checkinSummary['active'] ? __('messages.pages.client.register.checkinStatusActive') : __('messages.pages.client.register.checkinStatusInactive') }}</div>
                                    <div><strong>{{ __('messages.pages.client.register.checkinSummaryInterval') }}:</strong> {{ __('messages.pages.client.register.checkinSummaryIntervalValue', ['days' => $checkinSummary['interval_days']]) }}</div>
                                    <div><strong>{{ __('messages.pages.client.register.checkinSummaryNextDate') }}:</strong> {{ $checkinSummary['next_checkin_date'] ?: __('messages.pages.client.register.checkinSummaryNotScheduled') }}</div>
                                    <div><strong>{{ __('messages.pages.client.register.checkinSummaryLastSent') }}:</strong> {{ $checkinSummary['last_checkin_sent_date'] ?: __('messages.pages.client.register.checkinSummaryNoSentYet') }}</div>
                                    <div><strong>{{ __('messages.pages.client.register.checkinSummaryLastResponse') }}:</strong> {{ $checkinSummary['last_checkin_responded_date'] ?: __('messages.pages.client.register.checkinSummaryNoResponseYet') }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="alert alert-light border mb-3">
                            <strong>{{ __('messages.pages.client.register.checkinRuleTitle') }}</strong>
                            <div class="mt-1">{{ __('messages.pages.client.register.checkinRuleBody') }}</div>
                        </div>

                        <a class="btn btn-primary btn-sm" href="{{ route('app.checkin.config', ['clientCodedId' => $CLIENT->codedId]) }}">
                            {{ __('messages.pages.client.register.btnConfigureCheckin') }}
                        </a>
                    @else
                        @include('app.placeholder-premium', [
                            'DIV_CLASSES' => 'w-100 h-auto',
                            'TITLE' => __('messages.components.Features.premiumFeature'),
                            'DESCRIPTION' => __('messages.pages.client.register.checkinDescription') . ' ' . __('messages.pages.client.register.checkinRuleBody') . ' ' . __('messages.pages.client.register.checkinPremiumHint'),
                            'CTA_LABEL' => __('messages.pages.avaliation.modalAddAvaliation.ctaViewPremiumBenefits'),
                            'CTA_URL' => route('app.subscription.upgrade'),
                        ])
                    @endif
                </x-card>
            @endif
        @endif

        <div class="form-actions">
            <div class="text-right">
                @if ($canEdit)
                    <button type="submit" class="btn primary btn-user">{{ __('messages.buttonSave') }}</button>
                @endif

                <a href="{{ url()->previous() }}" class="btn btn-light">{{ __('messages.buttonBackToList') }}</a>
            </div>
        </div>
    </form>

    @if ($Constants::FORM_ADD === $TYPE && $isFirstClient)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const nameInput = document.getElementById('f-name');
                const surnameInput = document.getElementById('f-surname');
                const onboardingInput = document.getElementById('f-onboarding-create-first-avaliation');
                const fillSelfBtn = document.getElementById('btn-fill-self-client');
                const fillAndStartBtn = document.getElementById('btn-fill-self-and-start-avaliation');

                const selfFirstName = @json($prefillClient['first_name'] ?? '');
                const selfLastName = @json($prefillClient['last_name'] ?? '');

                const fillFields = function () {
                    if (nameInput && !nameInput.value) {
                        nameInput.value = selfFirstName;
                    }

                    if (surnameInput && !surnameInput.value) {
                        surnameInput.value = selfLastName;
                    }
                };

                if (fillSelfBtn) {
                    fillSelfBtn.addEventListener('click', function() {
                        fillFields();
                        onboardingInput.value = '0';
                    });
                }

                if (fillAndStartBtn) {
                    fillAndStartBtn.addEventListener('click', function() {
                        fillFields();
                        onboardingInput.value = '1';
                    });
                }
            });
        </script>
    @endif
@endsection
