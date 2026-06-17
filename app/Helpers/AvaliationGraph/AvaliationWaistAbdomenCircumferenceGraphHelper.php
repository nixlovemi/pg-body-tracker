<?php

namespace App\Helpers\AvaliationGraph;

use App\Helpers\Avaliation\WaistAbdomenCircumference;
use App\Helpers\Constants;
use App\Helpers\SysUtils;
use App\Models\Avaliation;

final class AvaliationWaistAbdomenCircumferenceGraphHelper extends AvaliationGraphAbstract
{
    private WaistAbdomenCircumference $WaistAbdomenCircumference;
    private const COLOR_WAIST = Constants::GRAPH_COLOR_LIGHT_BLUE;
    private const COLOR_ABDOMEN = Constants::GRAPH_COLOR_LIGHT_PINK;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->WaistAbdomenCircumference = new WaistAbdomenCircumference($this->getAvaliation());
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationWaistAbdomenCircumferenceGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $avaliation = $this->getAvaliation();
        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;

        $data = $this->initChartData();
        $arrValues = [];

        $avaliations = $this->getPreviousAvaliations($queryLimit);
        foreach ($avaliations as $av) {
            $hasValues = $av->waist_circ_cm !== null || $av->abdomen_circ_cm !== null;
            if (!$hasValues) {
                continue;
            }

            $helper = new WaistAbdomenCircumference($av);
            $this->appendChartPoint(
                $data,
                SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat),
                $helper->getMinIdealValue(),
                $helper->getMaxIdealValue(),
                $av->waist_circ_cm,
                $av->abdomen_circ_cm
            );

            if ($av->waist_circ_cm !== null) {
                $arrValues[] = (float) $av->waist_circ_cm;
            }
            if ($av->abdomen_circ_cm !== null) {
                $arrValues[] = (float) $av->abdomen_circ_cm;
            }
        }

        $this->appendCurrentChartPoint($data, $avaliation, $dateFormat, $arrValues);

        if (empty($arrValues)) {
            return $arrValues; // Return empty array if there are no values to avoid chart rendering issues
        }

        $max = max($arrValues);
        $min = min($arrValues);
        $stepSize = min(max((($max - $min) / 5), 1), 20);

        return [
            'type' => 'line',
            'data' => $data,
            'options' => [
                'responsive' => true,
                'title' => ['display' => false],
                'legend' => [
                    'display' => true,
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
                            'labelString' => __('messages.components.avaliationReport.waistAbdomenCircumference') . ' cm',
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
        $helper = new WaistAbdomenCircumference($avaliation);

        $this->appendChartPoint(
            $data,
            SysUtils::reformatDate($avaliation->date, 'Y-m-d', $dateFormat),
            $helper->getMinIdealValue(),
            $helper->getMaxIdealValue(),
            $avaliation->waist_circ_cm,
            $avaliation->abdomen_circ_cm
        );

        if ($avaliation->waist_circ_cm !== null) {
            $arrValues[] = (float) $avaliation->waist_circ_cm;
        }
        if ($avaliation->abdomen_circ_cm !== null) {
            $arrValues[] = (float) $avaliation->abdomen_circ_cm;
        }
    }

    private function appendChartPoint(array &$data, string $label, float $min, float $max, ?float $waist, ?float $abdomen): void
    {
        $data['labels'][] = $label;
        $data['datasets'][0]['data'][] = $waist;
        $data['datasets'][1]['data'][] = $abdomen;

        $waistLabel = $waist === null ? '-' : SysUtils::formatDbToNumber($waist, 1) . ' cm';
        $abdomenLabel = $abdomen === null ? '-' : SysUtils::formatDbToNumber($abdomen, 1) . ' cm';

        $this->addBodyItem($label, $waistLabel, $abdomenLabel);
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.models.Avaliation.fields.waist_circ_cm'));
        $this->addHeadItem(__('messages.models.Avaliation.fields.abdomen_circ_cm'));
    }

    private function initChartData(): array
    {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'label' => __('messages.models.Avaliation.fields.waist_circ_cm'),
                    'data' => [],
                    'borderColor' => self::COLOR_WAIST,
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 3,
                    'fill' => false,
                    'tension' => 0.1,
                    'pointRadius' => 5,
                    'pointBackgroundColor' => self::COLOR_WAIST,
                ],
                [
                    'label' => __('messages.models.Avaliation.fields.abdomen_circ_cm'),
                    'data' => [],
                    'borderColor' => self::COLOR_ABDOMEN,
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 3,
                    'fill' => false,
                    'tension' => 0.1,
                    'pointRadius' => 5,
                    'pointBackgroundColor' => self::COLOR_ABDOMEN,
                ],
            ],
        ];
    }
}
