@inject('QuickChart', 'QuickChart')

@php
/*
View variables:
===============
    - $DEFAULT_COLOR: string
    - $TITLE: string
    - $DATA_TABLE_HTML: string
    - $UID: string
    - $CONFIG: json string
    - $IS_PDF: ?boolean
*/

$IS_PDF = $IS_PDF ?? false;
@endphp

<div class="text-sm font-weight-bold text-secondary text-uppercase mb-3 text-center" style="color:{{$DEFAULT_COLOR}} !important;">
    {{$TITLE}}
</div>

@if (!empty($CONFIG) && $CONFIG != '[]')
    @if ($IS_PDF)
        <style>
            table th, table td {
                text-align: center !important
            }
        </style>

        <div class="text-center">
            <x-chart-php :elementId="$UID" :config="$CONFIG" width="750" height="250" />
        </div>
    @else
        <x-chart-js :elementId="$UID" :config="$CONFIG" />
    @endif

    {!! $DATA_TABLE_HTML !!}
@else
    <div class="row align-items-center h-100">
        <div class="col">
            <div class="h6 text-secondary mb-3 text-center align-middle">
                {{ __('messages.components.avaliationReport.dataErrorPrintReport') }}
            </div>
        </div>
    </div>
@endif
