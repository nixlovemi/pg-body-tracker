<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;
use App\Helpers\Avaliation\Weight;
use App\Helpers\SysUtils;

final class AvaliationWeightGraphHelper extends AvaliationGraphAbstract
{
    private Weight $Weight;

    public function __construct(
        private int $avaliationId,
    ) {
        $this->Weight = new Weight($this->getAvaliation());
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationWeightGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $Avaliation = $this->getAvaliation();
        $idealMin = $this->Weight->getMinIdealValue();
        $idealMax = $this->Weight->getMaxIdealValue();
        $lineColor = $this->Weight->getFieldInfo()[Constants::FI_RANK_COLOR];

        $rgb = SysUtils::hexToRGB(Constants::RANK_COLOR_2);
        $backgroundColor = sprintf("rgba(%d, %d, %d, 0.1)", ...$rgb);

        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;

        $data = $this->initChartData($backgroundColor, $lineColor);
        $arrWeights = [$idealMin, $idealMax];

        $avaliations = $this->getPreviousAvaliations($queryLimit);

        if ($avaliations->count() < $queryLimit) {
            $this->appendChartPoint($data, $Avaliation->client->created_at->format($dateFormat), $idealMin, $idealMax, $Avaliation->client->weight_kg);
            $arrWeights[] = $Avaliation->client->weight_kg;
        }

        foreach ($avaliations as $av) {
            $this->appendChartPoint($data, SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat), $idealMin, $idealMax, $av->weight_kg);
            $arrWeights[] = $av->weight_kg;
        }

        $this->appendChartPoint($data, SysUtils::reformatDate($Avaliation->date, 'Y-m-d', $dateFormat), $idealMin, $idealMax, $Avaliation->weight_kg);
        $arrWeights[] = $Avaliation->weight_kg;

        $stepSize = min(max(($max = max($arrWeights)) - ($min = min($arrWeights)) / 5, 1), 15);

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
                            'labelString' => __('messages.models.Client.fields.weight') . ' ' . $this->Weight->getFieldSuffix(),
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
        $this->addBodyItem($label, SysUtils::formatDbToNumber($value, 1) . ' ' . $this->Weight->getFieldSuffix());
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.models.Client.fields.weight'));
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
                    'label' => __('messages.models.Client.fields.weight') . ' ' . $this->Weight->getFieldSuffix(),
                    'data' => [],
                    'borderColor' => $lineColor,
                    'backgroundColor' => 'transparent',
                ]
            ]
        ];
    }
}
