<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\User;

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

    public function getLastAvaliation(): ?Avaliation
    {
        return $this->avaliations()
            ->orderBy('date', 'DESC')
            ->first();
    }
    // ===============

    // static functions
    public static function fGetGenders(): array
    {
        return [
            self::GENDER_MALE => __('messages.models.Client.gender.male'),
            self::GENDER_FEMALE => __('messages.models.Client.gender.female'),
        ];
    }

    public static function fSave(array $form, ?string $codedId = null): ApiResponse
    {
        // get model for insert or update
        if (!empty($codedId)) {
            $Client = self::getModelByCodedId($codedId);
            if ($Client === null) {
                return new ApiResponse(true, __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.Client.name')
                ]));
            }
        } else {
            $Client = new self();
        }
        $isEdit = ($Client->id > 0);

        // check if user can save
        if (!self::fHasAccess($Client)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => __('messages.models.Client.name')
            ]));
        }

        // fill model
        $Client->fill($form);

        // validate model
        $validation = $Client->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $Client->save();
            $Client->refresh();
        } catch (\Exception $e) {
            \App\Helpers\LocalLogger::log('Client save error', ['exception' => $e->getMessage()]);
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => __('messages.models.Client.name')
            ]));
        }

        // all good, return success
        $msg = $isEdit ? __('messages.saveModelSuccessEditing', ['modelName' => __('messages.models.Client.name')]) : __('messages.saveModelSuccessAdding', ['modelName' => __('messages.models.Client.name')]);
        return new ApiResponse(false, $msg, [
            'Client' => $Client,
            'isEdit' => $isEdit,
        ]);
    }

    public static function fHasAccess(self $Client): bool
    {
        // adding user is ok
        if (empty($Client->id)) {
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

        // check if user is trying to edit a client that is not his
        if ($Client->id > 0 && $Client->user_id !== $lggdUser->id) {
            return false;
        }

        return true;
    }
    // ================
}
