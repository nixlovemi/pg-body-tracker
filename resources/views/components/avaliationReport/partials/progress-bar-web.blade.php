@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
*/
@endphp

@extends('components.avaliationReport.partials.progress-bar', [
    'AVALIATION' => $AVALIATION,
    'DIV_PROGRESS_CLASSES' => 'mt-5',
    'HTML_ARR_BARS_WITH_PLACEHOLDERS' => <<<HTML
        <div style="background-color:!!COLOR!!;" class="progress-bar" role="progressbar" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100">
            <span class="d-inline d-md-none">!!LABEL_MIN!!</span>
            <span class="d-none d-md-inline">!!LABEL!!</span>
        </div>
    HTML,
    'HTML_ARR_BARS_ARROW_WITH_PLACEHOLDERS' => <<<HTML
        <div class="progress-bar" role="progressbar" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100">
            !!ARROW!!
        </div>
    HTML,
    'ARROW_STYLE' => 'font-size:150%',
])
