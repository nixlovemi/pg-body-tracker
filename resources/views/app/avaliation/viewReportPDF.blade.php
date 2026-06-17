@php
/*
View variables:
===============
    - $AVALIATION: App\Models\Avaliations
    - $PREVIOUS_AVALIATIONS: Illuminate\Database\Eloquent\Collection (pre-loaded for PDF optimization)
    - $INFO_CARDS_DATA: array (pre-calculated for PDF optimization)
    - $INCLUDE_GRAPHS: bool
    - $INCLUDE_PICTURES: bool
*/
@endphp

<link rel="stylesheet" href="{{ public_path('/base-reset.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/bootstrap-v3.6.6/bootstrap.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/components/font-awesome-5/css/all.min.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/sb-admin-2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/custom.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ public_path('/base.css') }}" type='text/css' media='all' />
<link rel="stylesheet" href="{{ public_path('/template/start-bootstrap/css/avaliation-report-pdf.css') }}" type='text/css' media='all' />

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<x-avaliation-report
    :avaliationId="$AVALIATION->id"
    isPdf="true"
    :previousAvaliations="$PREVIOUS_AVALIATIONS ?? null"
    :infoCardsData="$INFO_CARDS_DATA ?? null"
    :includeGraphs="$INCLUDE_GRAPHS ?? true"
    :includePictures="$INCLUDE_PICTURES ?? true"
/>
