<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SysUtils;

class IsAlreadyPremium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $User = SysUtils::getLoggedInUser();
        $isPremium = $User->hasPremiumPlan() ?? false;
        if ($isPremium) {
            return redirect(route('app.dashboard.index'))
                ->withErrors(['msg' => __('messages.pages.premium.paymentClassNotFound')]);
        }

        // check also the env PAYMENT_CLASS if class exists
        $paymentClass = env('PAYMENT_CLASS', '');
        if (!class_exists($paymentClass)) {
            return redirect(route('app.user.profile'))->withErrors(__('messages.errors.paymentClassNotFound', ['class' => $paymentClass]));
        }

        return $next($request);
    }
}
