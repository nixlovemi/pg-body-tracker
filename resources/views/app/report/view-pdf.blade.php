@php
/*
View variables:
===============
    - $REPORT: App\Helpers\Report\ReportAbstract
*/
@endphp

<link rel="stylesheet" href="{{ public_path('/base-reset.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/bootstrap-v3.6.6/bootstrap.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/font-awesome-5/css/all.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/sb-admin-2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/custom.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/base.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('template/start-bootstrap/css/reports.css') }}" type='text/css' media='all' />

@include('layout.partials.copyright')
<br /><br />
@include('app.report.view', [
    'PAGE_TITLE' => $REPORT->getTitle(),
    'REPORT' => $REPORT,
    'ROW_CLASS' => 'report-pdf',
    'DISPLAY_DOWNLOADS' => false,
    'DISPLAY_LIVEWIRE' => false,
])
