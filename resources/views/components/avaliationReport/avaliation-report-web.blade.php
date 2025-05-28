@extends('components.avaliation-report')

@section('AVALIATION_REPORT_GENERAL_INFO')
    <link rel='stylesheet' href='{{ url('/') }}template/start-bootstrap/css/avaliation-report-general-info.css' type='text/css' media='all' />
    <style>
        @media (max-width: 1200px) {
            #uinfo-logo,
            #uinfo-info {
                width: 100% !important;
                text-align: center !important;
                float: none !important;
            }
            #uinfo-logo {
                position: relative;
                margin: 0 auto;
            }
            #uinfo-info {
                margin-top: 10px;
            }
        }
    </style>

    @include('components.avaliationReport.partials.general-info', [
        'AVALIATION' => $Avaliation,
        'DIV_ROW_CLASSES' => 'row mb-xl-3',
        'DIV_COL_CLASSES' => 'col-12 mb-3 col-xl-8 mb-xl-0',
        'DIV_CARD_CLASSES' => 'card border-left-secondary shadow py-2 h-100',
        'DIV_USER_INFO_CLASSES' => 'h6 mb-1 text-gray-800',
        'TABLE_SOCIAL_1_CLASSES' => 'd-none d-xl-block',
        'TABLE_SOCIAL_2_CLASSES' => 'd-xl-none',
        'DIV_COL_CLIENT_INFO_CLASSES' => 'col-12 mb-0 col-xl-4',
        'DIV_CLIENT_INFO_EXTRA_SPACE' => false,
        'DIV_COL_PROGRESS_CLASSES' => 'col-12 mt-3',
        'PROGRESS_BAR_VIEW_NAME' => 'components.avaliationReport.partials.progress-bar-web',
    ])
@endsection
