<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Models\User;
use App\Models\UserPlanLogs;
use App\Helpers\Feature\FeatureAbstract;

class UserPlans extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PENDING = 'pending';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'plan_type',
        'start_date',
        'end_date',
        'payment_data',
        'status',
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
    protected $casts = [];

    protected $attributes = [];

    protected $appends = [
        'codedId',
    ];

    // relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(
            UserPlanLogs::class, 'user_plan_id',
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
        $validation->addIdField(self::class, __('messages.models.UserPlans.name'), 'id', 'ID');
        $validation->addIdField(User::class, __('messages.models.UserPlans.fields.user_id'), 'id', 'ID');
        $validation->addField('plan_type', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === in_array($value, FeatureAbstract::fGetPlanTypes())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.UserPlans.fields.plan_type')])
                );
            }
        }], __('messages.models.UserPlans.fields.plan_type'));
        $validation->addField('start_date', ['required', 'date', 'date_format:Y-m-d',  function ($attribute, $value, $fail) {
            // $value should be greater than or equal to today
            if (strtotime($value) < strtotime(now()->format('Y-m-d'))) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.UserPlans.fields.start_date')])
                );
            }
        }], __('messages.models.UserPlans.fields.start_date'));
        $validation->addField('end_date', ['required', 'date', 'date_format:Y-m-d', 'gte:start_date'], __('messages.models.UserPlans.fields.end_date'));
        $validation->addField('payment_data', ['required', 'string', 'min:2'], __('messages.models.UserPlans.fields.payment_data'));
        $validation->addField('status', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetStatuses())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.UserPlans.fields.status')])
                );
            }
        }], __('messages.models.UserPlans.fields.status'));

        return $validation->validate();
    }

    public function addLog(array $form): ApiResponse
    {
        $form['user_plan_id'] = $this->id;
        return UserPlanLogs::fSave($form);
    }
    // ===============

    // static functions
    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        if ($model->id > 0 && $model->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        return null;
    }

    public static function fGetStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => __('messages.models.UserPlans.status.active'),
            self::STATUS_CANCELED => __('messages.models.UserPlans.status.canceled'),
            self::STATUS_PENDING => __('messages.models.UserPlans.status.pending'),
        ];
    }
}
