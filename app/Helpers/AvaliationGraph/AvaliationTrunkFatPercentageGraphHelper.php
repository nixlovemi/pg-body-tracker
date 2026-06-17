<?php

namespace App\Helpers\AvaliationGraph;

use App\Helpers\Avaliation\TrunkFatPercentage;
use App\Helpers\Constants;
use App\Helpers\SysUtils;
use App\Models\Avaliation;

final class AvaliationTrunkFatPercentageGraphHelper extends AvaliationGraphAbstract
{
    private TrunkFatPercentage $TrunkFatPercentage;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->TrunkFatPercentage = new TrunkFatPercentage($this->getAvaliation());
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationTrunkFatPercentageGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $avaliation = $this->getAvaliation();
        $lineColor = $this->TrunkFatPercentage->getFieldInfo()[Constants::FI_RANK_COLOR];

        // Ideal band colors
        $rgbIdeal = SysUtils::hexToRGB(Constants::RANK_COLOR_2);
        $backgroundColorIdeal = sprintf('rgba(%d, %d, %d, 0.1)', ...$rgbIdeal);

        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;

        $data = $this->initChartData($backgroundColorIdeal, $lineColor);
        $arrValues = [];

        $avaliations = $this->getPreviousAvaliations($queryLimit);

        foreach ($avaliations as $av) {
            $trunkFat = new TrunkFatPercentage($av);
            $fieldValue = $trunkFat->getFieldValue();

            if ($fieldValue != Constants::RETURN_INT_CANT_CALCULATE) {
                $arrValues[] = [
                    'date' => SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat),
                    'value' => $fieldValue
                ];
            }
        }

        // Current avaliation
        $currentValue = $this->TrunkFatPercentage->getFieldValue();
        if ($currentValue != Constants::RETURN_INT_CANT_CALCULATE) {
            $arrValues[] = [
                'date' => SysUtils::reformatDate($avaliation->date, 'Y-m-d', $dateFormat),
                'value' => $currentValue
            ];
        }

        $arrPoints = [];
        $arrDates = [];

        foreach ($arrValues as $item) {
            $arrDates[] = $item['date'];
            $arrPoints[] = $item['value'];

            // Add to table
            $this->addBodyItem(
                $item['date'],
                SysUtils::formatDbToNumber($item['value'], 1) . $this->TrunkFatPercentage->getFieldSuffix()
            );
        }

        // If no data points, return empty array to trigger "insufficient data" message
        if (empty($arrPoints)) {
            return [];
        }

        // Current value line (only dataset needed)
        $data['data']['datasets'][] = [
            'label' => __('messages.components.avaliationReport.trunkFatPercentage'),
            'data' => $arrPoints,
            'borderColor' => $lineColor,
            'backgroundColor' => 'transparent',
            'borderWidth' => 3,
            'fill' => false,
            'tension' => 0.1,
            'pointRadius' => 5,
            'pointBackgroundColor' => $lineColor,
        ];

        $data['data']['labels'] = $arrDates;

        return [
            'type' => 'line',
            'data' => $data['data'],
            'options' => $data['options'],
        ];
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.components.avaliationReport.trunkFatPercentage'));
    }

    protected function initChartData(string $backgroundColor, string $lineColor): array
    {
        return [
            'data' => [
                'labels' => [],
                'datasets' => []
            ],
            'options' => [
                'responsive' => true,
                'title' => [
                    'display' => false,
                ],
                'legend' => [
                    'display' => false,
                    'position' => 'top',
                ],
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                            'padding' => 2.5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => __('messages.components.avaliationReport.trunkFatPercentage') . ' ' . $this->TrunkFatPercentage->getFieldSuffix(),
                        ],
                    ]],
                    'xAxes' => [[
                        'ticks' => ['padding' => 2.5],
                        'scaleLabel' => ['display' => false],
                    ]],
                ],
            ],
        ];
    }
}
