<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;
use App\Helpers\SysUtils;

final class AvaliationFatBySegmentGraphHelper extends AvaliationGraphAbstract
{
    public bool $fullHtmlTable = true;
    private const PREFIX = 'kg';

    private const SEGMENTS = [
        'right_arm_fat_kg' => [
            'label' => 'labelRightArm',
            'color' => Constants::GRAPH_COLOR_LIGHT_PINK,
        ],
        'left_arm_fat_kg' => [
            'label' => 'labelLeftArm',
            'color' => Constants::GRAPH_COLOR_PEACH,
        ],
        'trunk_fat_kg' => [
            'label' => 'labelTrunk',
            'color' => Constants::GRAPH_COLOR_MINT,
        ],
        'right_leg_fat_kg' => [
            'label' => 'labelRightLeg',
            'color' => Constants::GRAPH_COLOR_LAVENDER,
        ],
        'left_leg_fat_kg' => [
            'label' => 'labelLeftLeg',
            'color' => Constants::GRAPH_COLOR_LIGHT_BLUE,
        ],
    ];

    public function __construct(private int $avaliationId)
    {
        $this->addTableHeaders();
    }

    public function getClassName(): string
    {
        return 'AvaliationFatBySegmentGraph';
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

        $stepSize = $this->calculateStepSize($arrPoints);

        return [
            'type' => 'bar',
            'data' => $data,
            'options' => [
                'responsive' => true,
                'title' => ['display' => false],
                'legend' => ['display' => false, 'position' => 'top'],
                'scales' => [
                    'yAxes' => [[
                        'ticks' => [
                            'stepSize' => $stepSize,
                            'padding' => 2.5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => strtoupper(self::PREFIX),
                        ],
                        'stacked' => true,
                    ]],
                    'xAxes' => [[
                        'ticks' => ['padding' => 2.5],
                        'scaleLabel' => ['display' => false],
                        'stacked' => true,
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

            $header = sprintf(
                '<a href="javascript:;" class="btn btn-primary btn-circle btn-sm" style="margin-right:4px; width:18px; height:18px; background-color:%s; border-color:%s;">&nbsp;</a>%s',
                $color, $color, $label
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
                'backgroundColor' => $segment['color'],
                'borderColor' => $segment['color'],
                'order' => $order++,
            ];
        }

        return [
            'labels' => [],
            'datasets' => $datasets,
        ];
    }

    private function calculateStepSize(array $points): int
    {
        $min = min($points);
        $max = max($points);
        $range = $max - $min;

        return max(min((int)($range / 5), 15), 1);
    }
}
