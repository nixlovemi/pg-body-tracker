<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Client;

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
        return $this->hasOne(
            Client::class, 'id',
            'client_id'
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
        $validation->addIdField(self::class, __('messages.models.Goal.name'), 'id', 'ID');
        $validation->addIdField(Client::class, __('messages.models.Client.name'), 'id', 'ID');
        $validation->addField('objective', ['nullable', 'filled', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetObjectivies())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.Goal.fields.objective')])
                );
            }
        }], __('messages.models.Goal.fields.objective'));
        $validation->addField('target_weight_kg', ['required', 'numeric', 'min:30', 'max:400'], __('messages.models.Goal.fields.target_weight'));
        $validation->addField('deadline', ['required', 'date', 'date_format:Y-m-d'], __('messages.models.Goal.fields.deadline'));

        return $validation->validate();
    }
    // ===============

    // static functions
    public static function fGetObjectivies(): array
    {
        return [
            self::OBJECTIVE_WEIGHT_LOSS => __('messages.models.Goal.objective.weight'),
            self::OBJECTIVE_MUSCLE_GAIN => __('messages.models.Goal.objective.muscle'),
            self::OBJECTIVE_HEALTH => __('messages.models.Goal.objective.health'),
        ];
    }
    // ================
}
