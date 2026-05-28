<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AvaliationCheckinField extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const RESPONSE_TYPE_TEXT = 'text';
    public const RESPONSE_TYPE_TEXTAREA = 'textarea';
    public const RESPONSE_TYPE_NUMBER = 'number';
    public const RESPONSE_TYPE_SELECT = 'select';
    public const RESPONSE_TYPE_RADIO = 'radio';
    public const RESPONSE_TYPE_BOOLEAN = 'boolean';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avaliation_id',
        'field_class',
        'field_type',
        'field_key',
        'response',
        'response_type',
        'field_meta',
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
        'field_meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [];

    protected $appends = [
        'codedId',
    ];

    // relations
    public function avaliation()
    {
        return $this->belongsTo(Avaliation::class, 'avaliation_id', 'id');
    }
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, __('messages.models.AvaliationCheckinField.name'), 'id', 'ID');
        $validation->addIdField(Avaliation::class, __('messages.models.Avaliation.name'), 'id', 'ID');
        $validation->addField('field_class', ['required', 'string', 'min:3', 'max:255', function ($attribute, $value, $fail) {
            if (!class_exists($value)) {
                $fail(__('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.AvaliationCheckinField.fields.field_class')]));
            }
        }], __('messages.models.AvaliationCheckinField.fields.field_class'));
        $validation->addField('field_type', ['required', 'string', 'min:2', 'max:100'], __('messages.models.AvaliationCheckinField.fields.field_type'));
        $validation->addField('field_key', ['required', 'string', 'min:2', 'max:120'], __('messages.models.AvaliationCheckinField.fields.field_key'));
        $validation->addField('response', ['nullable', 'string'], __('messages.models.AvaliationCheckinField.fields.response'));
        $validation->addField('response_type', ['required', 'string', function ($attribute, $value, $fail) {
            if (!in_array($value, self::fGetResponseTypes(), true)) {
                $fail(__('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.AvaliationCheckinField.fields.response_type')]));
            }
        }], __('messages.models.AvaliationCheckinField.fields.response_type'));
        $validation->addField('field_meta', ['nullable', 'array'], __('messages.models.AvaliationCheckinField.fields.field_meta'));

        return $validation->validate();
    }

    public static function fGetResponseTypes(): array
    {
        return [
            self::RESPONSE_TYPE_TEXT,
            self::RESPONSE_TYPE_TEXTAREA,
            self::RESPONSE_TYPE_NUMBER,
            self::RESPONSE_TYPE_SELECT,
            self::RESPONSE_TYPE_RADIO,
            self::RESPONSE_TYPE_BOOLEAN,
        ];
    }
    // ===============

    // static functions
    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        if (!$user) {
            return false;
        }

        $avaliationId = $model->avaliation_id ?: $model->avaliation?->id;
        if (!$avaliationId) {
            return false;
        }

        $Avaliation = Avaliation::find($avaliationId);
        if (!$Avaliation || $Avaliation->client->user_id !== $user->id) {
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

        return null;
    }
    // ================
}
