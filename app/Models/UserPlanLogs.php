<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionUpdate;
use Illuminate\Support\Facades\Cache;

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

    public function getFormattedCreatedAt(bool $full=false): string
    {
        $format = $full ? __('messages.fullDateFormat') : __('messages.dateFormat');
        return SysUtils::timezoneDate($this->created_at, $format);
    }

    public function getColIdString(): string
    {
        // Return a short version of the payment ID
        return $this->payment_id ? substr($this->payment_id, 0, 6) . '...' : '-';
    }
    // ===============

    // static functions
    protected static function boot()
    {
        parent::boot();

        static::created(function ($UserPlanLogs) {
            // Send email notification to the user
            self::fCheckLog($UserPlanLogs);

            // syncSubscriptionStatus
            $paymentClass = $UserPlanLogs->payment_class;
            if (class_exists($paymentClass)) {
                (new $paymentClass())->syncSubscriptionStatus($UserPlanLogs->userPlan);
            }

            // Clear user cache
            Cache::forget($UserPlanLogs->userPlan->user->getPlanTypeCacheKey());
        });
    }

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

    public static function fCheckLog(UserPlanLogs $model): void
    {
        // get payment class
        $paymentClass = $model->payment_class;
        if (!class_exists($paymentClass)) {
            Log::error('Payment class does not exist: ' . $paymentClass);
            return;
        }

        // get log data
        $Class = new $paymentClass();
        $logData = json_decode($model->data ?? '{}', true);
        if (false === $Class->isPaymentLog($logData)) {
            return;
        }

        // check approval status
        if ($Class->isPaymentApproved($model->userPlan)) {
            $model->userPlan->status = UserPlans::STATUS_ACTIVE;
            $model->userPlan->save();

            Mail::to($model->userPlan->user->email)
                ->send(
                    new SubscriptionUpdate(
                        $model->userPlan,
                        'subscriptionApproved',
                    )
                );
            return;
        }

        if ($Class->isPaymentRejected($model->userPlan)) {
            $model->userPlan->status = UserPlans::STATUS_CANCELED;
            $model->userPlan->save();

            Mail::to($model->userPlan->user->email)
                ->send(
                    new SubscriptionUpdate(
                        $model->userPlan,
                        'subscriptionRejected',
                    )
                );
            return;
        }
    }
}
