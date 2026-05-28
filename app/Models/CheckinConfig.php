<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CheckinConfig extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'active',
        'interval_days',
        'link_expires_hours',
        'fields_config',
        'next_checkin_date',
        'last_checkin_date',
        'last_checkin_sent_date',
        'last_checkin_sent_at',
        'unanswered_reminders_sent',
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
    * Temporal semantics:
    * - next_checkin_date: next scheduled check-in date for the regular cycle.
    * - last_checkin_date: date when the client last answered a check-in.
    * - last_checkin_sent_date: calendar date when the last check-in email was sent.
    * - last_checkin_sent_at: exact timestamp when the last check-in email was sent.
    * - unanswered_reminders_sent: reminder emails already sent for the current unanswered cycle.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'fields_config' => 'array',
        'next_checkin_date' => 'date',
        'last_checkin_date' => 'date',
        'last_checkin_sent_date' => 'date',
        'last_checkin_sent_at' => 'datetime',
        'unanswered_reminders_sent' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'active' => true,
        'interval_days' => 7,
        'link_expires_hours' => 24,
        'unanswered_reminders_sent' => 0,
    ];

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
        $validation->addIdField(self::class, __('messages.models.CheckinConfig.name'), 'id', 'ID');
        $validation->addIdField(Client::class, __('messages.models.Client.name'), 'id', 'ID');
        $validation->addField('active', ['required', 'boolean'], __('messages.models.CheckinConfig.fields.active'));
        $validation->addField('interval_days', ['required', 'integer', 'min:1', 'max:365'], __('messages.models.CheckinConfig.fields.interval_days'));
        $validation->addField('link_expires_hours', ['required', 'integer', 'min:1', 'max:168'], __('messages.models.CheckinConfig.fields.link_expires_hours'));
        $validation->addField('fields_config', ['nullable', 'array'], __('messages.models.CheckinConfig.fields.fields_config'));
        $validation->addField('next_checkin_date', ['nullable', 'date'], __('messages.models.CheckinConfig.fields.next_checkin_date'));
        $validation->addField('last_checkin_date', ['nullable', 'date'], __('messages.models.CheckinConfig.fields.last_checkin_date'));
        $validation->addField('last_checkin_sent_date', ['nullable', 'date'], __('messages.models.CheckinConfig.fields.last_checkin_sent_date'));
        $validation->addField('last_checkin_sent_at', ['nullable', 'date'], __('messages.models.CheckinConfig.fields.last_checkin_sent_at'));
        $validation->addField('unanswered_reminders_sent', ['nullable', 'integer', 'min:0', 'max:255'], __('messages.models.CheckinConfig.fields.unanswered_reminders_sent'));

        return $validation->validate();
    }

    public function getFieldsConfig(): array
    {
        return $this->fields_config ?? [];
    }
    // ===============

    // static functions
    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        if (!$user) {
            return false;
        }

        $clientId = $model->client_id ?: $model->client?->id;
        if (!$clientId) {
            // During first save via BaseModelTrait::fSave, access is checked before fill(),
            // so client_id is still empty. Allow creation flow and enforce ownership later.
            return !$model->exists;
        }

        $Client = Client::find($clientId);
        if (!$Client || $Client->user_id !== $user?->id) {
            return false;
        }

        return true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        $User = SysUtils::getLoggedInUser();
        if (!$User || !$User->hasPremiumPlan()) {
            return new ApiResponse(true, __('messages.components.Features.CheckinFollowUp.validateMessage'));
        }

        if ((int) $model->interval_days <= 0) {
            $model->interval_days = 7;
        }

        if ((int) $model->link_expires_hours <= 0) {
            $model->link_expires_hours = 24;
        }

        return null;
    }
    // ================
}
