<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\UploadedFile;
use App\Helpers\Constants;
use App\Helpers\Feature\UserReportLogo;

class UserInfo extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;
    use \App\Traits\HasPhotoField;

    public const EVALUATION_MODE_PERSONAL = 'personal';
    public const EVALUATION_MODE_PROFESSIONAL = 'professional';

    public const BASE_PHOTOS_FOLDER = '/user-info/photos/';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'license_text',
        'evaluation_mode',
        'whatsapp_phone',
        'link_telegram',
        'link_facebook',
        'link_instagram',
        'link_twitter',
        'link_youtube',
        'link_website',
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
    // =========

    // class functions
    /**
     * https://laravel.com/docs/8.x/validation#available-validation-rules
     */
    public function validateModel(): ApiResponse
    {
        $validation = new ModelValidation($this->toArray());
        $validation->addIdField(self::class, __('messages.models.Goal.name'), 'id', 'ID');
        $validation->addIdField(User::class, __('messages.models.User.name'), 'id', 'ID');
        $validation->addField('title', ['nullable', 'string', 'min:2', 'max:60'], __('messages.models.UserInfo.fields.title'));
        $validation->addField('license_text', ['nullable', 'string', 'min:2', 'max:60'], __('messages.models.UserInfo.fields.license_text'));
        $validation->addField('evaluation_mode', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetEvaluationModes())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.UserInfo.fields.evaluation_mode')])
                );
            }
        }], __('messages.models.UserInfo.fields.evaluation_mode'));
        $validation->addField('whatsapp_phone', ['nullable', 'string', 'min:2', 'max:35'], __('messages.models.UserInfo.fields.whatsapp_phone'));
        $validation->addField('link_telegram', ['nullable', 'string', 'url', 'min:2', 'max:100'], __('messages.models.UserInfo.fields.link_telegram'));
        $validation->addField('link_facebook', ['nullable', 'string', 'url', 'min:2', 'max:100'], __('messages.models.UserInfo.fields.link_facebook'));
        $validation->addField('link_instagram', ['nullable', 'string', 'url', 'min:2', 'max:100'], __('messages.models.UserInfo.fields.link_instagram'));
        $validation->addField('link_twitter', ['nullable', 'string', 'url', 'min:2', 'max:100'], __('messages.models.UserInfo.fields.link_twitter'));
        $validation->addField('link_youtube', ['nullable', 'string', 'url', 'min:2', 'max:100'], __('messages.models.UserInfo.fields.link_youtube'));
        $validation->addField('link_website', ['nullable', 'string', 'url', 'min:2', 'max:100'], __('messages.models.UserInfo.fields.link_website'));

        return $validation->validate();
    }

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    public function setCreatedAtAttribute($value)
    {
        // to Disable created_at
    }

    public function setLogoUrl(?UploadedFile $file): void
    {
        $URLogoFeature = new UserReportLogo();
        if (!$URLogoFeature->validate()) {
            return;
        }

        $this->setPhotoUrl(
            'logo_url',
            $file,
            self::BASE_PHOTOS_FOLDER,
            400,
            'user_info_' . $this->id . '_' . time()
        );
    }

    public function removeLogoUrl(): void
    {
        $this->removePhotoUrl('logo_url', self::BASE_PHOTOS_FOLDER);
    }

    public function getLogoBase64(): ?string
    {
        $URLogoFeature = new UserReportLogo();
        if (!$URLogoFeature->validate()) {
            return Constants::USER_LOGO_DEFAULT_IMAGE_PATH;
        }

        $defaultImg = public_path(str_replace('/', DIRECTORY_SEPARATOR, Constants::USER_LOGO_DEFAULT_IMAGE_PATH));
        return $this->getPhotoBase64('logo_url', $defaultImg);
    }
    // ===============

    // static functions
    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        // Allow access during migrations/seed
        if (\App\Helpers\SysUtils::isRunningMigration()) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($model->id > 0 && $model->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        return null;
    }

    public static function fGetEvaluationModes(): array
    {
        return [
            self::EVALUATION_MODE_PERSONAL => __('messages.models.UserInfo.evaluationModes.personal'),
            self::EVALUATION_MODE_PROFESSIONAL => __('messages.models.UserInfo.evaluationModes.professional'),
        ];
    }
    // ================
}
