<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Avaliation;

class Client extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const GENDER_MALE = 'MALE';
    public const GENDER_FEMALE = 'FEMALE';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'birthdate',
        'weight_kg',
        'height_cm',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function goals()
    {
        return $this->hasMany(
            Goal::class, 'client_id',
            'id'
        );
    }

    public function avaliations()
    {
        return $this->hasMany(
            Avaliation::class, 'client_id',
            'id'
        );
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, __('messages.models.Client.name'), 'id', 'ID');
        $validation->addIdField(User::class, __('messages.models.User.name'), 'id', 'ID');
        $validation->addField('first_name', ['required', 'string', 'min:2', 'max:60'], __('messages.models.User.fields.name'));
        $validation->addField('last_name', ['required', 'string', 'min:2', 'max:80'], __('messages.models.User.fields.lastName'));
        $validation->addEmailField('email', 'E-mail', ['nullable', 'string', 'min:3', 'max:255']);
        $validation->addPhoneField('phone', __('messages.models.Client.fields.phone'), ['nullable', 'max:35']);
        $validation->addField('gender', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetGenders())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.Client.fields.gender')])
                );
            }
        }], __('messages.models.Client.fields.gender'));
        $validation->addField('birthdate', ['required', 'date', 'date_format:Y-m-d'], __('messages.models.Client.fields.birthdate'));
        $validation->addField('weight_kg', ['required', 'numeric', 'min:30', 'max:400'], __('messages.models.Client.fields.weight'));
        $validation->addField('height_cm', ['required', 'integer', 'min:60', 'max:250'], __('messages.models.Client.fields.height'));

        return $validation->validate();
    }

    public function getCurrentGoal(): ?Goal
    {
        return $this->goals()
            ->where('deadline', '>=', SysUtils::timezoneNow('Y-m-d'))
            ->orderBy('deadline', 'DESC')
            ->first();
    }

    public function getPastGoals(): ?\Illuminate\Database\Eloquent\Relations\Relation
    {
        return $this->goals()
            ->where('deadline', '<', SysUtils::timezoneNow('Y-m-d'))
            ->orderBy('deadline', 'DESC');
    }

    public function getCurrentWeight(): ?float
    {
        $avaliation = $this->avaliations()
            ->orderBy('date', 'DESC')
            ->first();
        if ($avaliation) {
            return $avaliation->weight_kg;
        }

        return $this->weight_kg;
    }

    public function getFormattedCurrentWeight(): string
    {
        return SysUtils::formatDbToNumber($this->getCurrentWeight(), 1) . 'kg';
    }

    public function getAge(): int
    {
        if (!$this->birthdate) return 0;

        $today = new \DateTime();
        $age = $today->diff(new \DateTime($this->birthdate))->y;
        return $age;
    }

    public function isMale(): bool
    {
        return $this->gender === Client::GENDER_MALE;
    }

    public function isFemale(): bool
    {
        return $this->gender === Client::GENDER_FEMALE;
    }

    public function getName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getGenderStr(): string
    {
        return self::fGetGenders()[$this->gender] ?? '';
    }

    public function getFirstAvaliation(): ?Avaliation
    {
        return $this->avaliations()
            ->orderBy('date', 'ASC')
            ->first();
    }

    public function getLastAvaliation(): ?Avaliation
    {
        return $this->avaliations()
            ->orderBy('date', 'DESC')
            ->first();
    }

    /**
     * Get the two most recent evaluations for the client.
     * @return array[?Avaliation]
     */
    public function getTwoLastAvaliations(): array
    {
        $avaliations = $this->avaliations()
            ->orderBy('date', 'DESC')
            ->take(2)
            ->get();

        return [
            $avaliations->get(0) ?: null,
            $avaliations->get(1) ?: null,
        ];
    }

    public function getFormattedHeight(): string
    {
        return SysUtils::formatDbToNumber($this->height_cm, 0) . 'cm';
    }

    public function getEvolutionRankingMuscleGainAttribute(): ?float
    {
        if ($this->avaliations->count() < 2) {
            return null;
        }

        $first = $this->getFirstAvaliation();
        $last = $this->getLastAvaliation();
        if (!$first || !$last) {
            return null;
        }

        if ($first->getMuscleMassPerc() === null || $last->getMuscleMassPerc() === null) {
            return null;
        }

        return round($last->getMuscleMassPerc() - $first->getMuscleMassPerc(), 1);
    }

    public function getEvolutionRankingFatLossAttribute(): ?float
    {
        if ($this->avaliations->count() < 2) {
            return null;
        }

        $first = $this->getFirstAvaliation();
        $last = $this->getLastAvaliation();
        if (!$first || !$last) {
            return null;
        }

        if ($first->getBodyFatPerc() === null || $last->getBodyFatPerc() === null) {
            return null;
        }

        return round($last->getBodyFatPerc() - $first->getBodyFatPerc(), 1);
    }

    public function getEvolutionRankingScoreAttribute(): ?float
    {
        if ($this->avaliations->count() < 2) {
            return null;
        }

        $muscleGain = $this->getEvolutionRankingMuscleGainAttribute();
        $fatLoss = $this->getEvolutionRankingFatLossAttribute();

        if ($muscleGain === null || $fatLoss === null) {
            return null;
        }

        return round($muscleGain + $fatLoss, 1);
    }
    // ===============

    // static functions
    /**
     * The "booted" method of the model.
     * 'retrieved', 'creating', 'created', 'updating', 'updated', 'saving', 'saved', 'restoring', 'restored', 'replicating', 'deleting', 'deleted', 'forceDeleted', 'trashed'
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $ret = self::fValidateClientLimit($model);
            if ($ret->isError()) {
                throw new \Exception($ret->getMessage());
            }
        });
    }

    public static function fGetGenders(): array
    {
        return [
            self::GENDER_MALE => __('messages.models.Client.gender.male'),
            self::GENDER_FEMALE => __('messages.models.Client.gender.female'),
        ];
    }

    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        if ($model->id > 0 && $model->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        $ret = self::fValidateClientLimit($model);
        if ($ret->isError()) {
            return $ret;
        }

        return null;
    }

    public static function fGetNbrNewClientsThisMonth(?int $userId = null): int
    {
        if (null === $userId) {
            $userId = SysUtils::getLoggedInUser()->id ?? 0;
        }

        // get first day of current month
        $firstDayOfMonth = Carbon::now()->startOfMonth();

        // get last day of current month
        $lastDayOfMonth = Carbon::now()->endOfMonth();

        $query = self::query()
            ->whereBetween('created_at', [$firstDayOfMonth, $lastDayOfMonth])
            ->where('user_id', $userId);

        return $query->count();
    }

    public static function fGetClientsWithoutAvaliation30Days(?int $userId = null): \Illuminate\Database\Eloquent\Builder
    {
        if (null === $userId) {
            $userId = SysUtils::getLoggedInUser()->id ?? 0;
        }

        $date30DaysAgo = Carbon::now()->subDays(30);

        return self::query()
            ->where('user_id', $userId)
            ->whereDoesntHave('avaliations', function ($query) use ($date30DaysAgo) {
                $query->where('date', '>=', $date30DaysAgo);
            });
    }

    public static function fGetNbrClientsWithoutAvaliation30Days(?int $userId = null): int
    {
        return self::fGetClientsWithoutAvaliation30Days($userId)->count();
    }

    public static function fGetClientsWithGoalsDueThisWeek(?int $userId = null): \Illuminate\Database\Eloquent\Builder
    {
        if (null === $userId) {
            $userId = SysUtils::getLoggedInUser()->id ?? 0;
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        return self::query()
            ->where('user_id', $userId)
            ->whereHas('goals', function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('deadline', [$startOfWeek, $endOfWeek]);
            });
    }

    public static function fGetNbrClientsWithGoalsDueThisWeek(?int $userId = null): int
    {
        return self::fGetClientsWithGoalsDueThisWeek($userId)->count();
    }

    public static function fValidateClientLimit(Client $Client, ?User $User = null): ApiResponse
    {
        $clientLimit = new \App\Helpers\Feature\ClientLimit($User);
        if (!$Client->exists && !$clientLimit->validate()) {
            return new ApiResponse(true, $clientLimit->getValidateMsg());
        }

        return new ApiResponse(false, '');
    }
    // ================
}
