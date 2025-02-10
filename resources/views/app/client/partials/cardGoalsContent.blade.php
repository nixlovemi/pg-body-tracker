@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $GOAL: App\Models\Goal|null
    - $CAN_EDIT: bool
    - $VIEW_ONLY: bool
*/

$CLIENT = $GOAL?->client;
$pastGoals = $CLIENT?->getPastGoals()?->get();
$VIEW_ONLY = $VIEW_ONLY ?? false;
@endphp

@if (!$GOAL && $CAN_EDIT && !$VIEW_ONLY)
    <div class="d-block mb-3">
        <a href="javascript:;" id="btn-client-new-goal" class="btn btn-light btn-user btn-sm">
            {{ __('messages.pages.client.register.btnNewGoal') }}
        </a>
    </div>
@endif

@if (!$pastGoals->isEmpty() && !$VIEW_ONLY)
    <div class="d-block mb-3">
        <a href="javascript:;" id="btn-client-past-goals" class="btn btn-light btn-user btn-sm">
            {{ __('messages.pages.client.register.btnOldGoals') }}
        </a>
    </div>
@endif

@if ($GOAL)
    <div class="form-row" id="card-client-goal-form-row-content">
        <div class="col-12 col-md-6">
            <div class="form-row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.models.Goal.fields.objective') }}</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ $GOAL?->getObjectivieString() }}"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.models.Goal.fields.target_weight') }} (kg)</label>
                        <input type="text" class="form-control form-control-user jq-mask-money"
                            disabled maxlength="7"
                            data-thousands="{{ __('messages.thousandSeparator') }}" data-decimal="{{ __('messages.decimalSeparator') }}"
                            data-precision="3" value="{{  number_format($GOAL?->target_weight_kg, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')) }}"
                        />
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.models.Goal.fields.deadline') }}</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ $SysUtils::reformatDate($GOAL?->deadline, 'Y-m-d', __('messages.dateFormat')) }}"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.pages.goal.modalAddGoal.labelDaysToDeadline') }}</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ $GOAL?->remainingDays() }}"
                        />
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.pages.goal.modalAddGoal.labelWeightChange') }} (kg)</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ ($GOAL?->isObjectiveWeightLoss() ? '-': '+') . number_format($GOAL?->totalWeightChangeSinceStart(), 2, __('messages.decimalSeparator'), __('messages.thousandSeparator')) }}"
                        />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('messages.pages.goal.modalAddGoal.labelProgress') }}</label>
                        <input type="text" class="form-control form-control-user"
                            disabled value="{{ number_format($GOAL?->percentageTowardsGoal(), 2, __('messages.decimalSeparator'), __('messages.thousandSeparator')) }}%"
                        />
                    </div>
                </div>
            </div>

            @csrf
            @if ($CAN_EDIT && !$VIEW_ONLY)
                <small class="d-block text-md-left text-center" class="text-muted">
                    <a href="javascript:;" id="btn-client-remove-goal"
                        style="color:gray !important; font-size:90%;"
                        data-confirm-title="{{ __('messages.confirmModalTitle') }}"
                        data-confirm-text="{{ __('messages.models.Goal.confirmDeleteModalText') }}"
                        data-gcid="{{ $GOAL?->codedId }}"
                        data-ccid="{{ $CLIENT?->codedId }}"
                        data-cedt="{{ $CAN_EDIT }}"
                    >
                        Remover objetivo?
                    </a>
                </small>
            @endif
        </div>

        <div class="col-12 col-md-6">
            <x-chart-client-goal
                :goalId="$GOAL?->id"
            />
        </div>
    </div>
@else
    {{ __('messages.pages.client.register.noGoals') }}
@endif
