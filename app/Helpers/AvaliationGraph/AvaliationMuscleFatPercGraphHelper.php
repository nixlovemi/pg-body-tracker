<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;
use App\Helpers\SysUtils;

final class AvaliationMuscleFatPercGraphHelper extends AvaliationGraphAbstract
{
    private const PREFIX = '%';
    private const DATASET_FAT_IDX = 0;
    private const DATASET_SKELETAL_MUSCLE_IDX = 1;
    private const FAT_COLOR = Constants::GRAPH_COLOR_LIGHT_PINK;
    private const SKELETAL_MUSCLE_COLOR = Constants::GRAPH_COLOR_LIGHT_BLUE;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationMuscleFatPercGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $Avaliation = $this->getAvaliation();
        $arrPoints = [];

        $dateFormat = strtolower(__('messages.dateFormat'));
        $queryLimit = 9;
        $data = $this->initChartData();

        // prev avaliations
        $prevAvaliations = $this->getPreviousAvaliations($queryLimit);
        foreach ($prevAvaliations as $av) {
            $fatPerc = $av->getBodyFatPerc();
            $skeletalPerc = $av->getSkeletalMuscleMassPerc();
            if (
                $fatPerc === Constants::RETURN_INT_CANT_CALCULATE ||
                $skeletalPerc === Constants::RETURN_INT_CANT_CALCULATE
            ) {
                continue;
            }

            $this->appendChartPoint(
                $data,
                SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat),
                $fatPerc,
                $skeletalPerc
            );
            $arrPoints[] = $fatPerc;
            $arrPoints[] = $skeletalPerc;
        }

        // current avaliation
        $currentFatPerc = $Avaliation->getBodyFatPerc();
        $currentSkeletalPerc = $Avaliation->getSkeletalMuscleMassPerc();
        if (
            $currentFatPerc === Constants::RETURN_INT_CANT_CALCULATE ||
            $currentSkeletalPerc === Constants::RETURN_INT_CANT_CALCULATE
        ) {
            return [];
        }

        $this->appendChartPoint(
            $data,
            SysUtils::reformatDate($Avaliation->date, 'Y-m-d', $dateFormat),
            $currentFatPerc,
            $currentSkeletalPerc
        );
        $arrPoints[] = $currentFatPerc;
        $arrPoints[] = $currentSkeletalPerc;

        // $stepSize = 10;
        $stepSize = min(max(($max = max($arrPoints)) - ($min = min($arrPoints)) / 5, 1), 15);

        $config = [
            'type' => 'bar',
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
                            'stepSize' => $stepSize,
                            'padding' => 2.5
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => 'Perc.',
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

    private function appendChartPoint(array &$data, string $label, float $fat, float $skeletalMuscle): void
    {
        $data['labels'][] = $label;
        $data['datasets'][self::DATASET_FAT_IDX]['data'][] = $fat;
        $data['datasets'][self::DATASET_SKELETAL_MUSCLE_IDX]['data'][] = $skeletalMuscle;

        $fatLabel = $this->getTableRowLabel(
            SysUtils::formatDbToNumber($fat, 1) . self::PREFIX,
            self::FAT_COLOR
        );

        $skeletalLabel = $this->getTableRowLabel(
            SysUtils::formatDbToNumber($skeletalMuscle, 1) . self::PREFIX,
            self::SKELETAL_MUSCLE_COLOR
        );

        // table data
        $this->addBodyItem($label, $fatLabel, $skeletalLabel);
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));
        $this->addHeadItem(__('messages.models.Avaliation.labelFatMass'));
        $this->addHeadItem(__('messages.components.avaliationReport.skeletalMuscle'));
    }

    private function initChartData(): array
    {
        return [
            'labels' => [],
            'datasets' => [
                [
                    'label' => __('messages.models.Avaliation.labelFatMass'),
                    'data' => [],
                    'backgroundColor' => self::FAT_COLOR,
                    'borderColor' => self::FAT_COLOR,
                    'order' => 2,
                ],
                [
                    'type' => 'line',
                    'label' => __('messages.components.avaliationReport.skeletalMuscle'),
                    'data' => [],
                    'backgroundColor' => 'transparent',
                    'borderColor' => self::SKELETAL_MUSCLE_COLOR,
                    'order' => 1,
                ]
            ]
        ];
    }
}
