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
use Illuminate\Database\Eloquent\Model;
use App\Mail\ConfirmationLink;
use Laravel\Socialite\Two\User as SocialiteUser;
use App\Models\UserPlans;
use App\Helpers\Feature\FeatureAbstract;
use Illuminate\Support\Facades\Cache;
use App\Helpers\GoogleUserLogin;

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
        'password',
        'role',
        'active',
        'confirmation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        # 'password', // TODO: if hidden we cant assign value to it, try to find a way to hide it when loading model
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

    public function plans()
    {
        return $this->hasMany(
            UserPlans::class, 'user_id',
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
        $validation->addField('password', ['required', 'string', function ($attribute, $value, $fail) {
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
        $validation->addField('confirmation', ['required', 'boolean'], __('messages.models.User.fields.confirmation'));

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

    public function sendConfirmationEmail(): void
    {
        // send confirmation email
        Mail::to($this->email)
            ->send(
              new ConfirmationLink(
                    $this->getFullName(),
                    URL::temporarySignedRoute(
                        'app.confirmUser',
                        now()->addHours(1),
                        ['key' => $this->getConfirmationKey()]
                    )
                )
            );
    }

    public function getConfirmationKey(): string
    {
        // enconde changes to @@
        return SysUtils::encodeStr(date('YmdHis') . '--' . $this->id);
    }

    public function setPictureFromUrl(string $url): void
    {
        // download image
        $file = file_get_contents($url);
        if (false === $file) {
            return;
        }

        // create a temporary file
        $tempFile = tmpfile();
        fwrite($tempFile, $file);
        $metaData = stream_get_meta_data($tempFile);
        $tempFilePath = $metaData['uri'];

        // set picture url
        $this->setPictureUrl(new UploadedFile($tempFilePath, 'user_picture.jpg'));

        // close and delete the temporary file
        fclose($tempFile);
    }

    public function getCurrentPlan(): ?UserPlans
    {
        return $this->plans()
            ->where('status', UserPlans::STATUS_ACTIVE)
            ->where('start_date', '<=', SysUtils::timezoneNow('Y-m-d'))
            ->where('end_date', '>=', SysUtils::timezoneNow('Y-m-d'))
            ->orderBy('end_date', 'desc')
            ->first();
    }

    public function getPlanType(): string
    {
        if ($this->isRoot()) {
            return FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM;
        }

        $cacheKey = $this->getPlanTypeCacheKey();
        $cacheTTL = 60 * 60 * 8; // 8 horas

        // check cache
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // get current plan
        $plan = $this->getCurrentPlan();
        $planType = $plan?->plan_type ?? FeatureAbstract::FEATURE_PLAN_TYPE_FREE;

        // if we dont have a plan, or the plan type is not valid, set to free
        if (!in_array($planType, FeatureAbstract::fGetPlanTypes())) {
            $planType = FeatureAbstract::FEATURE_PLAN_TYPE_FREE;
        }

        // save cache and return
        Cache::put($cacheKey, $planType, $cacheTTL);
        return $planType;
    }

    public function hasPremiumPlan(): bool
    {
        return $this->getPlanType() === FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM;
    }

    public function getPlanTypeLabel(): string
    {
        return FeatureAbstract::fGetLabelPlanType($this->getPlanType());
    }

    private function getPlanTypeCacheKey(): string
    {
        return 'user-plan-type-' . $this->id;
    }

    public function checkPlanPaymentStatus(): void
    {
        $userPlan = $this->plans()
            ->where('start_date', '<=', SysUtils::timezoneNow('Y-m-d'))
            ->where('end_date', '>=', SysUtils::timezoneNow('Y-m-d'))
            ->orderBy('end_date', 'desc')
            ->first();
        if ($userPlan?->status !== UserPlans::STATUS_PENDING) {
            return;
        }

        // check payment status
        $paymentLog = $userPlan->logs->first();
        $paymentClass = $paymentLog->payment_class;
        if (!class_exists($paymentClass)) {
            return;
        }

        $Class = new $paymentClass();
        if ($Class->isPaymentApproved($userPlan)) {
            // set user plan status to STATUS_ACTIVE
            $userPlan->status = UserPlans::STATUS_ACTIVE;
            $userPlan->save();

            // add custom log
            $userPlan->addLog([
                'payment_class' => $paymentClass,
                'payment_id' => $paymentLog->payment_id,
                'data' => json_encode([
                    'type' => 'checkPlanPaymentStatus',
                    'message' => __('messages.pages.premium.paymentStatusChecked'),
                ]),
            ]);

            // remove cache
            Cache::forget($this->getPlanTypeCacheKey());
        }
    }
    // ===============

    // static functions
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!empty($user->password)) {
                $user->password = Hash::make($user->password);
            }
        });
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

    public static function fLogin(string $email, string $password, ?GoogleUserLogin $GoogleUserLogin=null): ApiResponse
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
            (false === $User->checkPassword($password) && !$GoogleUserLogin?->getId()) ||
            (!$User->isManager() && !$User->isRoot())
        ) {
            return new ApiResponse(true, __('messages.models.User.fLogin.invalidCredentials'));
        }

        // check confirmation
        if (false == $User->confirmation) {
            $User->sendConfirmationEmail();
            return new ApiResponse(true, __('messages.models.User.fLogin.userNotConfirmed'));
        }

        // all good, register everything
        if (false === SysUtils::loginUser($User)) {
            return new ApiResponse(true, __('messages.models.User.fLogin.loginUserError'));
        }

        // clean reset token
        $User->password_reset_token = null;
        $User->update();
        $User->refresh();

        // clear cache
        Cache::forget($User->getPlanTypeCacheKey());

        return new ApiResponse(false, __('messages.models.User.fLogin.loginSuccess'), [
            'User' => $User
        ]);
    }

    public static function fLoginWithGoogle(SocialiteUser $SocialiteUser): ApiResponse
    {
        $userArray = $SocialiteUser->getRaw();
        $GoogleUser = new GoogleUserLogin($userArray);
        if (empty($GoogleUser->getEmail()) || !filter_var($GoogleUser->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new ApiResponse(true, __('messages.models.User.fLogin.invalidEmail'));
        }

        if (!$User = User::where('email', $GoogleUser->getEmail())->first()) {
            // create new user
            $User = new User();
            $User->first_name = $GoogleUser->getGivenName() ?? '';
            $User->last_name = $GoogleUser->getFamilyName() ?? '';
            $User->email = $GoogleUser->getEmail();
            $User->password = $GoogleUser->getId();
            $User->role = User::ROLE_MANAGER;
            $User->active = true;
            $User->confirmation = true;
            $User->google_login = json_encode($userArray);
            $User->save();
        } else {
            // update user
            $User->first_name = $GoogleUser->getGivenName() ?? '';
            $User->last_name = $GoogleUser->getFamilyName() ?? '';
            $User->google_login = json_encode($userArray);
            $User->update();
        }

        // profile picture from Google
        if (
            (empty($User->picture_url) || $User->picture_url === Constants::USER_DEFAULT_IMAGE_PATH) &&
            !empty($GoogleUser->getPicture())
        ) {
            $User->setPictureFromUrl($GoogleUser->getPicture());
        }

        return User::fLogin(
            $User->email,
            $GoogleUser->getId(),
            $GoogleUser
        );
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
        Mail::to($User->email)
            ->send(
                new ResetPassword($shortResetLink)
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

    public static function fHasAccessCustom(Model $model, ?User $user = null): bool
    {
        if ($model->id > 0 && $model->id !== $user->id) {
            return false;
        }

        return true;
    }

    public static function fSaveBeforeValidate(Model &$model, array $form): ?ApiResponse
    {
        return null;
    }

    public static function fConfirmUser(string $key): ApiResponse
    {
        $decoded = SysUtils::decodeStr($key);
        if (empty($decoded)) {
            return new ApiResponse(true, __('messages.models.User.ConfirmationLink.invalidKey'));
        }

        // changes -- to @@
        $parts = explode('@@', $decoded);
        if (count($parts) !== 2) {
            return new ApiResponse(true, __('messages.models.User.ConfirmationLink.invalidKey'));
        }

        $id = (int) $parts[1];
        $User = User::where('id', $id)
            ->where('active', true)
            ->first();
        if (!$User) {
            return new ApiResponse(true, __('messages.models.User.ConfirmationLink.userNotFound'));
        }

        // confirm user
        $User->confirmation = true;
        $User->update();

        return new ApiResponse(false, __('messages.models.User.ConfirmationLink.successMessage'), [
            'User' => $User,
        ]);
    }
    // ================
}
