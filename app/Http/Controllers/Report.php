<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Report\ReportAbstract;

class Report extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('app.report.index', [
            'PAGE_TITLE' => __('messages.pages.report.title'),
        ]);
    }

    public function view(string $reportClass)
    {
        $report = $this->getReportFromClassOrRedirect($reportClass);
        return view('app.report.view-html', [
            'PAGE_TITLE' => $report->getTitle(),
            'REPORT' => $report,
        ]);
    }

    public function pdf(string $reportClass)
    {
        $report = $this->getReportFromClassOrRedirect($reportClass);
        $pdf = Pdf::loadView('app.report.view-pdf', [
            'REPORT' => $report,
        ]);
        return $pdf->stream();
    }

    private function getReportFromClassOrRedirect(string $reportClass)
    {
        $report = self::getReportFromClass($reportClass);
        if (null === $report) {
            return $this->redirectWithError('app.report.index', __('messages.pages.report.reportNotFound'));
        }

        return $report;
    }

    public static function getReportFromClass(string $reportClass): null|ReportAbstract
    {
        $reportClass = 'App\\Helpers\\Report\\' . ucfirst($reportClass);
        if (!class_exists($reportClass)) {
            return null;
        }

        return new $reportClass();
    }
}
