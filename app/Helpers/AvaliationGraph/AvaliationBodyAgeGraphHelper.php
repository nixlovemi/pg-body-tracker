<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;
use App\Helpers\SysUtils;

final class AvaliationBodyAgeGraphHelper extends AvaliationGraphAbstract
{
    private const PREFIX = '';

    private const SEGMENTS = [
        'age' => [
            'label' => 'messages.models.Avaliation.fields.age',
            'color' => Constants::GRAPH_COLOR_LIGHT_BLUE,
        ],
        'bodyAgeCalc' => [
            'label' => 'messages.models.Avaliation.fields.body_age',
            'color' => Constants::GRAPH_COLOR_LIGHT_PINK,
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
        return 'AvaliationBodyAgeGraph';
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
                            'padding' => 5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                            'labelString' => strtoupper(self::PREFIX),
                        ],
                        'stacked' => false,
                    ]],
                    'xAxes' => [[
                        'ticks' => ['padding' => 5],
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

            $row[] = SysUtils::formatDbToNumber($value, 0) . self::PREFIX;
        }

        $this->addBodyItem(...$row);
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.models.Avaliation.fields.date'));

        foreach (self::SEGMENTS as $segment) {
            $color = $segment['color'];
            $label = __($segment['label']);

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
                'label' => __($segment['label']),
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
