<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;
use App\Helpers\SysUtils;

/**
 * Waist + Abdomen circumference card.
 * Risk classification is based on waist circumference cutoffs (WHO/IDF).
 */
class WaistAbdomenCircumference extends AvaliationFieldInfoAbstract
{
    public function defineIdealValues(): void
    {
        $this->setManIdealValues(0, $this->getManRanking()[0]);
        $this->setWomanIdealValues(0, $this->getWomanRanking()[0]);
    }

    protected function getIdealLabel(): string
    {
        return '< ' . SysUtils::formatDbToNumber($this->getMaxIdealValue(), 1) . $this->getFieldSuffix()
            . ' (' . __('messages.models.Avaliation.fields.waist_circ_cm') . ')';
    }

    public function getFieldSuffix(): string
    {
        return 'cm';
    }

    public function getFieldValue(): float|int
    {
        $waist = $this->Avaliation->waist_circ_cm;
        $abdomen = $this->Avaliation->abdomen_circ_cm;

        if ($waist === null && $abdomen === null) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        // Risk references are established for waist circumference.
        // Abdomen circumference is shown as complementary context in the card.
        if ($waist !== null) {
            return (float) $waist;
        }

        return (float) $abdomen;
    }

    public function getFieldLabel(): string
    {
        $waist = $this->Avaliation->waist_circ_cm;
        $abdomen = $this->Avaliation->abdomen_circ_cm;

        $waistLabel = $waist === null
            ? '-'
            : SysUtils::formatDbToNumber($waist, 1) . $this->getFieldSuffix();

        $abdomenLabel = $abdomen === null
            ? '-'
            : SysUtils::formatDbToNumber($abdomen, 1) . $this->getFieldSuffix();

        return __('messages.models.Avaliation.fields.waist_circ_cm') . ': ' . $waistLabel
            . ' / ' . __('messages.models.Avaliation.fields.abdomen_circ_cm') . ': ' . $abdomenLabel;
    }

    public function getManRanking(): array
    {
        // WHO/IDF waist circumference cutoffs (men): <94 low, 94-101.9 moderate, >=102 high.
        return [94, 102, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        // WHO/IDF waist circumference cutoffs (women): <80 low, 80-87.9 moderate, >=88 high.
        return [80, 88, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.VisceralFat1'),
            __('messages.components.avaliationReport.VisceralFat2'),
            __('messages.components.avaliationReport.VisceralFat3'),
        ];
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings([1, 4, 7]), 'color');
    }
}
