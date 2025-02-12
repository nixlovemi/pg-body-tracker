<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Based on Omron
 */
class Weight extends AvaliationFieldInfoAbstract
{
    public function defineIdealValues(): void
    {
        // based on ideal bmi (18.5 a 24.9)
        $idealWeightMin = 18.5 * (($this->Avaliation->height_cm / 100) ** 2);
        $idealWeightMax = 24.9 * (($this->Avaliation->height_cm / 100) ** 2);

        $this->setManIdealValues($idealWeightMin, $idealWeightMax);
        $this->setWomanIdealValues($idealWeightMin, $idealWeightMax);
    }

    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->weight_kg;
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedWeight();
    }

    public function getManRanking(): array
    {
        // BMI based
        return [18.5, 24.9, 29.9, 34.9, 39.9, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return $this->getManRanking();
    }

    public function getRankingLabels(): array
    {
        return  [
            __('messages.components.avaliationReport.weightLabel1'),
            __('messages.components.avaliationReport.bmiLabel4'),
            __('messages.components.avaliationReport.bmiLabel5'),
            __('messages.components.avaliationReport.bmiLabel6'),
            __('messages.components.avaliationReport.bmiLabel7'),
            __('messages.components.avaliationReport.bmiLabel8'),
        ];
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings([0, 1, 3, 5, 6, 7]), 'color');
    }

    public function getRankNbr(): int
    {
        $rank = -1;
        foreach ($this->getRankingForLoop() as $key => $maxValue) {
            if ($this->Avaliation->getBmi() < $maxValue) {
                $rank = $key + 1;
                break;
            }
        }

        return $rank;
    }
}
