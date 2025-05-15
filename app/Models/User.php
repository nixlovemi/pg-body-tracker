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

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

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
        $validation->addField('picture_url', ['nullable', 'filled', 'string', 'min:5', 'max:255'], __('messages.models.User.fields.pictureUrl'));
        $validation->addField('password', ['required', 'string', 'min:8', 'max:255', function ($attribute, $value, $fail) {
            $ValidadePwd = new ValidatePassword($value);
            $retValidate = $ValidadePwd->validate();
            if (true === $retValidate->isError()) {
                $fail($retValidate->getMessage());
            }
        }], __('messages.models.User.fields.password'));
        $validation->addField('password_reset_token', ['nullable', 'filled', 'string', 'min:20', 'max:255'], __('messages.models.User.fields.passwordToken'));
        $validation->addField('role', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::fGetRoles())) {
                $fail(
                    __('messages.helpers.modelValidation.invalidField', ['attribute' => __('messages.models.User.fields.role')])
                );
            }
        }], __('messages.models.User.fields.role'));
        $validation->addField('active', ['required', 'boolean'], __('messages.models.User.fields.active'));

        return $validation->validate();
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function getPictureUrl(): string
    {
        if (empty($this->picture_url)) {
            return Constants::USER_DEFAULT_IMAGE_PATH;
        }

        return $this->picture_url;
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
    // ===============

    // static functions
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
    // ================
}
