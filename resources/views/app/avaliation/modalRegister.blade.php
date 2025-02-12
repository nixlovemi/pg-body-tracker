@inject('SysUtils', 'App\Helpers\SysUtils')

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
@endphp

@extends('layout.modal', [
    'divId' => date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ __('messages.pages.avaliation.modalAddAvaliation.title') }}
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="register-avaliation-form" method="POST" action="{{ $ACTION }}">
        @csrf
        <input type="hidden" name="f-cid" value="{{ $CUID }}" />
        <input type="hidden" name="f-cedit" value="{{ $CEDIT ?? 0 }}" />

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
                    <label class="form-label" title="{{ __('messages.models.Client.fields.weight') }} (kg)">
                        * {{ __('messages.models.Client.fields.weight') }} (kg)
                    </label>
                    @php
                    $weight = old("f-weight") ?: (($AVALIATION?->weight_kg) ? number_format($AVALIATION?->weight_kg, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                    @endphp
                    <input type="text" class="form-control form-control-user jq-mask-money"
                        id="f-weight" name="f-weight" maxlength="7"
                        data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                        data-precision="3" value="{{ $weight }}"
                        {{ $canEdit ? '': 'disabled' }}
                    />
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.models.Avaliation.fields.body_fat_perc') }} (%)">
                        * {{ __('messages.models.Avaliation.fields.body_fat_perc') }} (%)
                    </label>
                    @php
                    $bfat = old("f-bfat") ?: (($AVALIATION?->body_fat_perc) ? number_format($AVALIATION?->body_fat_perc, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                    @endphp
                    <input type="text" class="form-control form-control-user jq-mask-money"
                        id="f-bfat" name="f-bfat" maxlength="7"
                        data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                        data-precision="3" value="{{ $bfat }}"
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
                    $skeletalMp = old("f-skeletal_mp") ?: (($AVALIATION?->skeletal_muscle_perc) ? number_format($AVALIATION?->skeletal_muscle_perc, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                    @endphp
                    <input type="text" class="form-control form-control-user jq-mask-money"
                        id="f-skeletal_mp" name="f-skeletal_mp" maxlength="7"
                        data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                        data-precision="3" value="{{ $skeletalMp }}"
                        {{ $canEdit ? '': 'disabled' }}
                    />
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.models.Avaliation.fields.visceral_fat_kg') }} (kg)">
                        {{ __('messages.models.Avaliation.fields.visceral_fat_kg') }} (kg)
                    </label>
                    @php
                    $visceralFat = old("f-visceral_fat") ?: (($AVALIATION?->visceral_fat_kg) ? number_format($AVALIATION?->visceral_fat_kg, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                    @endphp
                    <input type="text" class="form-control form-control-user jq-mask-money"
                        id="f-visceral_fat" name="f-visceral_fat" maxlength="7"
                        data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                        data-precision="3" value="{{ $visceralFat }}"
                        {{ $canEdit ? '': 'disabled' }}
                    />
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.models.Avaliation.fields.waist_circumference_cm') }} (cm)">
                        @php
                        $dataContent = __('messages.pages.avaliation.modalAddAvaliation.waist_circumference_info', [
                            'visceral_fat' => __('messages.models.Avaliation.fields.visceral_fat_kg'),
                            'waist_circumference' => __('messages.models.Avaliation.fields.waist_circumference_cm'),
                        ]);
                        @endphp
                        <x-info-icon-modal
                            title="{{ __('messages.infoModalTitle') }}"
                            :message="$dataContent"
                        />

                        {{ __('messages.models.Avaliation.fields.waist_circumference_cm') }} (cm)
                    </label>
                    @php
                    $waistCirc = old("f-waist_circ") ?: (($AVALIATION?->waist_circumference_cm) ? number_format($AVALIATION?->waist_circumference_cm, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')): '');
                    @endphp
                    <input type="text" class="form-control form-control-user jq-mask-money"
                        id="f-waist_circ" name="f-waist_circ" maxlength="7"
                        data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                        data-precision="3" value="{{ $waistCirc }}"
                        {{ $canEdit ? '': 'disabled' }}
                    />
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="float-right">
                @if ($canEdit)
                    <button type="submit" class="btn-modal-submit btn btn-sm primary btn-user">{{ __('messages.buttonSave') }}</button>
                @endif

                <a href="javascript:;" class="btn-modal-close btn btn-sm btn-light" data-dismiss="modal">Fechar</a>
            </div>
        </div>
    </form>
@endsection
