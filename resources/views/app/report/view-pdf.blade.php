@inject('SysUtils', 'app\Helpers\SysUtils')

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

<div class="header card-header py-3 d-flex flex-row align-items-center justify-content-between">
    <div class="row" style="width: 100%;">
        <div class="col-xs-6">
            <h3 style="margin: 0; color: #2D8EDB; font-size: 18px;">
                {{ $PAGE_TITLE ?? $REPORT->getTitle() }}
            </h3>
        </div>
        <div class="col-xs-6 text-right">
            <small style="font-size: 11px; color: #999; position:relative; left:-40px; top: 3px;">
                {{ __('messages.pages.report.generatedAt') }} {{ $SysUtils::timezoneNow(__('messages.fullDateFormat')) }}
            </small>
        </div>
    </div>
</div>

<div class="footer">
    <div class="card-footer text-center" style="font-size:11px; color:#999; margin-top: 30px;">
        © {{ env('APP_NAME') }} {{ date('Y') }} <span class="pagenum"></span>
    </div>
</div>

<p style="font-size:13px; color:#666;">
    {{ $REPORT->getDescription() }}
</p>
<div class="table-responsive">
    {!! $REPORT->generateHtml() !!}
</div>
