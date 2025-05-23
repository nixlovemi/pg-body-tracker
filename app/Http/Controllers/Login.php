<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\SysUtils;
use App\Models\User;

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
}
