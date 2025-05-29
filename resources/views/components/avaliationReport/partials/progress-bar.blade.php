@inject('Icons', 'App\Helpers\Icons')
@inject('Constants', 'App\Helpers\Constants')

@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
    - $DIV_PROGRESS_CLASSES: string
    - $HTML_ARR_BARS_WITH_PLACEHOLDERS: string
    - $HTML_ARR_BARS_ARROW_WITH_PLACEHOLDERS: string
    - $ARROW_STYLE: string
*/
@endphp

<style>
    .avaliation-progress-bar label {
        font-weight: bold;
    }
    .avaliation-progress-bar .progress {
        height: 45px;
    }
    .avaliation-progress-bar .progress-bar {
        width: 12.5%;
    }
    .avaliation-progress-bar #arrow-progress {
        font-size: 150%;
    }
    .avaliation-progress-bar .arrow-progress,
    .avaliation-progress-bar .arrow-progress .progress-bar {
        background-color: transparent;
        text-align: center;
        color: #5a5c69;
    }
    .is-pdf-progress-div {
        width: 12.5%;
        height: 50px;
        color: white;
        text-align: center;
        float: left;
    }
</style>

<div class="avaliation-progress-bar">
    <div class="progress {{$DIV_PROGRESS_CLASSES}}">
        @php
            $arrBars = $Constants::getRankBarInfo();
            $bci = $AVALIATION->getBciInfo();
        @endphp

        @foreach ($arrBars as $bar)
            @php
                $HTML_LOOP = $HTML_ARR_BARS_WITH_PLACEHOLDERS;
                foreach ([
                    ['placeholder' => '!!COLOR!!', 'value' => $bar['color']],
                    ['placeholder' => '!!LABEL!!', 'value' => $bar['label']],
                    ['placeholder' => '!!LABEL_MIN!!', 'value' => $bar['labelMin']]
                ] as $item) {
                    $HTML_LOOP = str_replace($item['placeholder'], $item['value'], $HTML_LOOP);
                }
            @endphp

            {!! $HTML_LOOP !!}
        @endforeach
    </div>

    <div class="progress arrow-progress" style="margin:0; padding:0">
        @foreach ($arrBars as $bar)
            @php
            $arrow = '';
            if ($loop->index+1 == ($bci['rank'] ?? null)) {
                $arrow = '<span id="arrow-progress" style="'.$ARROW_STYLE.'">'.$Icons::ARROW_UP.'</span>';
            }
            @endphp

            {!! str_replace('!!ARROW!!', $arrow, $HTML_ARR_BARS_ARROW_WITH_PLACEHOLDERS) !!}
        @endforeach
    </div>
</div>
