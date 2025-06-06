<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;

class UserPlanLogs extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_plan_id',
        'payment_class',
        'payment_id',
        'data',
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
    public function userPlan()
    {
        return $this->belongsTo(UserPlans::class, 'user_plan_id', 'id');
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, __('messages.models.UserPlanLogs.name'), 'id', 'ID');
        $validation->addIdField(UserPlans::class, __('messages.models.UserPlanLogs.user_plan_id'), 'id', 'ID');
        $validation->addField('payment_class', ['required', 'string', function ($attribute, $value, $fail) {
            // Check if the payment class exists
            if (!class_exists($value)) {
                $fail(__('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.UserPlanLogs.fields.payment_class')]));
            }
        }], __('messages.models.UserPlanLogs.fields.payment_class'));
        $validation->addField('payment_id', ['required', 'string', 'min:1', 'max:100'], __('messages.models.UserPlanLogs.fields.payment_id'));
        $validation->addField('data', ['required', 'string', 'min:1'], __('messages.models.UserPlanLogs.fields.data'));

        return $validation->validate();
    }
    // ===============

    // static functions
    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        if ($model->id > 0 && $model->userPlan->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        return null;
    }
}
