<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;

final class AvaliationFatLeanMassGraphHelper extends AvaliationGraphAbstract
{
    private const COLOR_LEAN = Constants::GRAPH_COLOR_MINT;
    private const COLOR_FAT = Constants::GRAPH_COLOR_LIGHT_PINK;

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) {
        parent::__construct($avaliationId, $isForPdf);
        $this->addTableHeaders();
        $this->buildTableBody();
    }

    public function getClassName(): string
    {
        return 'AvaliationFatLeanMassGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $Avaliation = $this->getAvaliation();
        $leanPercent = ($Avaliation->getLeanMassKg() / ($Avaliation->getLeanMassKg() + $Avaliation->getFatMassKg())) * 100;
        $fatPercent = 100 - $leanPercent;

        $colorLean = self::COLOR_LEAN;
        $colorFat = self::COLOR_FAT;

        $config = [
            'type' => 'pie',
            'data' => [
                'labels' => [
                    __('messages.models.Avaliation.labelFatMass'),
                    __('messages.models.Avaliation.labelLeanMass'),
                ],
                'datasets' => [[
                    'data' => [$fatPercent, $leanPercent],
                    'backgroundColor' => [$colorFat, $colorLean],
                    'borderColor' => [$colorFat, $colorLean],
                    'borderWidth' => 1,
                    'weight' => 2,
                ]]
            ],
            'options' => [
                'plugins' => [
                    'datalabels' => [
                        'display' => false
                    ],
                ],
                'responsive' => true,
                'title' => ['display' => false],
                'legend' => ['display' => false, 'position' => 'top'],
                'tooltips' => [
                    'callbacks' => [
                        'label' => 'FUNC_TOOLTIP_LABEL'
                    ]
                ],
                'scales' => new \stdClass()
            ]
        ];

        return $config;
    }

    private function addTableHeaders(): void
    {
        $this->addHeadItem(__('messages.components.avaliationFatLeanMassGraph.tableColType'));
        $this->addHeadItem(__('messages.components.avaliationFatLeanMassGraph.tableColValue'));
        $this->addHeadItem(__('messages.components.avaliationFatLeanMassGraph.tableColPerc'));
    }

    private function buildTableBody(): void
    {
        $Avaliation = $this->getAvaliation();

        $leanKg = $Avaliation->getLeanMassKg();
        $fatKg = $Avaliation->getFatMassKg();
        $total = $leanKg + $fatKg;

        $leanPercent = ($leanKg / $total) * 100;
        $fatPercent = ($fatKg / $total) * 100;

        $colorLean = self::COLOR_LEAN;
        $colorFat = self::COLOR_FAT;

        $this->makeTableRow('LeanMass', $Avaliation->getFormattedLeanMass(), $leanPercent, $colorLean);
        $this->makeTableRow('FatMass', $Avaliation->getFormattedFatMass(), $fatPercent, $colorFat);
    }

    private function makeTableRow(string $typeKey, string $value, float $percent, string $color): void
    {
        $label = $this->getTableRowLabel(
            __('messages.models.Avaliation.label' . $typeKey),
            $color
        );

        $this->addBodyItem($label, $value, number_format($percent, 2) . ' %');
    }
}
