<?php

namespace App\Helpers\AvaliationGraph;

use App\Helpers\Avaliation\FatMass;
use App\Helpers\Constants;
use App\Helpers\SysUtils;
use App\Models\Avaliation;

final class AvaliationFatMassGraphHelper extends AvaliationGraphAbstract
{
    private FatMass $FatMass;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->FatMass = new FatMass($this->getAvaliation());
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationFatMassGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $avaliation = $this->getAvaliation();
        $lineColor = $this->FatMass->getFieldInfo()[Constants::FI_RANK_COLOR];

        $rgb = SysUtils::hexToRGB(Constants::RANK_COLOR_2);
        $backgroundColor = sprintf('rgba(%d, %d, %d, 0.1)', ...$rgb);

        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;

        $data = $this->initChartData($backgroundColor, $lineColor);
        $arrValues = [];

        $avaliations = $this->getPreviousAvaliations($queryLimit);

        foreach ($avaliations as $av) {
            $fatMass = new FatMass($av);
            $this->appendChartPoint(
                $data,
                SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat),
                $fatMass->getMinIdealValue(),
                $fatMass->getMaxIdealValue(),
                $av->getFatMassKg()
            );
            $arrValues[] = $av->getFatMassKg();
            $arrValues[] = $fatMass->getMinIdealValue();
            $arrValues[] = $fatMass->getMaxIdealValue();
        }

        $this->appendCurrentChartPoint($data, $avaliation, $dateFormat, $arrValues);

        $max = max($arrValues);
        $min = min($arrValues);
        $stepSize = min(max((($max - $min) / 5), 1), 15);

        return [
            'type' => 'line',
            'data' => $data,
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
                            'suggestedMin' => $min - $stepSize,
                            'suggestedMax' => $max + $stepSize,
                            'stepSize' => $stepSize,
                            'padding' => 2.5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => __('messages.components.avaliationReport.fatMass') . ' ' . $this->FatMass->getFieldSuffix(),
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

    private function appendCurrentChartPoint(array &$data, Avaliation $avaliation, string $dateFormat, array &$arrValues): void
    {
        $fatMass = new FatMass($avaliation);
        $value = $avaliation->getFatMassKg();

        $this->appendChartPoint(
            $data,
            SysUtils::reformatDate($avaliation->date, 'Y-m-d', $dateFormat),
            $fatMass->getMinIdealValue(),
            $fatMass->getMaxIdealValue(),
            $value
        );

        $arrValues[] = $value;
        $arrValues[] = $fatMass->getMinIdealValue();
        $arrValues[] = $fatMass->getMaxIdealValue();
    }

    private function appendChartPoint(array &$data, string $label, float $min, float $max, float $value): void
    {
        $data['labels'][] = $label;
        $data['datasets'][0]['data'][] = $min;
        $data['datasets'][1]['data'][] = $max;
        $data['datasets'][2]['data'][] = $value;

        $this->addBodyItem($label, SysUtils::formatDbToNumber($value, 1) . ' ' . $this->FatMass->getFieldSuffix());
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.components.avaliationReport.fatMass'));
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
                    'label' => __('messages.components.avaliationReport.fatMass') . ' ' . $this->FatMass->getFieldSuffix(),
                    'data' => [],
                    'borderColor' => $lineColor,
                    'backgroundColor' => 'transparent',
                ],
            ],
        ];
    }
}
