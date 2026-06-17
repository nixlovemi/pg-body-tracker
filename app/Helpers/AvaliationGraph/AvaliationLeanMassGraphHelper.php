<?php

namespace App\Helpers\AvaliationGraph;

use App\Helpers\Avaliation\LeanMass;
use App\Helpers\Constants;
use App\Helpers\SysUtils;
use App\Models\Avaliation;

final class AvaliationLeanMassGraphHelper extends AvaliationGraphAbstract
{
    private LeanMass $LeanMass;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->LeanMass = new LeanMass($this->getAvaliation());
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationLeanMassGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $avaliation = $this->getAvaliation();
        $lineColor = $this->LeanMass->getFieldInfo()[Constants::FI_RANK_COLOR];

        $rgb = SysUtils::hexToRGB(Constants::RANK_COLOR_2);
        $backgroundColor = sprintf('rgba(%d, %d, %d, 0.1)', ...$rgb);

        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;

        $data = $this->initChartData($backgroundColor, $lineColor);
        $arrValues = [];

        $avaliations = $this->getPreviousAvaliations($queryLimit);

        foreach ($avaliations as $av) {
            $leanMass = new LeanMass($av);
            $this->appendChartPoint(
                $data,
                SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat),
                $leanMass->getMinIdealValue(),
                $leanMass->getMaxIdealValue(),
                $av->getLeanMassKg()
            );
            $arrValues[] = $av->getLeanMassKg();
            $arrValues[] = $leanMass->getMinIdealValue();
            $arrValues[] = $leanMass->getMaxIdealValue();
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
                            'labelString' => __('messages.components.avaliationReport.leanMass') . ' ' . $this->LeanMass->getFieldSuffix(),
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
        $leanMass = new LeanMass($avaliation);
        $value = $avaliation->getLeanMassKg();

        $this->appendChartPoint(
            $data,
            SysUtils::reformatDate($avaliation->date, 'Y-m-d', $dateFormat),
            $leanMass->getMinIdealValue(),
            $leanMass->getMaxIdealValue(),
            $value
        );

        $arrValues[] = $value;
        $arrValues[] = $leanMass->getMinIdealValue();
        $arrValues[] = $leanMass->getMaxIdealValue();
    }

    private function appendChartPoint(array &$data, string $label, float $min, float $max, float $value): void
    {
        $data['labels'][] = $label;
        $data['datasets'][0]['data'][] = $min;
        $data['datasets'][1]['data'][] = $max;
        $data['datasets'][2]['data'][] = $value;

        $this->addBodyItem($label, SysUtils::formatDbToNumber($value, 1) . ' ' . $this->LeanMass->getFieldSuffix());
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.components.avaliationReport.leanMass'));
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
                    'label' => __('messages.components.avaliationReport.leanMass') . ' ' . $this->LeanMass->getFieldSuffix(),
                    'data' => [],
                    'borderColor' => $lineColor,
                    'backgroundColor' => 'transparent',
                ],
            ],
        ];
    }
}
