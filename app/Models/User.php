<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\ApiResponse;
use App\Helpers\ModelValidation;
use App\Helpers\ValidatePassword;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Constants;
use App\Helpers\SysUtils;
use App\Helpers\Permissions;
use App\Models\UserInfo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;
    use \App\Traits\HasPhotoField;

    public const BASE_PHOTOS_FOLDER = '/users/photos/';

    public const ROLE_ROOT = 'ROOT';
    public const ROLE_MANAGER = 'MANAGER';
    public const ROLE_CLIENT = 'CLIENT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'role',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'password_reset_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'active' => true,
        'picture_url' => Constants::USER_DEFAULT_IMAGE_PATH,
    ];

    protected $appends = [
        'codedId',
    ];

    // relations
    public function clients()
    {
        return $this->hasMany(
            Client::class, 'user_id',
            'id'
        );
    }

    public function info()
    {
        return $this->hasOne(
            UserInfo::class, 'user_id',
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
        $validation->addIdField(self::class, __('messages.models.User.name'), 'id', 'ID');
        $validation->addField('first_name', ['required', 'string', 'min:2', 'max:60'], __('messages.models.User.fields.name'));
        $validation->addField('last_name', ['required', 'string', 'min:2', 'max:80'], __('messages.models.User.fields.lastName'));
        $validation->addEmailField('email', 'E-mail', ['required', 'string', 'min:3', 'max:255']);
        $validation->addField('picture_url', ['nullable', 'string', 'min:5', 'max:255'], __('messages.models.User.fields.pictureUrl'));
        $validation->addField('password', ['filled', 'string', 'min:8', 'max:255', function ($attribute, $value, $fail) {
            $ValidadePwd = new ValidatePassword($value);
            $retValidate = $ValidadePwd->validate();
            if (true === $retValidate->isError()) {
                $fail($retValidate->getMessage());
            }
        }], __('messages.models.User.fields.password'));
        $validation->addField('password_reset_token', ['nullable', 'string', 'min:20', 'max:255'], __('messages.models.User.fields.passwordToken'));
        $validation->addField('role', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetRoles(false))) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.User.fields.role')])
                );
            }
        }], __('messages.models.User.fields.role'));
        $validation->addField('active', ['required', 'boolean'], __('messages.models.User.fields.active'));

        return $validation->validate();
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->created_at->format(__('messages.dateFormat'));
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function getPictureBase64(): ?string
    {
        $defaultImg = public_path(str_replace('/', DIRECTORY_SEPARATOR, Constants::USER_DEFAULT_IMAGE_PATH));
        return $this->getPhotoBase64('picture_url', $defaultImg);
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isRoot(): bool
    {
        return $this->role === User::ROLE_ROOT;
    }

    public function isManager(): bool
    {
        return $this->role === User::ROLE_MANAGER;
    }

    public function isClient(): bool
    {
        return $this->role === User::ROLE_CLIENT;
    }

    public function hasPermission(string $permission): bool
    {
        return Permissions::checkPermission($permission, $this);
    }

    public function setPictureUrl(?UploadedFile $file): void
    {
        $this->setPhotoUrl(
            'picture_url',
            $file,
            self::BASE_PHOTOS_FOLDER,
            400,
            'user_' . $this->id . '_' . time()
        );
    }

    public function removePictureUrl(): void
    {
        $this->removePhotoUrl('picture_url', self::BASE_PHOTOS_FOLDER);
    }

    public function changePassword(
        string $newPassword,
        string $newPasswordRetype,
        ?string $currentPassword = null
    ): ApiResponse {
        if (null !== $currentPassword) {
            if (false === $this->checkPassword($currentPassword)) {
                return new ApiResponse(true, __('messages.components.ValidatePassword.currentPasswordWrong'));
            }
        }

        if ($newPassword !== $newPasswordRetype) {
            return new ApiResponse(true, __('messages.components.ValidatePassword.retypePasswordWrong'));
        }

        $ValidadePwd = new ValidatePassword($newPassword);
        $retValidate = $ValidadePwd->validate();
        if (true === $retValidate->isError()) {
            return $retValidate;
        }

        // all good, change it
        $this->password_reset_token = null;
        $this->password = User::fPasswordHash($newPassword);
        $this->update();
        $this->refresh();

        return new ApiResponse(false, __('messages.components.ValidatePassword.passwordChangedSuccess'), [
            'User' => $this
        ]);
    }

    public function generateResetPassToken(): string
    {
        $this->password_reset_token = SysUtils::encodeStr($this->id . date('YmdHisu'));
        $this->update();

        return $this->password_reset_token;
    }
    // ===============

    // static functions
    public static function fSave(array $form, ?string $codedId = null): ApiResponse
    {
        // get model for insert or update
        if (!empty($codedId)) {
            $User = self::getModelByCodedId($codedId);
            if ($User === null) {
                return new ApiResponse(true, __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.User.name'),
                ]));
            }
        } else {
            $User = new self();
        }
        $isEdit = ($User->id > 0);

        // check if user can save
        if (!self::fHasAccess($User)) {
            return new ApiResponse(true, __('messages.saveModelErrorSavingOther', [
                'modelName' => __('messages.models.User.name'),
            ]));
        }

        // fill model
        $User->fill($form);

        // validate model
        $validation = $User->validateModel();
        if ($validation->isError()) {
            return $validation;
        }

        // save model
        try {
            $User->save();
            $User->refresh();
        } catch (\Exception $e) {
            \App\Helpers\LocalLogger::log('User save error', ['exception' => $e->getMessage()]);
            return new ApiResponse(true, __('messages.saveModelErrorSaving', [
                'modelName' => __('messages.models.User.name'),
            ]));
        }

        // all good, return success
        $msg = $isEdit ? __('messages.saveModelSuccessEditing', ['modelName' => __('messages.models.User.name')]) : __('messages.saveModelSuccessAdding', ['modelName' => __('messages.models.User.name')]);
        return new ApiResponse(false, $msg, [
            'User' => $User,
            'isEdit' => $isEdit,
        ]);
    }

    public static function fHasAccess(self $User): bool
    {
        // adding user is ok
        if (empty($User->id)) {
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

        // can only save itself
        if ($User->id > 0 && $User->id !== $lggdUser->id) {
            return false;
        }

        return true;
    }

    public static function fPasswordHash(string $password): string
    {
        // return bcrypt($password);
        return Hash::make($password);
    }

    public static function fGetRoles(bool $hideRoot = true): array
    {
        $roles = [];

        if (false === $hideRoot) {
            $roles[self::ROLE_ROOT] = __('messages.models.User.roles.root');
        }

        $roles[self::ROLE_MANAGER] = __('messages.models.User.roles.manager');
        $roles[self::ROLE_CLIENT] = __('messages.models.User.roles.client');

        return $roles;
    }

    public static function fLogin(string $email, string $password): ApiResponse
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, __('messages.models.User.fLogin.invalidEmail'));
        }

        if (empty($password)) {
            return new ApiResponse(true, __('messages.models.User.fLogin.emptyPassword'));
        }

        $User = User::where('email', $email)
            ->where('active', true)
            ->first();
        if (
            !$User ||
            false === $User->checkPassword($password) ||
            (!$User->isManager() && !$User->isRoot())
        ) {
            return new ApiResponse(true, __('messages.models.User.fLogin.invalidCredentials'));
        }

        // all good, register everything
        if (false === SysUtils::loginUser($User)) {
            return new ApiResponse(true, __('messages.models.User.fLogin.loginUserError'));
        }

        // clean reset token
        $User->password_reset_token = null;
        $User->update();
        $User->refresh();

        return new ApiResponse(false, __('messages.models.User.fLogin.loginSuccess'), [
            'User' => $User
        ]);
    }

    public static function fRecoverPwd(string $email): ApiResponse
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, __('messages.pages.login.forgot.errorMailNotValid'));
        }

        $User = User::where('email', $email)
            ->where('active', true)
            ->first();
        if (!$User) {
            // returning like this to avoid user enumeration
            // if user exists or not
            return new ApiResponse(false, __('messages.pages.login.forgot.successMessage'), [
                'token' => null,
                'User' => null,
            ]);
        }

        // generate and save token
        $token = $User->generateResetPassToken();
        $User->refresh();

        // reset link
        $resetLink = URL::temporarySignedRoute(
            'app.resetPwd',
            now()->addHours(24),
            ['idKey' => $token]
        );
        $shortResetLink = \App\Models\UrlShort::make($resetLink);

        // send mail
        $forgotImg = 'images/mail-forgot-password.jpg';

        Mail::to($User->email)
            ->send(
                new ResetPassword([
                    'EMAIL_TITLE' => __('messages.pages.login.forgot.mailTitle'),
                    'TITLE' => __('messages.pages.login.forgot.mailTitle'),
                    'HEADER_IMG_FULL_BASE64' => SysUtils::getImageBase64($forgotImg),
                    'ARR_TEXT_LINES' => [
                        __('messages.pages.login.forgot.mailLine1'),
                        __('messages.pages.login.forgot.mailLine2'),
                        __('messages.pages.login.forgot.mailLine3'),
                    ],
                    'ACTION_BUTTON_URL' => $shortResetLink,
                    'ACTION_BUTTON_TEXT' => __('messages.pages.login.forgot.mailActionLink'),
                ])
            );

        return new ApiResponse(false, __('messages.pages.login.forgot.successMessage'), [
            'token' => $token,
            'User' => $User,
        ]);
    }

    public static function fResetPasswordByToken(
        string $token,
        string $newPassword,
        string $newPasswordRetype
    ): ApiResponse {
        $User = User::where('password_reset_token', $token)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, __('messages.pages.login.resetPwd.invalidKey'));
        }

        return $User->changePassword($newPassword, $newPasswordRetype);
    }
    // ================
}
