@inject('ARPresenter', 'App\Presenters\AvaliationReportPresenter')

@php
/*
View variables:
===============
    - $DIV_CARD_CLASSES: string
    - $DIV_CARD_HAS_BREAK_CLASS: bool
*/
@endphp

<style>
    .is-pdf-card-graph {
        height: 1000px;
        overflow-y: hidden;
    }
    .is-pdf-card-graph-first {
        height: 800px;
        overflow-y: hidden;
    }
</style>

<div class="row">
    @foreach ($ARPresenter::getGraphData() as $graph)
        <div class="col-12 col-lg-6 mb-3">
            <div @class([
                $DIV_CARD_CLASSES,
                'is-pdf-card-graph-first' => $DIV_CARD_HAS_BREAK_CLASS && $loop->index === 0,
                'is-pdf-card-graph' => $DIV_CARD_HAS_BREAK_CLASS && $loop->index !== 0,
            ])>
                <div class="card-body">
                    <x-avaliation-graph
                        :avaliationId="$Avaliation->id"
                        :isPdf="$DIV_CARD_HAS_BREAK_CLASS"
                        title="{{ $graph['title'] }}"
                        helperClass="{{  $graph['helperClass'] }}"
                    />
                </div>
            </div>
        </div>

        @if ($DIV_CARD_HAS_BREAK_CLASS)
            <div class="page-break"></div>
            <br />
        @endif
    @endforeach
</div>
