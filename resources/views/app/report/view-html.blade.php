@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $REPORT: App\Helpers\Report\ReportAbstract
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @include('layout.partials.button-back')
    @include('app.report.view', [
        'PAGE_TITLE' => $PAGE_TITLE ?? '',
        'REPORT' => $REPORT,
        'ROW_CLASS' => 'report-view',
        'DISPLAY_DOWNLOADS' => true,
    ])
@endsection
