<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Feature\ReportPremium as ReportPremiumFeature;
use App\Http\Controllers\Report as ReportController;

class ReportPremium
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
        $report = ReportController::getReportFromClass(
            class_basename($request->getRequestUri())
        );

        if (null === $report) {
            return redirect(route('app.report.index'))
                ->withErrors(['msg' => __('messages.components.Features.ReportPremium.validateMessage')]);
        }

        $RepExportFeature = ReportPremiumFeature::make($report);
        if (false === $RepExportFeature->validate()) {
            return redirect(route('app.report.index'))
                ->withErrors(['msg' => $RepExportFeature->getValidateMsg()]);
        }

        return $next($request);
    }
}
