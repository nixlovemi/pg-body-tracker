<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;
use App\Helpers\SysUtils;

final class AvaliationUpperLimbsGraphHelper extends AvaliationGraphAbstract
{
    public bool $fullHtmlTable = true;
    private const PREFIX = 'cm';

    private const SEGMENTS = [
        'right_arm_circ_cm' => [
            'label' => 'labelRightArm',
            'color' => Constants::GRAPH_COLOR_LIGHT_PINK,
        ],
        'right_forearm_circ_cm' => [
            'label' => 'labelRightForearmCirc',
            'color' => Constants::GRAPH_COLOR_PEACH,
        ],
        'left_arm_circ_cm' => [
            'label' => 'labelLeftArm',
            'color' => Constants::GRAPH_COLOR_MINT,
        ],
        'left_forearm_circ_cm' => [
            'label' => 'labelLeftForearmCirc',
            'color' => Constants::GRAPH_COLOR_LIGHT_BLUE,
        ],
    ];

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationUpperLimbsGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $avaliation = $this->getAvaliation();
        $data = $this->initChartData();
        $arrPoints = [];

        $dateFormat = strtolower(__('messages.dateFormat'));
        $prevAvaliations = $this->getPreviousAvaliations(9);

        foreach ($prevAvaliations as $av) {
            $formattedDate = SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat);
            $this->appendChartPoint($data, $formattedDate, $av, $arrPoints);
        }

        // Current
        $formattedDate = SysUtils::reformatDate($avaliation->date, 'Y-m-d', $dateFormat);
        $this->appendChartPoint($data, $formattedDate, $avaliation, $arrPoints);

        $stepSize = min(max(($max = max($arrPoints)) - ($min = min($arrPoints)) / 5, 1), 5);

        return [
            'type' => 'line',
            'data' => $data,
            'options' => [
                'responsive' => true,
                'title' => ['display' => false],
                'legend' => ['display' => false, 'position' => 'top'],
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'stepSize' => $stepSize,
                            'suggestedMin' => $min - $stepSize,
                            'suggestedMax' => $max + $stepSize,
                            'padding' => 2.5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => strtoupper(self::PREFIX),
                        ],
                        'stacked' => false,
                    ]],
                    'xAxes' => [[
                        'ticks' => ['padding' => 2.5],
                        'scaleLabel' => ['display' => false],
                        'stacked' => false,
                    ]]
                ],
            ]
        ];
    }

    private function appendChartPoint(array &$data, string $label, Avaliation $avaliation, array &$arrPoints): void
    {
        $data['labels'][] = $label;
        $row = [$label];

        $i = 0;
        foreach (array_keys(self::SEGMENTS) as $segment) {
            $value = $avaliation->{$segment};
            $data['datasets'][$i++]['data'][] = $value;
            $arrPoints[] = $value;

            $row[] = SysUtils::formatDbToNumber($value, 1) . self::PREFIX;
        }

        $this->addBodyItem(...$row);
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));

        foreach (self::SEGMENTS as $segment) {
            $color = $segment['color'];
            $label = __('messages.pages.avaliation.modalAddAvaliation.' . $segment['label']);

            $header = $this->getTableRowLabel(
                $label,
                $color
            );
            $this->addHeadItem($header);
        }
    }

    private function initChartData(): array
    {
        $datasets = [];
        $order = 1;

        foreach (self::SEGMENTS as $segment) {
            $datasets[] = [
                'label' => __('messages.pages.avaliation.modalAddAvaliation.' . $segment['label']),
                'data' => [],
                'backgroundColor' => 'transparent',
                'borderColor' => $segment['color'],
                'order' => $order++,
            ];
        }

        return [
            'labels' => [],
            'datasets' => $datasets,
        ];
    }
}
