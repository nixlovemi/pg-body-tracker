<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Helpers\SysUtils;
use Illuminate\Http\UploadedFile;
use App\Helpers\Constants;

class UserInfo extends Model
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;
    use \App\Traits\HasPhotoField;

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
        $defaultImg = public_path(str_replace('/', DIRECTORY_SEPARATOR, Constants::USER_LOGO_DEFAULT_IMAGE_PATH));
        return $this->getPhotoBase64('logo_url', $defaultImg);
    }
    // ===============

    // static functions
    public static function fSave(array $form, ?string $codedId = null): ApiResponse
    {
        // get model for insert or update
        if (!empty($codedId)) {
            $UserInfo = self::getModelByCodedId($codedId);
            if ($UserInfo === null) {
                return new ApiResponse(true, __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.UserInfo.name'),
                ]));
            }
        } else {
            $UserInfo = new self();
        }
        $isEdit = ($UserInfo->id > 0);

        // check if user can save
        if (!self::fHasAccess($UserInfo)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => __('messages.models.UserInfo.name'),
            ]));
        }

        // fill model
        $UserInfo->fill($form);

        // validate model
        $validation = $UserInfo->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $UserInfo->save();
            $UserInfo->refresh();
        } catch (\Exception $e) {
            \App\Helpers\LocalLogger::log('UserInfo save error', ['exception' => $e->getMessage()]);
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => __('messages.models.UserInfo.name'),
            ]));
        }

        // all good, return success
        $msg = $isEdit ? __('messages.saveModelSuccessEditing', ['modelName' => __('messages.models.UserInfo.name')]) : __('messages.saveModelSuccessAdding', ['modelName' => __('messages.models.UserInfo.name')]);
        return new ApiResponse(false, $msg, [
            'UserInfo' => $UserInfo,
            'isEdit' => $isEdit,
        ]);
    }

    public static function fHasAccess(self $UserInfo): bool
    {
        // adding user is ok
        if (empty($UserInfo->id)) {
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

        // check if user is trying to edit a goal from a client that is not his
        if ($UserInfo->id > 0 && $UserInfo->user_id !== $lggdUser->id) {
            return false;
        }

        return true;
    }
    // ================
}
