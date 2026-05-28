<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Feature\CheckinFollowUp as CheckinFollowUpFeature;

class CheckinFollowUp
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
        $feature = new CheckinFollowUpFeature();
        if (false === $feature->validate()) {
            return redirect(route('app.dashboard.index'))
                ->withErrors(['msg' => $feature->getValidateMsg()]);
        }

        return $next($request);
    }
}
