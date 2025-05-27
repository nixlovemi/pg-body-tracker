@php
/*
View variables:
===============
    - $AVALIATION: App\Models\Avaliations
*/
@endphp

<link rel="stylesheet" href="{{ public_path('/base-reset.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/bootstrap-v3.6.6/bootstrap.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/font-awesome-5/css/all.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/sb-admin-2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/custom.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/base.css') }}" type='text/css' media='all' />

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: sans-serif;
        font-size: 12px;
        color: #000;
    }

    .container {
        width: 100%;
        margin: 0 auto;
        padding: 10px;
    }

    .row {
        display: block;
        width: 100%;
        clear: both;
    }

    [class*="col-"] {
        display: inline-block;
        vertical-align: top;
        padding: 5px;
        float: none !important;
    }

    .col-1 { width: 8.33%; }
    .col-2 { width: 16.66%; }
    .col-3 { width: 25%; }
    .col-4 { width: 33.33%; }
    .col-5 { width: 41.66%; }
    .col-6 { width: 50%; }
    .col-7 { width: 58.33%; }
    .col-8 { width: 66.66%; }
    .col-9 { width: 75%; }
    .col-10 { width: 83.33%; }
    .col-11 { width: 91.66%; }
    .col-12 { width: 100%; }
    .card {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .card-header {
        font-weight: bold;
        margin-bottom: 5px;
        background: #f8f8f8;
        padding: 5px;
        border-bottom: 1px solid #ddd;
    }

    .card-body {
        padding: 5px;
    }

    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }

    .mb-1 { margin-bottom: 5px; }
    .mb-2 { margin-bottom: 10px; }
    .mb-3 { margin-bottom: 15px; }

    .mt-1 { margin-top: 5px; }
    .mt-2 { margin-top: 10px; }
    .mt-3 { margin-top: 15px; }

    .bold { font-weight: bold; }

    .row > [class*="col-"] {
        margin-right: 1%; /* opcional */
    }
</style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<x-avaliation-report
    :avaliationId="$AVALIATION->id"
    isPdf="true"
/>
