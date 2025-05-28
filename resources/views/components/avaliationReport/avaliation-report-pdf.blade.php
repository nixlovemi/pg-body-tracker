@extends('components.avaliation-report')

@section('AVALIATION_REPORT_GENERAL_INFO')
    <link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/avaliation-report-general-info.css') }}" type='text/css' media='all' />

    @include('components.avaliationReport.partials.general-info', [
        'AVALIATION' => $Avaliation,
        'DIV_ROW_CLASSES' => 'row mb-0',
        'DIV_COL_CLASSES' => 'col-12 mb-3',
        'DIV_CARD_CLASSES' => 'card border-left-secondary shadow py-2',
        'DIV_USER_INFO_CLASSES' => 'h6 mb-0 text-gray-800',
        'TABLE_SOCIAL_1_CLASSES' => 'd-block',
        'TABLE_SOCIAL_2_CLASSES' => 'd-none',
        'DIV_COL_CLIENT_INFO_CLASSES' => 'col-5 mb-3',
        'DIV_CLIENT_INFO_EXTRA_SPACE' => true,
        'DIV_COL_PROGRESS_CLASSES' => 'col-7 mb-3',
        'PROGRESS_BAR_VIEW_NAME' => 'components.avaliationReport.partials.progress-bar-pdf',
    ])
@endsection
