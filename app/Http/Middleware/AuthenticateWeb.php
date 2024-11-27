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
        Notification::setWarning('Atenção!', 'Você não tem acesso a esse conteúdo! Faça o login novamente.');
        return redirect()->route('site.login');
    }
}
