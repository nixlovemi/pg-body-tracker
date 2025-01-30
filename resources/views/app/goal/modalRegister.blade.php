@inject('mGoal', 'App\Models\Goal')

@php
/*
View variables:
    - $CUID: string
    - $ACTION: string
===============
*/

@endphp

@extends('layout.modal', [
    'divId' => date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ __('messages.pages.goal.modalAddGoal.title') }}
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="register-goal-form" method="POST" action="{{ $ACTION }}">
        @csrf
        <input type="hidden" name="f-cid" value="{{ $CUID }}" />

        <div class="form-row">
            <div class="col-12">
                <div class="form-group">
                    <label class="form-label">* {{ __('messages.models.Goal.fields.objective') }}</label>
                    <select
                        class="form-control form-control-user"
                        id="f-objective"
                        name="f-objective"
                    >
                        @foreach (array_merge(
                            ['' => __('messages.selectEmptyOption') ],
                            $mGoal::fGetObjectivies()
                        ) as $goal => $display)
                            <option
                                value="{{ $goal }}"
                            >{{ $display }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="form-label">* {{ __('messages.models.Goal.fields.target_weight') }} (kg)</label>
                    <input type="text" class="form-control form-control-user jq-mask-money"
                        id="f-weight" name="f-weight" maxlength="7"
                        data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                        data-precision="3" value=""
                    />
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="form-label">* {{ __('messages.models.Goal.fields.deadline') }}</label>
                    <input type="text" class="form-control form-control-user jq-datepicker"
                        id="f-deadline" name="f-deadline" maxlength="10" value=""
                    />
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="float-right">
                <button type="submit" class="btn-modal-submit btn btn-sm primary btn-user">{{ __('messages.buttonSave') }}</button>
                <a href="javascript:;" class="btn-modal-close btn btn-sm btn-light" data-dismiss="modal">Fechar</a>
            </div>
        </div>
    </form>
@endsection
