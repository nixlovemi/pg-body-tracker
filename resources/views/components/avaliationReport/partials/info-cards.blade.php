@inject('ARPresenter', 'App\Presenters\AvaliationReportPresenter')

@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
    - $IS_PDF: bool
*/
@endphp

<div @class(['row', 'mt-3' => !$IS_PDF, 'mt-0' => $IS_PDF])>
    @foreach ($ARPresenter::getInfoCardsData($AVALIATION) as $item)
        <div @class(['mb-3', 'col-12 col-lg-4' => !$IS_PDF, 'col-6' => $IS_PDF])>
            @include('components.avaliation-report-info-card', [
                'TITLE' => $item['title'],
                'COLOR' => $item['info'][$Constants::FI_RANK_COLOR] ?? '',
                'RESULT' => $item['info'][$Constants::FI_FIELD_LABEL] ?? '',
                'DIAGNOSIS' => $item['info'][$Constants::FI_RANK_LABEL] ?? '',
                'REFERENCE' => $item['showReference'] ? ($item['info'][$Constants::FI_IDEAL_LABEL] ?? '') : null,
                'IS_PDF' => $IS_PDF,
            ])
        </div>

        @if ($loop->index == 9 && $IS_PDF)
            <div class="page-break"></div>
            <br />
        @endif
    @endforeach
</div>
