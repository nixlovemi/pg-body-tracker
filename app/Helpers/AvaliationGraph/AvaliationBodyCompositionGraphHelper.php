<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;

final class AvaliationBodyCompositionGraphHelper extends AvaliationGraphAbstract
{
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
        return 'AvaliationBodyCompositionGraph';
    }

    public function getAvaliation(): Avaliation
    {
        return Avaliation::find($this->avaliationId);
    }

    public function getConfig(): array
    {
        $info = $this->getAvaliationInfo();
        $fatMassPercent = $info['fatMass']['percent'];
        $skeletalMassPercent = $info['skeletalMass']['percent'];
        $boneMassPercent = $info['boneMass']['percent'];
        $residualMassPercent = $info['residualMass']['percent'];

        $colorFat = $info['fatMass']['color'];
        $colorSkeletal = $info['skeletalMass']['color'];
        $colorBone = $info['boneMass']['color'];
        $colorResidual = $info['residualMass']['color'];

        $labelFat = $info['fatMass']['label'];
        $labelSkeletal = $info['skeletalMass']['label'];
        $labelBone = $info['boneMass']['label'];
        $labelResidual = $info['residualMass']['label'];

        $config = [
            'type' => 'pie',
            'data' => [
                'labels' => [
                    $labelFat,
                    $labelSkeletal,
                    $labelBone,
                    $labelResidual,
                ],
                'datasets' => [[
                    'data' => [$fatMassPercent, $skeletalMassPercent, $boneMassPercent, $residualMassPercent],
                    'backgroundColor' => [$colorFat, $colorSkeletal, $colorBone, $colorResidual],
                    'borderColor' => [$colorFat, $colorSkeletal, $colorBone, $colorResidual],
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

    private function getAvaliationInfo(): array
    {
        $Avaliation = $this->getAvaliation();

        // values
        $fatMassKg = $Avaliation->getFatMassKg();
        $skeletalMassKg = $Avaliation->getSkeletalMuscleMassKg();
        $boneMassKg = $Avaliation->getBoneMassKg();
        $residualMassKg = $Avaliation->weight_kg - $fatMassKg - $skeletalMassKg - $boneMassKg;

        // formatted values
        $fatMassFormatted = $Avaliation->getFormattedFatMass();
        $skeletalMassFormatted = $Avaliation->getFormattedSkeletalMuscleMassKg();
        $boneMassFormatted = $Avaliation->getFormattedBoneMassKg();
        $residualMassFormatted = number_format($residualMassKg, 2) . 'kg';

        // calculate percentages
        $fatMassPercent = ($fatMassKg / $Avaliation->weight_kg) * 100;
        $skeletalMassPercent = ($skeletalMassKg / $Avaliation->weight_kg) * 100;
        $boneMassPercent = ($boneMassKg / $Avaliation->weight_kg) * 100;
        $residualMassPercent = ($residualMassKg / $Avaliation->weight_kg) * 100;

        // color
        $colorFat = Constants::GRAPH_COLOR_LIGHT_PINK;
        $colorSkeletal = Constants::GRAPH_COLOR_YELLOW;
        $colorBone = Constants::GRAPH_COLOR_LIGHT_BLUE;
        $colorResidual = Constants::GRAPH_COLOR_LIGHT_GREEN;

        return [
            'fatMass' => [
                'formatted' => $fatMassFormatted,
                'kg' => $fatMassKg,
                'percent' => $fatMassPercent,
                'color' => $colorFat,
                'label' => __('messages.models.Avaliation.labelFatMass'),
            ],
            'skeletalMass' => [
                'formatted' => $skeletalMassFormatted,
                'kg' => $skeletalMassKg,
                'percent' => $skeletalMassPercent,
                'color' => $colorSkeletal,
                'label' => __('messages.components.avaliationReport.skeletalMuscle'),
            ],
            'boneMass' => [
                'formatted' => $boneMassFormatted,
                'kg' => $boneMassKg,
                'percent' => $boneMassPercent,
                'color' => $colorBone,
                'label' => __('messages.models.Avaliation.fields.bone_mass_kg'),
            ],
            'residualMass' => [
                'formatted' => $residualMassFormatted,
                'kg' => $residualMassKg,
                'percent' => $residualMassPercent,
                'color' => $colorResidual,
                'label' => __('messages.components.avaliationReport.residualMass'),
            ],
        ];
    }

    private function buildTableBody(): void
    {
        $info = $this->getAvaliationInfo();

        $fatMassFormatted = $info['fatMass']['formatted'];
        $skeletalMassFormatted = $info['skeletalMass']['formatted'];
        $boneMassFormatted = $info['boneMass']['formatted'];
        $residualMassFormatted = $info['residualMass']['formatted'];

        $fatMassLabel = $info['fatMass']['label'];
        $skeletalMassLabel = $info['skeletalMass']['label'];
        $boneMassLabel = $info['boneMass']['label'];
        $residualMassLabel = $info['residualMass']['label'];

        $fatMassPercent = $info['fatMass']['percent'];
        $skeletalMassPercent = $info['skeletalMass']['percent'];
        $boneMassPercent = $info['boneMass']['percent'];
        $residualMassPercent = $info['residualMass']['percent'];

        $fatMassColor = $info['fatMass']['color'];
        $skeletalMassColor = $info['skeletalMass']['color'];
        $boneMassColor = $info['boneMass']['color'];
        $residualMassColor = $info['residualMass']['color'];

        $this->makeTableRow($fatMassLabel, $fatMassFormatted, $fatMassPercent, $fatMassColor);
        $this->makeTableRow($skeletalMassLabel, $skeletalMassFormatted, $skeletalMassPercent, $skeletalMassColor);
        $this->makeTableRow($boneMassLabel, $boneMassFormatted, $boneMassPercent, $boneMassColor);
        $this->makeTableRow($residualMassLabel, $residualMassFormatted, $residualMassPercent, $residualMassColor);
    }

    private function makeTableRow(string $label, string $value, float $percent, string $color): void
    {
        $label = $this->getTableRowLabel(
            $label,
            $color
        );

        $this->addBodyItem($label, $value, number_format($percent, 2) . ' %');
    }
}
