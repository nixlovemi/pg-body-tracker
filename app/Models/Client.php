<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
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
        'weight',
        'height',
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
        return $this->hasOne(
            User::class, 'id',
            'user_id'
        );
    }

    public function goals()
    {
        return $this->hasMany(
            Goal::class, 'client_id',
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
        $validation->addEmailField('email', 'E-mail', ['nullable', 'filled', 'string', 'min:3', 'max:255']);
        $validation->addPhoneField('phone', __('messages.models.Client.fields.phone'), ['nullable', 'filled', 'max:35']);
        $validation->addField('gender', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetGenders())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.Client.fields.gender')])
                );
            }
        }], __('messages.models.Client.fields.gender'));
        $validation->addField('birthdate', ['required', 'date', 'date_format:Y-m-d'], __('messages.models.Client.fields.birthdate'));
        $validation->addField('weight', ['required', 'numeric', 'min:30', 'max:400'], __('messages.models.Client.fields.weight'));
        $validation->addField('height', ['required', 'integer', 'min:60', 'max:250'], __('messages.models.Client.fields.height'));

        return $validation->validate();
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
    // ================
}
