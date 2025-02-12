@php
/*
View variables:
===============
    - $AVALIATION: App\Models\Avaliations
*/
@endphp

<link rel="stylesheet" href="{{ public_path('/base-reset.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/bootstrap/css/bootstrap.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/font-awesome-5/css/all.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/sb-admin-2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/custom.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/base.css') }}" type='text/css' media='all' />

<style>
    div[class*="col-"] {
        float: left;
        display: inline-block;
    }
    .card-body {
        display: inline-block;
    }
</style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<x-avaliation-report
    :avaliationId="$AVALIATION->id"
    isPdf="true"
/>
