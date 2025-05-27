<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;
use App\Helpers\Avaliation\BodyWater;
use App\Helpers\SysUtils;

final class AvaliationBodyWaterGraphHelper extends AvaliationGraphAbstract
{
    private BodyWater $BodyWater;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->BodyWater = new BodyWater($this->getAvaliation());
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationBodyWaterGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $Avaliation = $this->getAvaliation();
        $idealMin = $this->BodyWater->getMinIdealValue();
        $idealMax = $this->BodyWater->getMaxIdealValue();
        $lineColor = $this->BodyWater->getFieldInfo()[Constants::FI_RANK_COLOR];

        $backgroundColor = Constants::GRAPH_COLOR_LIGHT_BLUE;
        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;

        $data = $this->initChartData($backgroundColor, $lineColor);
        $arrMinMax = [$idealMin, $idealMax];

        // previous avaliations
        $avaliations = $this->getPreviousAvaliations($queryLimit);
        foreach ($avaliations as $av) {
            $this->appendChartPoint($data, SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat), $idealMin, $idealMax, $av->body_water_perc);
            $arrMinMax[] = $av->body_water_perc;
        }

        // current avaliation
        $this->appendChartPoint($data, SysUtils::reformatDate($Avaliation->date, 'Y-m-d', $dateFormat), $idealMin, $idealMax, $Avaliation->body_water_perc);
        $arrMinMax[] = $Avaliation->body_water_perc;

        $stepSize = min(max(($max = max($arrMinMax)) - ($min = min($arrMinMax)) / 5, 1), 10);

        $config = [
            'type' => 'line',
            'data' => $data,
            'options' => [
                'responsive' => true,
                'title' => [
                    'display' => false,
                ],
                'legend' => [
                    'display' => false,
                    'position' => 'top'
                ],
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'suggestedMin' => $min - $stepSize,
                            'suggestedMax' => $max + $stepSize,
                            'stepSize' => $stepSize,
                            'padding' => 2.5
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => __('messages.components.avaliationReport.bodyWater', []) . ' ' . $this->BodyWater->getFieldSuffix(),
                        ]
                    ]],
                    'xAxes' => [[
                        'ticks' => ['padding' => 2.5],
                        'scaleLabel' => ['display' => false]
                    ]]
                ],
            ]
        ];

        return $config;
    }

    private function appendChartPoint(array &$data, string $label, $min, $max, $value): void
    {
        $data['labels'][] = $label;
        $data['datasets'][0]['data'][] = $min;
        $data['datasets'][1]['data'][] = $max;
        $data['datasets'][2]['data'][] = $value;

        // table data
        $this->addBodyItem($label, SysUtils::formatDbToNumber($value, 1) . ' ' . $this->BodyWater->getFieldSuffix());
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.components.avaliationReport.bodyWater', []));
    }

    private function initChartData(string $bgColor, string $lineColor): array
    {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'data' => [],
                    'backgroundColor' => $bgColor,
                    'borderWidth' => 0,
                    'pointRadius' => 0,
                    'fill' => '+1',
                    'hoverRadius' => 0,
                    'hitRadius' => 0,
                    'borderColor' => 'transparent',
                ],
                [
                    'data' => [],
                    'backgroundColor' => $bgColor,
                    'borderWidth' => 0,
                    'pointRadius' => 0,
                    'fill' => false,
                    'hoverRadius' => 0,
                    'hitRadius' => 0,
                    'borderColor' => 'transparent',
                ],
                [
                    'label' => __('messages.components.avaliationReport.bodyWater', []) . ' ' . $this->BodyWater->getFieldSuffix(),
                    'data' => [],
                    'borderColor' => $lineColor,
                    'backgroundColor' => 'transparent',
                ]
            ]
        ];
    }
}
