<?php

namespace App\Http\Middleware;

use Closure;
use App\View\Components\Notification;
use Illuminate\Support\Facades\Route;
use App\Helpers\Permissions;

class AuthenticateWeb
{
    public function handle($request, Closure $next)
    {
        $routeName = Route::currentRouteName();
        $canAccess = Permissions::checkPermission($routeName);
        if (false === $canAccess) {
            return $this->redirectNoPermission();
        }

        // all good
        return $next($request);
    }

    private function redirectNoPermission()
    {
        return redirect()
            ->route('app.login')
            ->withErrors(['msg' => __('messages.dontHavePermission')]);
    }
}
