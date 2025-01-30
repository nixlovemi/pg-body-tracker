@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $CLIENT: App\Models\Client|null
    - $CAN_EDIT: bool
*/

$currentGoal = $CLIENT?->getCurrentGoal();
@endphp

@if (!$currentGoal && $CAN_EDIT)
    <div class="d-block mb-3">
        <a href="javascript:;" id="btn-client-new-goal" class="btn btn-light btn-user btn-sm">
            {{ __('messages.pages.client.register.btnNewGoal') }}
        </a>
    </div>
@endif

@if ($currentGoal)
    <div class="form-row">
        <div class="col-12 col-md-6">
            <div class="form-row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.models.Goal.fields.objective') }}</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ $currentGoal?->getObjectivieString() }}"
                        />
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.models.Goal.fields.target_weight') }} (kg)</label>
                        <input type="text" class="form-control form-control-user jq-mask-money"
                            disabled maxlength="7"
                            data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                            data-precision="3" value="{{  number_format($currentGoal?->target_weight_kg, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')) }}"
                        />
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.models.Goal.fields.deadline') }}</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ $SysUtils::reformatDate($currentGoal?->deadline, 'Y-m-d', __('messages.dateFormat')) }}"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <x-chart-client-goal
                :clientId="$CLIENT?->id"
            />
        </div>
    </div>
@else
    {{ __('messages.pages.client.register.noGoals') }}
@endif
