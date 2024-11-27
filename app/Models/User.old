<?php

namespace App\Models;

use App\Helpers\ApiResponse;
use App\Helpers\Constants;
use App\Helpers\ModelValidation;
use App\Helpers\SysUtils;
use App\Helpers\ValidatePassword;
use App\Mail\ResetPassword;
use App\Models\Client;
use App\Models\Job;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Image;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use \App\Traits\BaseModelTrait;

    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_MANAGER = 'MANAGER';
    public const ROLE_CREATIVE = 'CREATIVE';
    public const ROLE_EDITOR = 'EDITOR';
    public const ROLE_CUSTOMER = 'CUSTOMER';
    private const PICTURE_FOLDER = '/img/users';

    public const USER_ROLES = [
        self::ROLE_ADMIN => 'Administrador',
        self::ROLE_MANAGER => 'Gerência',
        self::ROLE_CREATIVE => 'Criação',
        self::ROLE_EDITOR => 'Editor',
        self::ROLE_CUSTOMER => 'Atendimento'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
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
        'roleDescription'
    ];

    // relations
    public function clients()
    {
        return $this->hasMany(
            Client::class, 'create_user_id',
            'id'
        );
    }

    public function jobs()
    {
        return $this->hasMany(
            Job::class, 'create_user_id',
            'id'
        );
    }

    public function jobsResponsible()
    {
        return $this->hasMany(
            Job::class, 'user_responsible_id',
            'id'
        );
    }

    public function quotes()
    {
        return $this->hasMany(
            Quote::class, 'create_user_id',
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
        $validation->addIdField(self::class, 'Usuário', 'id', 'ID');
        $validation->addField('name', ['required', 'string', 'min:3', 'max:255'], 'Nome');
        $validation->addEmailField('email', 'E-mail', ['required', 'string', 'min:3', 'max:255']);
        $validation->addField('password', ['required', 'string', 'min:8', 'max:255', function ($attribute, $value, $fail) {
            $ValidadePwd = new ValidatePassword($value);
            $retValidate = $ValidadePwd->validate();
            if (true === $retValidate->isError()) {
                $fail($retValidate->getMessage());
            }
        }], 'Senha');
        $validation->addField('role', ['required', 'string', function ($attribute, $value, $fail) {
            if (false === array_key_exists($value, self::USER_ROLES)) {
                $fail("O campo \"Cargo\" contém um valor inválido!");
            }
        }], 'Cargo');

        return $validation->validate();
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function changePassword(
        string $newPassword,
        string $newPasswordRetype,
        ?string $currentPassword = null
    ): ApiResponse {
        if (null !== $currentPassword) {
            if (false === $this->checkPassword($currentPassword)) {
                return new ApiResponse(true, 'Senha atual não confere!');
            }
        }

        if ($newPassword !== $newPasswordRetype) {
            return new ApiResponse(true, 'Senha não conferem com a redigitada!');
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

        return new ApiResponse(false, 'Senha alterada com sucesso!', [
            'User' => $this
        ]);
    }

    public function getPictureUrl(): string
    {
        if (empty($this->picture_url)) {
            return Constants::USER_DEFAULT_IMAGE_PATH;
        }

        return $this->picture_url;
    }

    public function generateResetPassToken(): string
    {
        $this->password_reset_token = SysUtils::encodeStr($this->id . date('YmdHisu'));
        $this->update();

        return $this->password_reset_token;
    }

    public function isAdmin(): bool
    {
        return $this->role === User::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === User::ROLE_MANAGER;
    }

    public function isCreative(): bool
    {
        return $this->role === User::ROLE_CREATIVE;
    }

    public function isCustomer(): bool
    {
        return $this->role === User::ROLE_CUSTOMER;
    }

    public function isEditor(): bool
    {
        return $this->role === User::ROLE_EDITOR;
    }

    public function getRoleDescriptionAttribute(): ?string
    {
        return self::USER_ROLES[$this->role] ?? '';
    }

    public function setNewProfilePicture(UploadedFile $file): void
    {
        $destinationPath = public_path(self::PICTURE_FOLDER);
        $newFileName = 'pic-' . $this->id . '.' . $file->extension();
        $saveDestinationPath = $destinationPath . '/' . $newFileName;

        $img = Image::make($file->path());
        $retSave = $img->fit(250)->save($saveDestinationPath);
        if ($retSave) {
            $this->picture_url = self::PICTURE_FOLDER . '/' . $newFileName;
            $this->update();
        }
    }

    public function canSeeJobQuoteTab(): bool
    {
        return $this->isAdmin() || $this->isManager() || $this->isCustomer();
    }
    // ===============

    // static functions
    public static function fPasswordHash(string $password): string
    {
        // return bcrypt($password);
        return Hash::make($password);
    }

    public static function fLogin(string $email, string $password): ApiResponse
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, 'Informe um e-mail válido!');
        }

        if (empty($password)) {
            return new ApiResponse(true, 'Preencha a senha!');
        }

        $User = User::where('email', $email)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, 'Usuário não encontrado ou inativo!');
        }

        if (false === $User->checkPassword($password)) {
            return new ApiResponse(true, 'Usuário ou senha inválido(s)!');
        }

        // all good, register everything
        if (false === SysUtils::loginUser($User)) {
            return new ApiResponse(true, 'Erro ao registrar usuário! Tente novamente.');
        }

        // clean reset token
        $User->password_reset_token = null;
        $User->update();
        $User->refresh();

        return new ApiResponse(false, 'Login efetuado com sucesso!', [
            'User' => $User
        ]);
    }

    public static function fRecoverPwd(string $email): ApiResponse
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, 'Informe um e-mail válido!');
        }

        $User = User::where('email', $email)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, 'Usuário não encontrado ou inativo!');
        }

        // generate and save token
        $token = $User->generateResetPassToken();
        $User->refresh();

        // send mail
        Mail::to($User->email)
            ->send(
                new ResetPassword([
                    'EMAIL_TITLE' => 'Você acaba de pedir para alterar sua senha',
                    'TITLE' => 'Você acaba de pedir para alterar sua senha',
                    'HEADER_IMG_FULL_URL' => (env('APP_ENV') === 'local') ? 'https://i.imgur.com/SzkGU2o.png': asset('/img/resetPassword.png'),
                    'ARR_TEXT_LINES' => [
                        'Esqueceu a sua senha?',
                        'Nós vimos que você solicitou alteração de senha da sua conta.',
                        'Caso não tenha sido você, ignore esse e-mail. Mas fique tranquilo, a sua conta está segura com a gente!'
                    ],
                    'ACTION_BUTTON_URL' => route('site.changeNewPwd', ['idKey' => $token]),
                    'ACTION_BUTTON_TEXT' => 'Escolha sua nova senha',
                ])
            );

        return new ApiResponse(false, 'Solicitação de alteração de senha concluído! Acesse seu e-mail para ver as instruções para recuperar a senha.', [
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
            return new ApiResponse(true, 'Usuário não encontrado ou inativo!');
        }

        return $User->changePassword($newPassword, $newPasswordRetype);
    }
    // ================
}
