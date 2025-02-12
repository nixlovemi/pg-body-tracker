<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Client;
use App\Helpers\SysUtils;

class Goal extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const OBJECTIVE_WEIGHT_LOSS = 'weight';
    public const OBJECTIVE_MUSCLE_GAIN = 'muscle';
    public const OBJECTIVE_HEALTH = 'health';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'objective',
        'target_weight_kg',
        'deadline',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [];

    protected $appends = [
        'codedId',
    ];

    // relations
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, __('messages.models.Goal.name'), 'id', 'ID');
        $validation->addIdField(Client::class, __('messages.models.Client.name'), 'id', 'ID');
        $validation->addField('objective', ['nullable', 'filled', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetObjectives())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.Goal.fields.objective')])
                );
            }
        }], __('messages.models.Goal.fields.objective'));
        $validation->addField('initial_weight_kg', ['required', 'numeric', 'min:30', 'max:400'], __('messages.models.Goal.fields.initial_weight'));
        $validation->addField('target_weight_kg', ['required', 'numeric', 'min:30', 'max:400', 'different:initial_weight_kg'], __('messages.models.Goal.fields.target_weight'));
        $validation->addField('deadline', ['required', 'date', 'date_format:Y-m-d', function ($attribute, $value, $fail) {
            // deadline must be greater than today
            if (strtotime($value) <= strtotime(SysUtils::timezoneNow('Y-m-d'))) {
                $fail(
                    __('messages.models.Goal.fSave.objectiveDateMustBeGreaterThanToday')
                );
            }
        }], __('messages.models.Goal.fields.deadline'));

        return $validation->validate();
    }

    public function getObjectivieString(): string
    {
        $objectivies = self::fGetObjectives();
        return $objectivies[$this->objective] ?? '';
    }

    public function remainingDays(): int
    {
        $today = new \DateTime(SysUtils::timezoneNow('Y-m-d'));
        $deadline = new \DateTime($this->deadline);
        if ($today >= $deadline) {
            return 0;
        }

        // calculate difference
        $diff = $today->diff($deadline);
        return $diff->days;
    }

    public function targetWeightChange(): float
    {
        $initial = $this->initial_weight_kg;
        $target = $this->target_weight_kg;
        return abs($initial - $target);
    }

    public function totalWeightChangeSinceStart(): float
    {
        $client = $this->client;
        if (!$client) {
            return 0;
        }

        $initial = $this->initial_weight_kg;
        $lastAvaliation = $client->avaliations()
            ->where('date', '<=', $this->deadline)
            ->orderBy('date', 'DESC')
            ->first();
        $last = $lastAvaliation?->weight_kg ?? 0;

        if ($this->isObjectiveWeightLoss()) {
            return $initial - $last;
        } else {
            return $last - $initial;
        }
    }

    public function isObjectiveWeightLoss(): bool
    {
        return $this->initial_weight_kg > $this->target_weight_kg;
    }

    public function isObjectiveMuscleGain(): bool
    {
        return $this->initial_weight_kg < $this->target_weight_kg;
    }

    public function percentageTowardsGoal(): float
    {
        $change = $this->targetWeightChange();
        if ($change == 0) return 0;

        $progress = $this->totalWeightChangeSinceStart() / $change;
        if ($progress < 0) {
            return 0;
        }

        return number_format($progress * 100, 2);
    }
    // ===============

    // static functions
    public static function fGetObjectives(): array
    {
        return [
            self::OBJECTIVE_WEIGHT_LOSS => __('messages.models.Goal.objective.weight'),
            self::OBJECTIVE_MUSCLE_GAIN => __('messages.models.Goal.objective.muscle'),
            self::OBJECTIVE_HEALTH => __('messages.models.Goal.objective.health'),
        ];
    }

    public static function fSave(array $form, ?string $codedId = null): ApiResponse
    {
        // get model for insert or update
        if (!empty($codedId)) {
            $Goal = self::getModelByCodedId($codedId);
            if ($Goal === null) {
                return new ApiResponse(true, __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.Goal.name'),
                ]));
            }
        } else {
            $Goal = new self();
        }
        $isEdit = ($Goal->id > 0);

        // check if user can save
        if (!self::fHasAccess($Goal)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => __('messages.models.Goal.name'),
            ]));
        }

        // fill model
        $Goal->fill($form);

        // default value for initial_weight_kg = Client current weight
        if (!$isEdit) {
            $Client = Client::find($Goal->client_id);
            if (!$Client) {
                return new ApiResponse(true, __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.Client.name')
                ]));
            }

            $Goal->initial_weight_kg = $Client?->getCurrentWeight();
        }

        // validate model
        $validation = $Goal->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $Goal->save();
            $Goal->refresh();
        } catch (\Exception $e) {
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => __('messages.models.Goal.name'),
            ]));
        }

        // all good, return success
        $msg = $isEdit ? __('messages.saveModelSuccessEditing', ['modelName' => __('messages.models.Goal.name')]) : __('messages.saveModelSuccessAdding', ['modelName' => __('messages.models.Goal.name')]);
        return new ApiResponse(false, $msg, [
            'Goal' => $Goal,
            'isEdit' => $isEdit,
        ]);
    }

    public static function fRemove(string $codedId): ApiResponse
    {
        $Goal = self::getModelByCodedId($codedId);
        if ($Goal === null) {
            return new ApiResponse(true, __('messages.saveModelNotFound', [
                'modelName' => __('messages.models.Goal.name'),
            ]));
        }

        // check if user can save
        if (!self::fHasAccess($Goal)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => __('messages.models.Goal.name'),
            ]));
        }

        // remove model
        try {
            $Goal->delete();
        } catch (\Exception $e) {
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => __('messages.models.Goal.name'),
            ]));
        }

        // all good, return success
        return new ApiResponse(false, __('messages.saveModelSuccessRemoving', ['modelName' => __('messages.models.Goal.name')]));
    }

    public static function fHasAccess(self $Goal): bool
    {
        // adding user is ok
        if (empty($Goal->id)) {
            return true;
        }

        // check logged user
        $lggdUser = SysUtils::getLoggedInUser();
        if (null === $lggdUser) {
            return false;
        }

        // root can save any client
        if ($lggdUser->role === User::ROLE_ROOT) {
            return true;
        }

        // check if user is trying to edit a goal from a client that is not his
        if ($Goal->id > 0 && $Goal->client->user_id !== $lggdUser->id) {
            return false;
        }

        return true;
    }
    // ================
}
