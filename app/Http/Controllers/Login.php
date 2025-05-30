<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\SysUtils;
use App\Models\User;
use App\Helpers\ApiResponse;

class Login extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        SysUtils::logout(false);
        return view('app.login', [
            'PAGE_TITLE' => 'Login',
        ]);
    }

    public function doLogin(Request $request)
    {
        $form = $request->only(['f-email', 'f-password']);
        $response = User::fLogin(
            $form['f-email'] ?? '',
            $form['f-password'] ?? ''
        );
        if ($response->isError()) {
            return $this->redirectWithError('app.login', $response->getMessage());
        }

        return redirect()->route('app.dashboard.index');
    }

    public function forgot()
    {
        return view('app.login-forgot', [
            'PAGE_TITLE' => __('messages.pages.login.forgot.title'),
        ]);
    }

    public function doForgot(Request $request)
    {
        $form = $request->only(['f-email']);
        $ret = User::fRecoverPwd($form['f-email'] ?? '');
        if ($ret->isError()) {
            return $this->redirectWithError('app.forgot', $ret->getMessage());
        }

        return $this->redirectSuccess('app.forgot', $ret->getMessage());
    }

    public function resetPwd(string $idKey)
    {
        SysUtils::logout(false);
        return view('app.login-reset-pwd', [
            'PAGE_TITLE' => __('messages.pages.login.resetPwd.title'),
            'ID_KEY' => $idKey,
        ]);
    }

    public function doResetPwd(Request $request)
    {
        $idKey = $request->input('f-idkey') ?: '';
        $newPassword = $request->input('f-password') ?: '';
        $newPasswordCheck = $request->input('f-rtype-password') ?: '';

        $ret = User::fResetPasswordByToken($idKey, $newPassword, $newPasswordCheck);
        if ($ret->isError()) {
            return redirect()->back()
                ->withErrors(['msg' => $ret->getMessage()]);
        }

        return $this->redirectSuccess('app.login', $ret->getMessage());
    }

    public function register()
    {
        return view('app.register', [
            'PAGE_TITLE' => __('messages.pages.login.register.title'),
        ]);
    }

    public function doRegister(Request $request)
    {
        $form = $this->getDoRegisterForm($request);
        if (!empty($form['email'])) {
            if ($error = $this->doRegisterCheckEmailExists($form['email'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['msg' => $error]);
            }
        }

        $ret = User::fSave($form);
        if ($ret->isError()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['msg' => ApiResponse::getValidateMessage($ret)]);
        }

        $User = $ret->getValueFromResponse('User');
        $User->sendConfirmationEmail();
        return $this->redirectSuccess('app.login', __('messages.pages.login.register.successMessage'));
    }

    private function getDoRegisterForm(Request $request)
    {
        $form = [];
        $form['first_name'] = $request->input('f-name') ?: null;
        $form['last_name'] = $request->input('f-lastname') ?: null;
        $form['email'] = $request->input('f-email') ?: null;
        $form['password'] = $request->input('f-password') ?: null;
        $form['role'] = User::ROLE_MANAGER;
        $form['confirmation'] = false;

        return $form;
    }

    private function doRegisterCheckEmailExists(string $email): ?string
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return __('messages.pages.login.register.emailExists');
        }

        return null;
    }

    public function confirmUser(string $key)
    {
        $ret = User::fConfirmUser($key);
        if ($ret->isError()) {
            return $this->redirectWithError('app.login', $ret->getMessage());
        }

        return $this->redirectSuccess('app.login', $ret->getMessage());
    }
}
