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
}
