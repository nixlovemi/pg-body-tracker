<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateWebExpireAt
{
    public const SESSION_NAME = 'auth.expire-at';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && session(self::SESSION_NAME) && now()->gt(session(self::SESSION_NAME))) {
            Auth::logout();
            session()->forget(self::SESSION_NAME);
            return redirect(route('app.login'))
                ->withErrors(['msg' => __('messages.pages.signedExpired.title')]);
        }

        return $next($request);
    }
}
