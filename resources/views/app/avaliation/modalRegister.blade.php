@inject('SysUtils', 'App\Helpers\SysUtils')
@inject('mAvaliation', 'App\Models\Avaliation')
@inject('mClient', 'App\Models\Client')
@inject('mUserInfo', 'App\Models\UserInfo')
@inject('AvaliationPictures', 'App\Helpers\Feature\AvaliationPictures')
@inject('RevaluationDate', 'App\Helpers\Feature\RevaluationDate')

@php
/*
View variables:
===============
    - $AVALIATION: App\Models\Avaliation|null
    - $CUID: string (Client Coded ID)
    - $CEDIT: string (bool 0/1)
    - $ACTION: string
===============
*/

$AVALIATION = $AVALIATION ?? null;
$CUID = $CUID ?? '';
$ACTION = $ACTION ?? '';
$canEdit = (1 == $CEDIT) ? true: false;
$Client = $mClient::getModelByCodedId($CUID);
$UserEvaluationMode = $SysUtils::getLoggedInUser()?->info?->evaluation_mode ?? $mUserInfo::EVALUATION_MODE_PERSONAL;
$APicFeature = new $AvaliationPictures();
$RevDateFeature = new $RevaluationDate();
@endphp

@extends('layout.modal', [
    'divId' => 'avaliation-modal-register' . date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        @if ($canEdit && !$AVALIATION)
            {{ __('messages.modalAddTitle', [
                'modelName' => __('messages.models.Avaliation.name')
            ]) }}
        @elseif ($canEdit && $AVALIATION?->id > 0)
            {{ __('messages.modalEditTitle', [
                'modelName' => __('messages.models.Avaliation.name')
            ]) }}
        @else
            {{ __('messages.modalViewTitle', [
                'modelName' => __('messages.models.Avaliation.name')
            ]) }}
        @endif
        <br />
        <small style="position:relative; top:-8px;">
            {{ $Client?->getName() ?? '' }}
        </small>
    </h5>

    <div class="float-right">
        <a href="javascript:;" class="btn btn-sm btn-secondary btn-user btn-nav btn-prev" disabled data-idx="1">
            {{ __('messages.buttonPrevious') }}
        </a>
        <a href="javascript:;" class="btn btn-sm btn-secondary btn-user btn-nav btn-next" data-idx="2">
            {{ __('messages.buttonNext') }}
        </a>
    </div>
@endsection

@section('MODAL_BODY')
    <form id="register-avaliation-form" method="POST" action="{{ $ACTION }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="f-cid" value="{{ $CUID }}" />
        <input type="hidden" name="f-cedit" value="{{ $CEDIT ?? 0 }}" />
        <input type="hidden" name="f-acid" value="{{ ($canEdit) ? $AVALIATION?->codedId: '' }}" />

        <div id="raf-page-1" data-idx="1">
            <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageOneTitle') }}">
                <div class="alert alert-info py-2">
                    <p class="mb-1 font-weight-bold">
                        {{ $UserEvaluationMode === $mUserInfo::EVALUATION_MODE_PROFESSIONAL
                            ? __('messages.pages.avaliation.modalAddAvaliation.quickIntroProfessionalTitle')
                            : __('messages.pages.avaliation.modalAddAvaliation.quickIntroTitle') }}
                    </p>
                    <small class="d-block">
                        {{ $UserEvaluationMode === $mUserInfo::EVALUATION_MODE_PROFESSIONAL
                            ? __('messages.pages.avaliation.modalAddAvaliation.quickIntroProfessionalDescription')
                            : __('messages.pages.avaliation.modalAddAvaliation.quickIntroDescription') }}
                    </small>
                </div>

                <div class="form-row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="* {{ __('messages.models.Avaliation.fields.date') }}">
                                * {{ __('messages.models.Avaliation.fields.date') }}
                            </label>
                            @php
                            $date = old('f-date') ?: ($AVALIATION?->date ? $SysUtils::reformatDate($AVALIATION?->date, 'Y-m-d', __('messages.dateFormat')) : null);
                            @endphp
                            <input type="text" class="form-control form-control-user jq-datepicker"
                                id="f-date" name="f-date" maxlength="10" value="{{ $date ?? $SysUtils::timezoneNow(__('messages.dateFormat')) }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.calculate_perc_fat_by') }}">
                                * {{ __('messages.models.Avaliation.fields.calculate_perc_fat_by') }}
                            </label>
                            <select
                                {{ ($canEdit) ?: 'disabled' }}
                                class="form-control form-control-user"
                                id="f-cfpb"
                                name="f-cfpb"
                            >
                                @php
                                $avaliationCalcBy = old("f-cfpb") ?: ($AVALIATION?->calculate_perc_fat_by ?: $mAvaliation::CALCULATE_PERC_FAT_BY_MEASURES);
                                @endphp

                                @foreach (array_merge(
                                    ['' => __('messages.selectEmptyOption') ],
                                    $mAvaliation::fGetCalculatePercFatBy()
                                ) as $val => $display)
                                    <option
                                        value="{{ $val }}"
                                        {{ $val !== $avaliationCalcBy ? '': 'selected' }}
                                    >{{ $display }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.quickModeHint') }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-12">
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="f-show-advanced" name="f-show-advanced" {{ $UserEvaluationMode === $mUserInfo::EVALUATION_MODE_PROFESSIONAL ? 'checked' : '' }} />
                            <label class="custom-control-label" for="f-show-advanced">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.quickModeLabel') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Client.fields.weight') }} (kg)">
                                * {{ __('messages.models.Client.fields.weight') }} (kg)
                            </label>
                            @php
                            $weight = old("f-weight") ?: (($AVALIATION?->weight_kg) ? number_format($AVALIATION?->weight_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-weight" name="f-weight" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $weight }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.age') }}">
                                {{ __('messages.models.Avaliation.fields.age') }}
                            </label>
                            <input readonly type="text" class="form-control form-control-user" value="{{ $AVALIATION?->age ?: $Client?->getAge() }}" />
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="d-none" id="raf-page-2" data-idx="2" data-flow-hidden="1">
            <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageTwoTitle') }}">
                <div class="form-row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.body_fat_perc') }} (%)">
                                {{ __('messages.models.Avaliation.fields.body_fat_perc') }} (%)
                            </label>
                            @php
                            $bfat = old("f-bfat") ?: (($AVALIATION?->body_fat_perc) ? number_format($AVALIATION?->body_fat_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-bfat" name="f-bfat" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $bfat }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skeletal_muscle_perc') }} (%)">
                                @php
                                $dataContent = __('messages.pages.avaliation.modalAddAvaliation.skeletal_muscle_perc_info', [
                                    'skeletal_muscle_perc' => __('messages.models.Avaliation.fields.skeletal_muscle_perc')
                                ]);
                                @endphp
                                <x-info-icon-modal
                                    title="{{ __('messages.infoModalTitle') }}"
                                    :message="$dataContent"
                                />

                                {{ __('messages.models.Avaliation.fields.skeletal_muscle_perc') }} (%)
                            </label>
                            @php
                            $skeletalMp = old("f-skeletal_mp") ?: (($AVALIATION?->skeletal_muscle_perc) ? number_format($AVALIATION?->skeletal_muscle_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-skeletal_mp" name="f-skeletal_mp" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $skeletalMp }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.muscle_mass_perc') }} (%)">
                                {{ __('messages.models.Avaliation.fields.muscle_mass_perc') }} (%)
                            </label>
                            @php
                            $muscleMp = old("f-muscle_mp") ?: (($AVALIATION?->muscle_mass_perc) ? number_format($AVALIATION?->muscle_mass_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-muscle_mp" name="f-muscle_mp" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $muscleMp }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.visceral_fat_level') }}">
                                @php
                                $dataContent = __('messages.pages.avaliation.modalAddAvaliation.visceral_fat_level_info');
                                @endphp
                                <x-info-icon-modal
                                    title="{{ __('messages.infoModalTitle') }}"
                                    :message="$dataContent"
                                />

                                {{ __('messages.models.Avaliation.fields.visceral_fat_level') }}
                            </label>
                            @php
                            $visceralFat = old("f-visceral_fat") ?: (($AVALIATION?->visceral_fat_level) ? number_format($AVALIATION?->visceral_fat_level, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-visceral_fat" name="f-visceral_fat" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $visceralFat }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.basal_metabolism') }} (kcal)">
                                {{ __('messages.models.Avaliation.fields.basal_metabolism') }} (kcal)
                            </label>
                            @php
                            $basal = old("f-basal") ?: (($AVALIATION?->basal_metabolism) ? number_format($AVALIATION?->basal_metabolism, 0, __('messages.decimalSeparator'), ''): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-basal" name="f-basal" maxlength="4"
                                data-thousands="" data-decimal=""
                                data-precision="0" value="{{ $basal }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.body_age') }}">
                                {{ __('messages.models.Avaliation.fields.body_age') }}
                            </label>
                            @php
                            $bdAge = old("f-bage") ?: (($AVALIATION?->body_age) ? number_format($AVALIATION?->body_age, 0, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-bage" name="f-bage" maxlength="3"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="0" value="{{ $bdAge }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.body_water_perc') }} (%)">
                                {{ __('messages.models.Avaliation.fields.body_water_perc') }} (%)
                            </label>
                            @php
                            $bWater = old("f-bwater") ?: (($AVALIATION?->body_water_perc) ? number_format($AVALIATION?->body_water_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-bwater" name="f-bwater" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $bWater }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.bone_mass_kg') }} (kg)">
                                {{ __('messages.models.Avaliation.fields.bone_mass_kg') }} (kg)
                            </label>
                            @php
                            $boneMass = old("f-bmass") ?: (($AVALIATION?->bone_mass_kg) ? number_format($AVALIATION?->bone_mass_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-bmass" name="f-bmass" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $boneMass }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card closed="1" title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageTwoSubOneTitle') }}">
                <p class="mb-0 font-weight-bold">
                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftArm') }}
                </p>
                <div class="form-row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}
                            </label>
                            @php
                            $value = old("f-la-lmass-kg") ?: (($AVALIATION?->left_arm_lean_mass_kg) ? number_format($AVALIATION?->left_arm_lean_mass_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-la-lmass-kg" name="f-la-lmass-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}
                            </label>
                            @php
                            $value = old("f-la-lmass-perc") ?: (($AVALIATION?->left_arm_lean_mass_perc) ? number_format($AVALIATION?->left_arm_lean_mass_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-la-lmass-perc" name="f-la-lmass-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}
                            </label>
                            @php
                            $value = old("f-la-fat-kg") ?: (($AVALIATION?->left_arm_fat_kg) ? number_format($AVALIATION?->left_arm_fat_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-la-fat-kg" name="f-la-fat-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}
                            </label>
                            @php
                            $value = old("f-la-fat-perc") ?: (($AVALIATION?->left_arm_fat_perc) ? number_format($AVALIATION?->left_arm_fat_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-la-fat-perc" name="f-la-fat-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <p class="mb-0 font-weight-bold">
                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelRightArm') }}
                </p>
                <div class="form-row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}
                            </label>
                            @php
                            $value = old("f-ra-lmass-kg") ?: (($AVALIATION?->right_arm_lean_mass_kg) ? number_format($AVALIATION?->right_arm_lean_mass_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ra-lmass-kg" name="f-ra-lmass-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}
                            </label>
                            @php
                            $value = old("f-ra-lmass-perc") ?: (($AVALIATION?->right_arm_lean_mass_perc) ? number_format($AVALIATION?->right_arm_lean_mass_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ra-lmass-perc" name="f-ra-lmass-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}
                            </label>
                            @php
                            $value = old("f-ra-fat-kg") ?: (($AVALIATION?->right_arm_fat_kg) ? number_format($AVALIATION?->right_arm_fat_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ra-fat-kg" name="f-ra-fat-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}
                            </label>
                            @php
                            $value = old("f-ra-fat-perc") ?: (($AVALIATION?->right_arm_fat_perc) ? number_format($AVALIATION?->right_arm_fat_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ra-fat-perc" name="f-ra-fat-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <p class="mb-0 font-weight-bold">
                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelTrunk') }}
                </p>
                <div class="form-row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}
                            </label>
                            @php
                            $value = old("f-tr-lmass-kg") ?: (($AVALIATION?->trunk_lean_mass_kg) ? number_format($AVALIATION?->trunk_lean_mass_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-tr-lmass-kg" name="f-tr-lmass-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}
                            </label>
                            @php
                            $value = old("f-tr-lmass-perc") ?: (($AVALIATION?->trunk_lean_mass_perc) ? number_format($AVALIATION?->trunk_lean_mass_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-tr-lmass-perc" name="f-tr-lmass-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}
                            </label>
                            @php
                            $value = old("f-tr-fat-kg") ?: (($AVALIATION?->trunk_fat_kg) ? number_format($AVALIATION?->trunk_fat_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-tr-fat-kg" name="f-tr-fat-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}
                            </label>
                            @php
                            $value = old("f-tr-fat-perc") ?: (($AVALIATION?->trunk_fat_perc) ? number_format($AVALIATION?->trunk_fat_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-tr-fat-perc" name="f-tr-fat-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <p class="mb-0 font-weight-bold">
                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftLeg') }}
                </p>
                <div class="form-row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}
                            </label>
                            @php
                            $value = old("f-ll-lmass-kg") ?: (($AVALIATION?->left_leg_lean_mass_kg) ? number_format($AVALIATION?->left_leg_lean_mass_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ll-lmass-kg" name="f-ll-lmass-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}
                            </label>
                            @php
                            $value = old("f-ll-lmass-perc") ?: (($AVALIATION?->left_leg_lean_mass_perc) ? number_format($AVALIATION?->left_leg_lean_mass_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ll-lmass-perc" name="f-ll-lmass-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}
                            </label>
                            @php
                            $value = old("f-ll-fat-kg") ?: (($AVALIATION?->left_leg_fat_kg) ? number_format($AVALIATION?->left_leg_fat_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ll-fat-kg" name="f-ll-fat-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}
                            </label>
                            @php
                            $value = old("f-ll-fat-perc") ?: (($AVALIATION?->left_leg_fat_perc) ? number_format($AVALIATION?->left_leg_fat_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-ll-fat-perc" name="f-ll-fat-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>

                <p class="mb-0 font-weight-bold">
                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelRightLeg') }}
                </p>
                <div class="form-row">
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassKg') }}
                            </label>
                            @php
                            $value = old("f-rl-lmass-kg") ?: (($AVALIATION?->right_leg_lean_mass_kg) ? number_format($AVALIATION?->right_leg_lean_mass_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rl-lmass-kg" name="f-rl-lmass-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeanMassPerc') }}
                            </label>
                            @php
                            $value = old("f-rl-lmass-perc") ?: (($AVALIATION?->right_leg_lean_mass_perc) ? number_format($AVALIATION?->right_leg_lean_mass_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rl-lmass-perc" name="f-rl-lmass-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatKg') }}
                            </label>
                            @php
                            $value = old("f-rl-fat-kg") ?: (($AVALIATION?->right_leg_fat_kg) ? number_format($AVALIATION?->right_leg_fat_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rl-fat-kg" name="f-rl-fat-kg" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelFatPerc') }}
                            </label>
                            @php
                            $value = old("f-rl-fat-perc") ?: (($AVALIATION?->right_leg_fat_perc) ? number_format($AVALIATION?->right_leg_fat_perc, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rl-fat-perc" name="f-rl-fat-perc" maxlength="4"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="d-none" id="raf-page-3" data-idx="3">
            <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageThreeTitle') }}">
                <p class="mb-0 font-weight-bold">
                    @php
                    $dataContent = view('app.avaliation.partials.measuresPopup')->render();
                    @endphp
                    <x-info-icon-modal
                        title="{{ __('messages.infoModalTitle') }}"
                        :message="$dataContent"
                    />

                    {{ __('messages.pages.avaliation.modalAddAvaliation.pageThreeSubOneTitle') }}
                </p>
                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelChestCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelChestCirc') }}
                            </label>
                            @php
                            $value = old("f-chest_circ") ?: (($AVALIATION?->chest_circ_cm) ? number_format($AVALIATION?->chest_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-chest_circ" name="f-chest_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelRightArmCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelRightArmCirc') }}
                            </label>
                            @php
                            $value = old("f-rarm_circ") ?: (($AVALIATION?->right_arm_circ_cm) ? number_format($AVALIATION?->right_arm_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rarm_circ" name="f-rarm_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftArmCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftArmCirc') }}
                            </label>
                            @php
                            $value = old("f-rarm_circ") ?: (($AVALIATION?->left_arm_circ_cm) ? number_format($AVALIATION?->left_arm_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-larm_circ" name="f-larm_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelWaistCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelWaistCirc') }}
                            </label>
                            @php
                            $value = old("f-waist_circ") ?: (($AVALIATION?->waist_circ_cm) ? number_format($AVALIATION?->waist_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-waist_circ" name="f-waist_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelRightForearmCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelRightForearmCirc') }}
                            </label>
                            @php
                            $value = old("f-rfarm_circ") ?: (($AVALIATION?->right_forearm_circ_cm) ? number_format($AVALIATION?->right_forearm_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rfarm_circ" name="f-rfarm_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftForearmCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftForearmCirc') }}
                            </label>
                            @php
                            $value = old("f-lfarm_circ") ?: (($AVALIATION?->left_forearm_circ_cm) ? number_format($AVALIATION?->left_forearm_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-lfarm_circ" name="f-lfarm_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelAbdomenCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelAbdomenCirc') }}
                            </label>
                            @php
                            $value = old("f-abd_circ") ?: (($AVALIATION?->abdomen_circ_cm) ? number_format($AVALIATION?->abdomen_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-abd_circ" name="f-abd_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelRightThighCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelRightThighCirc') }}
                            </label>
                            @php
                            $value = old("f-rthi_circ") ?: (($AVALIATION?->right_thigh_circ_cm) ? number_format($AVALIATION?->right_thigh_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rthi_circ" name="f-rthi_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftThighCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftThighCirc') }}
                            </label>
                            @php
                            $value = old("f-lthi_circ") ?: (($AVALIATION?->left_thigh_circ_cm) ? number_format($AVALIATION?->left_thigh_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-lthi_circ" name="f-lthi_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelHipCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelHipCirc') }}
                            </label>
                            @php
                            $value = old("f-hip_circ") ?: (($AVALIATION?->hip_circ_cm) ? number_format($AVALIATION?->hip_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-hip_circ" name="f-hip_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelRightCalfCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelRightCalfCirc') }}
                            </label>
                            @php
                            $value = old("f-rcalf_circ") ?: (($AVALIATION?->right_calf_circ_cm) ? number_format($AVALIATION?->right_calf_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-rcalf_circ" name="f-rcalf_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftCalfCirc') }} (cm)">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.labelLeftCalfCirc') }}
                            </label>
                            @php
                            $value = old("f-lcalf_circ") ?: (($AVALIATION?->left_calf_circ_cm) ? number_format($AVALIATION?->left_calf_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-lcalf_circ" name="f-lcalf_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label text-decoration-underline" title="{{ __('messages.models.Avaliation.fields.neck_circ_cm') }}">
                                {{ __('messages.models.Avaliation.fields.neck_circ_cm') }}
                            </label>
                            @php
                            $value = old("f-neck_circ") ?: (($AVALIATION?->neck_circ_cm) ? number_format($AVALIATION?->neck_circ_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money"
                                id="f-neck_circ" name="f-neck_circ" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="d-none" id="raf-page-4" data-idx="4" data-flow-hidden="1">
            <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageFourTitle') }}">
                <div class="form-row">
                    <div class="col-12">
                        <div class="form-group">
                            <span class="data-sf" data-sf-code="3jp">
                                @php
                                $dataContent = view('app.avaliation.partials.threeSiteSkinfoldJacksonPollockPopup')->render();
                                @endphp
                                <x-info-icon-modal
                                    title="{{ __('messages.infoModalTitle') }}"
                                    :message="$dataContent"
                                />
                            </span>
                            <span class="data-sf" data-sf-code="7jp">
                                @php
                                $dataContent = view('app.avaliation.partials.sevenSiteSkinfoldJacksonPollockPopup')->render();
                                @endphp
                                <x-info-icon-modal
                                    title="{{ __('messages.infoModalTitle') }}"
                                    :message="$dataContent"
                                />
                            </span>
                            <span class="data-sf" data-sf-code="4dw">
                                @php
                                $dataContent = view('app.avaliation.partials.fourSiteSkinfoldDurninWomersleyPopup')->render();
                                @endphp
                                <x-info-icon-modal
                                    title="{{ __('messages.infoModalTitle') }}"
                                    :message="$dataContent"
                                />
                            </span>

                            <label class="form-label" title="{{ __('messages.models.Avaliation.labelSkinFoldsFormula') }}">
                                {{ __('messages.models.Avaliation.labelSkinFoldsFormula') }}
                            </label>
                            <select
                                {{ ($canEdit) ?: 'disabled' }}
                                class="form-control form-control-user"
                                id="f-sf-form"
                                name="f-sf-form"
                            >
                                @php
                                $skinFoldFormula = old("f-sf-form") ?: $AVALIATION?->skin_folds_formula;
                                @endphp

                                @foreach (array_merge(
                                    ['' => __('messages.selectEmptyOption') ],
                                    $mAvaliation::fGetSkinFoldFormulas()
                                ) as $val => $display)
                                    <option
                                        value="{{ $val }}"
                                        {{ $val !== $skinFoldFormula ? '': 'selected' }}
                                    >{{ $display }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_chest_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_chest_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_chest") ?: (($AVALIATION?->skin_folds_chest_cm) ? number_format($AVALIATION?->skin_folds_chest_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_chest" name="f-skin_folds_chest" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="1" data-sf-3jp-f="0"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="0" data-sf-4dw-f="0"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_abdominal_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_abdominal_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_abdominal") ?: (($AVALIATION?->skin_folds_abdominal_cm) ? number_format($AVALIATION?->skin_folds_abdominal_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_abdominal" name="f-skin_folds_abdominal" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="1" data-sf-3jp-f="0"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="0" data-sf-4dw-f="0"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_thigh_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_thigh_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_thigh") ?: (($AVALIATION?->skin_folds_thigh_cm) ? number_format($AVALIATION?->skin_folds_thigh_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_thigh" name="f-skin_folds_thigh" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="1" data-sf-3jp-f="1"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="0" data-sf-4dw-f="0"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_tricep_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_tricep_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_tricep") ?: (($AVALIATION?->skin_folds_tricep_cm) ? number_format($AVALIATION?->skin_folds_tricep_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_tricep" name="f-skin_folds_tricep" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="0" data-sf-3jp-f="1"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="1" data-sf-4dw-f="1"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_axilla_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_axilla_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_axilla") ?: (($AVALIATION?->skin_folds_axilla_cm) ? number_format($AVALIATION?->skin_folds_axilla_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_axilla" name="f-skin_folds_axilla" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="0" data-sf-3jp-f="0"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="0" data-sf-4dw-f="0"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_subscapular_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_subscapular_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_subscapular") ?: (($AVALIATION?->skin_folds_subscapular_cm) ? number_format($AVALIATION?->skin_folds_subscapular_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_subscapular" name="f-skin_folds_subscapular" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="0" data-sf-3jp-f="0"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="1" data-sf-4dw-f="1"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_suprailiac_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_suprailiac_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_suprailiac") ?: (($AVALIATION?->skin_folds_suprailiac_cm) ? number_format($AVALIATION?->skin_folds_suprailiac_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_suprailiac" name="f-skin_folds_suprailiac" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="0" data-sf-3jp-f="1"
                                data-sf-7jp-m="1" data-sf-7jp-f="1"
                                data-sf-4dw-m="1" data-sf-4dw-f="1"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label class="form-label" title="{{ __('messages.models.Avaliation.fields.skin_folds_bicep_cm') }} (cm)">
                                {{ __('messages.models.Avaliation.fields.skin_folds_bicep_cm') }} (cm)
                            </label>
                            @php
                            $value = old("f-skin_folds_bicep") ?: (($AVALIATION?->skin_folds_bicep_cm) ? number_format($AVALIATION?->skin_folds_bicep_cm, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                            @endphp
                            <input type="text" class="form-control form-control-user jq-mask-money data-sf"
                                id="f-skin_folds_bicep" name="f-skin_folds_bicep" maxlength="5"
                                data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                                data-precision="1" value="{{ $value }}"
                                data-sf-3jp-m="0" data-sf-3jp-f="0"
                                data-sf-7jp-m="0" data-sf-7jp-f="0"
                                data-sf-4dw-m="1" data-sf-4dw-f="1"
                                {{ $canEdit ? '': 'disabled' }}
                            />
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="d-none" id="raf-page-5" data-idx="5">
            @if ($canEdit)
                <div id="quick-actions-card">
                    <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.quickActionsTitle') }}">
                        <div id="quick-actions-block">
                            <button type="button" class="btn btn-sm btn-primary btn-user btn-modal-submit">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.quickSaveNow') }}
                            </button>
                            <a href="javascript:;" class="btn btn-sm btn-link" id="btn-open-advanced-pages">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.quickOpenExtras') }}
                            </a>
                        </div>
                    </x-card>
                </div>
            @endif

            <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageFiveTitle') }}">
                <div class="form-row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label" title="* {{ __('messages.models.Avaliation.fields.revaluation_date') }}">
                                {{ __('messages.models.Avaliation.fields.revaluation_date') }}
                            </label>

                            @if (!$RevDateFeature->validate())
                                @include('app.placeholder-premium', [
                                    'DIV_CLASSES' => 'w-100 h-auto',
                                    'TITLE' => __('messages.components.Features.premiumFeature'),
                                    'DESCRIPTION' => __('messages.components.Features.RevaluationDate.logoPlaceholderText', [
                                        'fieldLabel' => __('messages.models.Avaliation.fields.revaluation_date')
                                    ]),
                                    'CTA_LABEL' => __('messages.pages.avaliation.modalAddAvaliation.ctaViewPremiumBenefits'),
                                    'CTA_URL' => route('app.subscription.upgrade'),
                                    'CTA_TARGET' => '_blank',
                                ])
                            @else
                                @php
                                $value = old('f-rev-date') ?: ($AVALIATION?->revaluation_date ? $SysUtils::reformatDate($AVALIATION?->revaluation_date, 'Y-m-d', __('messages.dateFormat')) : null);
                                @endphp
                                <input type="text" class="form-control form-control-user jq-datepicker"
                                    id="f-rev-date" name="f-rev-date" maxlength="10" value="{{ $value ?? '' }}"
                                    {{ $canEdit ? '': 'disabled' }}
                                />
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label m-0" title="{{ __('messages.models.Avaliation.fields.client_notes') }}">
                                {{ __('messages.models.Avaliation.fields.client_notes') }}
                                <br />
                                <small class="text-muted" style="position:relative; top:-7px;">
                                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelClientNotes') }}
                                </small>
                            </label>
                            @php
                            $value = old("f-cnotes") ?: $AVALIATION?->client_notes;
                            @endphp
                            <textarea
                                {{ ($canEdit) ?: 'disabled' }}
                                class="form-control form-control-user"
                                id="f-cnotes"
                                name="f-cnotes"
                            >{{ $value }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label m-0" title="{{ __('messages.models.Avaliation.fields.private_notes') }}">
                                {{ __('messages.models.Avaliation.fields.private_notes') }}
                                <br />
                                <small class="text-muted" style="position:relative; top:-7px;">
                                    {{ __('messages.pages.avaliation.modalAddAvaliation.labelPrivateNotes') }}
                                </small>
                            </label>
                            @php
                            $value = old("f-pnotes") ?: $AVALIATION?->private_notes;
                            @endphp
                            <textarea
                                {{ ($canEdit) ?: 'disabled' }}
                                class="form-control form-control-user"
                                id="f-pnotes"
                                name="f-pnotes"
                            >{{ $value }}</textarea>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="d-none" id="raf-page-6" data-idx="6" data-flow-hidden="1">
            <x-card title="{{ __('messages.pages.avaliation.modalAddAvaliation.pageSixTitle') }}">
                <div class="form-row">
                    @if ($APicFeature->validate())
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label m-0" title="{{ __('messages.models.Avaliation.fields.photo_front_url') }}">
                                    {{ __('messages.models.Avaliation.fields.photo_front_url') }}
                                </label>

                                @include('app.avaliation.partials.photoInput', [
                                    'MODEL' => $AVALIATION,
                                    'FIELD_NAME' => 'photo_front_url',
                                    'INPUT_NAME' => 'f-photo_front_url',
                                    'INPUT_DEFAULT_IMAGE' => '/images/photo_front.jpg',
                                    'IMG_ALT' => __('messages.models.Avaliation.fields.photo_front_url'),
                                    'CAN_EDIT' => $canEdit
                                ])
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label m-0" title="{{ __('messages.models.Avaliation.fields.photo_right_url') }}">
                                    {{ __('messages.models.Avaliation.fields.photo_right_url') }}
                                </label>

                                @include('app.avaliation.partials.photoInput', [
                                    'MODEL' => $AVALIATION,
                                    'FIELD_NAME' => 'photo_right_url',
                                    'INPUT_NAME' => 'f-photo_right_url',
                                    'INPUT_DEFAULT_IMAGE' => '/images/photo_right.jpg',
                                    'IMG_ALT' => __('messages.models.Avaliation.fields.photo_right_url'),
                                    'CAN_EDIT' => $canEdit
                                ])
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label m-0" title="{{ __('messages.models.Avaliation.fields.photo_rear_url') }}">
                                    {{ __('messages.models.Avaliation.fields.photo_rear_url') }}
                                </label>

                                @include('app.avaliation.partials.photoInput', [
                                    'MODEL' => $AVALIATION,
                                    'FIELD_NAME' => 'photo_rear_url',
                                    'INPUT_NAME' => 'f-photo_rear_url',
                                    'INPUT_DEFAULT_IMAGE' => '/images/photo_rear.jpg',
                                    'IMG_ALT' => __('messages.models.Avaliation.fields.photo_rear_url'),
                                    'CAN_EDIT' => $canEdit
                                ])
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label m-0" title="{{ __('messages.models.Avaliation.fields.photo_left_url') }}">
                                    {{ __('messages.models.Avaliation.fields.photo_left_url') }}
                                </label>

                                @include('app.avaliation.partials.photoInput', [
                                    'MODEL' => $AVALIATION,
                                    'FIELD_NAME' => 'photo_left_url',
                                    'INPUT_NAME' => 'f-photo_left_url',
                                    'INPUT_DEFAULT_IMAGE' => '/images/photo_left.jpg',
                                    'IMG_ALT' => __('messages.models.Avaliation.fields.photo_left_url'),
                                    'CAN_EDIT' => $canEdit
                                ])
                            </div>
                        </div>
                    @else
                        @include('app.placeholder-premium', [
                            'DIV_CLASSES' => 'w-100',
                            'TITLE' => __('messages.pages.avaliation.modalAddAvaliation.photosPremiumTitle'),
                            'DESCRIPTION' => __('messages.pages.avaliation.modalAddAvaliation.photosPremiumDescription'),
                            'CTA_LABEL' => __('messages.pages.avaliation.modalAddAvaliation.ctaViewPremiumBenefits'),
                            'CTA_URL' => route('app.subscription.upgrade'),
                            'CTA_TARGET' => '_blank',
                        ])
                    @endif
                </div>
            </x-card>
        </div>

        <div class="form-actions">
            <div class="float-right" style="position:relative; top:10px;">
                @if ($canEdit)
                    <button type="submit" class="btn-modal-submit btn btn-sm primary btn-user">{{ __('messages.buttonSave') }}</button>
                @endif

                <a href="javascript:;" class="btn-modal-close btn btn-sm btn-light" data-dismiss="modal">
                    {{ __('messages.buttonClose') }}
                </a>
            </div>
        </div>

        <p>
            <small class="text-muted">
                {{ __('messages.pages.avaliation.modalAddAvaliation.requiredFieldsInfo') }}
                <br />
                {{ __('messages.pages.avaliation.modalAddAvaliation.requiredFieldsInfo2') }}
            </small>
        </p>
    </form>

    <script>
        (function($) {
            $(document).ready(function() {
                const formEl = $('#register-avaliation-form');
                const modalContainer = formEl.closest('div[id^="avaliation-modal-register"]');

                function setFlowPageVisible(pageIdx, isVisible) {
                    const page = $(`#raf-page-${pageIdx}`);
                    const hiddenFlag = isVisible ? '0' : '1';
                    page.attr('data-flow-hidden', hiddenFlag);
                    page.data('flowHidden', hiddenFlag);
                    if (!isVisible) {
                        page.addClass('d-none');
                    }
                }

                function applyQuickFlowByMethod() {
                    const calcBy = $('#f-cfpb').val();
                    const showAdvanced = $('#f-show-advanced').is(':checked');
                    const quickActionsBlock = $('#quick-actions-block');
                    const quickActionsCard = $('#quick-actions-card');

                    function refreshQuickActionsVisibility() {
                        if (quickActionsCard.length === 0 || quickActionsBlock.length === 0) {
                            return;
                        }

                        const hasActions = quickActionsBlock.find('button, a').length > 0;
                        const hasVisibleActions = hasActions && !quickActionsBlock.hasClass('d-none');
                        quickActionsCard.toggleClass('d-none', !hasVisibleActions);
                    }

                    if (showAdvanced) {
                        setFlowPageVisible(2, true);
                        setFlowPageVisible(4, true);
                        setFlowPageVisible(6, true);
                        quickActionsBlock.addClass('d-none');
                    } else {
                        setFlowPageVisible(2, calcBy === '{{ $mAvaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE }}');
                        setFlowPageVisible(4, calcBy === '{{ $mAvaliation::CALCULATE_PERC_FAT_BY_SKINFOLD }}');
                        setFlowPageVisible(6, false);
                        quickActionsBlock.removeClass('d-none');
                    }

                    refreshQuickActionsVisibility();

                    modalContainer.trigger('avaliation:refresh-nav');
                }

                function goToPage(pageIdx) {
                    const modalBody = modalContainer.find('.modal-body').first();
                    modalBody.find('div[id^="raf-page-"]').addClass('d-none');
                    modalBody.find(`#raf-page-${pageIdx}`).removeClass('d-none');
                    modalContainer.trigger('avaliation:refresh-nav');
                }

                function displayInputs(formulaKey) {
                    let clientGender = '{!! $Client?->gender !!}';
                    clientGender = clientGender[0].toLowerCase();

                    // get json data
                    let skinFoldsInputCodeArr = {!! json_encode($mAvaliation::fGetJsonSkinFoldsInputCode()) !!};

                    // get code from json data
                    let skinFoldsInputCode = skinFoldsInputCodeArr[formulaKey];

                    // hide all input with class data-sf
                    $('input.data-sf').each(function() {
                        $(this).closest('.form-group').parent().hide();
                    });
                    $('span.data-sf').each(function() {
                        $(this).hide();
                    });

                    // show the inputs with the code from json data
                    let inputCode = `data-sf-${skinFoldsInputCode}-${clientGender}`;
                    $(`input[${inputCode}="1"]`).each(function() {
                        $(this).closest('.form-group').parent().show();
                    });
                    $(`span[data-sf-code="${skinFoldsInputCode}"]`).each(function() {
                        $(this).show();
                    });
                }

                // initialize skin folds fields
                let skinFoldsFormulaKey = $('#f-sf-form').val(); // (ex: "3_FOLDS_JACKSON_POLLOCK")
                displayInputs(skinFoldsFormulaKey);

                // on change of f-sf-form
                $('#f-sf-form').on('change', function() {
                    let skinFoldsFormulaKey = $(this).val();
                    displayInputs(skinFoldsFormulaKey);
                });

                $('#f-cfpb').on('change', function() {
                    applyQuickFlowByMethod();
                });

                $('#f-show-advanced').on('change', function() {
                    applyQuickFlowByMethod();
                });

                $('#btn-open-advanced-pages').on('click', function(e) {
                    e.preventDefault();
                    $('#f-show-advanced').prop('checked', true).trigger('change');
                    goToPage(2);
                });

                if ('{{ $UserEvaluationMode }}' === '{{ $mUserInfo::EVALUATION_MODE_PROFESSIONAL }}') {
                    $('#f-show-advanced').prop('checked', true);
                }

                applyQuickFlowByMethod();
            });
        }(jQuery));
    </script>

@endsection
