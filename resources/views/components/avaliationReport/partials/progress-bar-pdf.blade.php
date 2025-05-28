@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
*/
@endphp

@extends('components.avaliationReport.partials.progress-bar', [
    'AVALIATION' => $AVALIATION,
    'DIV_PROGRESS_CLASSES' => 'mt-1',
    'HTML_ARR_BARS_WITH_PLACEHOLDERS' => <<<HTML
        <div class="is-pdf-progress-div" style="background-color:!!COLOR!!;">
            <span style="position:relative; top:25px;">!!LABEL!!</span>
        </div>
    HTML,
    'HTML_ARR_BARS_ARROW_WITH_PLACEHOLDERS' => <<<HTML
        <div class="is-pdf-progress-div">
            !!ARROW!!
        </div>
    HTML,
    'ARROW_STYLE' => 'color:#5a5c69; font-size:250%; position:relative; top:0;',
])
