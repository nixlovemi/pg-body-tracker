<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use App\Models\User as mUser;
use App\Helpers\ApiResponse;
use App\Helpers\SysUtils;
use App\Models\UserInfo;
use Illuminate\Database\Eloquent\Model;

class User extends Controller
{
    private const DO_PROFILE_REDIRECT = 'app.user.profile';
    private const DO_CHANGE_PSW_REDIRECT = 'app.user.changePsw';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function profile()
    {
        return view('app.user.profile', [
            'PAGE_TITLE' => __('messages.profile'),
        ]);
    }

    public function doProfile(Request $request)
    {
        $User = SysUtils::getLoggedInUser();
        $form = $this->formatSaveRequest($request);

        // validate User
        $User->fill($form['User']);
        $validation = $User->validateModel();
        if ($validation->isError()) {
            return $this->redirect(self::DO_PROFILE_REDIRECT, true, ApiResponse::getValidateMessage($validation));
        }

        // validate User Info
        $UserInfo = $User->info ?? new UserInfo();
        $UserInfo->fill($form['UserInfo']);
        $validation = $UserInfo->validateModel();
        if ($validation->isError()) {
            return $this->redirect(self::DO_PROFILE_REDIRECT, true, ApiResponse::getValidateMessage($validation));
        }

        // User Info
        $response = UserInfo::fSave($form['UserInfo'], $User?->info?->codedId ?? null);
        if ($response->isError()) {
            return $this->redirect(self::DO_PROFILE_REDIRECT, true, ApiResponse::getValidateMessage($response));
        }

        // User
        $response = mUser::fSave($form['User'], $User->codedId);
        if ($response->isError()) {
            return $this->redirect(self::DO_PROFILE_REDIRECT, true, ApiResponse::getValidateMessage($response));
        }
        $User->refresh();

        // Pictures
        $this->saveUserPhoto($User, $request, [
            [
                'name' => 'f-user-picture',
                'fieldName' => 'picture_url',
                'method' => 'setPictureUrl',
                'removeMethod' => 'removePictureUrl',
            ],
        ]);
        $this->saveUserPhoto($User?->info, $request, [
            [
                'name' => 'f-userinfo-logo',
                'fieldName' => 'logo_url',
                'method' => 'setLogoUrl',
                'removeMethod' => 'removeLogoUrl',
            ],
        ]);

        return redirect()
            ->route(self::DO_PROFILE_REDIRECT)
            ->withSuccess($response->getMessage());
    }

    public function changePsw()
    {
        return view('app.user.changePsw', [
            'PAGE_TITLE' => __('messages.pages.changePsw.title'),
        ]);
    }

    public function doChangePsw(Request $request)
    {
        $form = [
            'old_password' => $request->input('f-cur-psw'),
            'new_password' => $request->input('f-new-psw'),
            'new_password_confirmation' => $request->input('f-confirm-new-psw'),
        ];
        $User = SysUtils::getLoggedInUser();
        if (null === $User) {
            return $this->redirect(self::DO_CHANGE_PSW_REDIRECT, true, __('messages.saveModelNotFound', [
                'model' => __('messages.models.User.name'),
            ]));
        }

        $changeRet = $User->changePassword(
            $form['new_password'] ?? '',
            $form['new_password_confirmation'] ?? '',
            $form['old_password'] ?? ''
        );
        if ($changeRet->isError()) {
            return $this->redirect(self::DO_CHANGE_PSW_REDIRECT, true, ApiResponse::getValidateMessage($changeRet));
        }

        return redirect()
            ->route(self::DO_CHANGE_PSW_REDIRECT)
            ->withSuccess($changeRet->getMessage());
    }

    public function payments()
    {
        $User = SysUtils::getLoggedInUser();
        if ($User?->plans?->count() <= 0) {
            return $this->redirectWithError('app.dashboard.index', __('messages.pages.premium.paymentHistory.redirectDoestHavePayments'));
        }

        return view('app.user.payments', [
            'PAGE_TITLE' => __('messages.pages.premium.paymentHistory.menuTitle'),
        ]);
    }

    private function formatSaveRequest(Request $request): array
    {
        $form = ['User' => [], 'UserInfo' => []];

        $form['User']['first_name'] = $request->input('f-user-name') ?? null;
        $form['User']['last_name'] = $request->input('f-user-lname') ?? null;

        $form['UserInfo']['user_id'] = SysUtils::getLoggedInUser()?->id ?? null;
        $form['UserInfo']['title'] = $request->input('f-userinfo-title') ?? null;
        $form['UserInfo']['license_text'] = $request->input('f-userinfo-lictext') ?? null;
        $form['UserInfo']['evaluation_mode'] = $request->input('f-userinfo-mode') ?? null;
        $form['UserInfo']['whatsapp_phone'] = $request->input('f-userinfo-whats') ?? null;
        $form['UserInfo']['link_telegram'] = $request->input('f-userinfo-telegram') ?? null;
        $form['UserInfo']['link_facebook'] = $request->input('f-userinfo-face') ?? null;
        $form['UserInfo']['link_instagram'] = $request->input('f-userinfo-insta') ?? null;
        $form['UserInfo']['link_twitter'] = $request->input('f-userinfo-twit') ?? null;
        $form['UserInfo']['link_youtube'] = $request->input('f-userinfo-yt') ?? null;
        $form['UserInfo']['link_website'] = $request->input('f-userinfo-site') ?? null;

        return $form;
    }

    private function redirect(string $route, bool $isError, string $errorMsg, array $routeParams=[])
    {
        $redirect = redirect()
            ->route($route, $routeParams);

        if ($isError) {
            $redirect->withInput()
                ->withErrors(['msg' => $errorMsg]);

            return $redirect;
        }

        return $redirect->withSuccess($errorMsg);
    }

    private function saveUserPhoto(Model $model, Request $request, array $arrLoop): void
    {
        foreach ($arrLoop as $loop) {
            // check if we need to remove image
            $inputRemove = 'remove_' . $loop['name'];
            if ($request->input($inputRemove) == 1) {
                $model->{$loop['removeMethod']}($loop['fieldName']);
                continue;
            }

            // save image
            $file = $request->file($loop['name']);
            $model->{$loop['method']}($file);
        }
    }
}
