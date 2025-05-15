<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * World Health Organization (WHO)
 */
class BodyMassIndex extends AvaliationFieldInfoAbstract
{
    protected function defineIdealValues(): void
    {
        $this->setManIdealValues(18.4, 24.9);
        $this->setWomanIdealValues(18.4, 24.9);
    }

    public function getFieldSuffix(): string
    {
        return 'kg/m²';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getBmi();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedBmi();
    }

    public function getManRanking(): array
    {
        return [16, 16.9, 18.4, 24.9, 29.9, 34.9, 39.9, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return $this->getManRanking();
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.bmiLabel1'),
            __('messages.components.avaliationReport.bmiLabel2'),
            __('messages.components.avaliationReport.bmiLabel3'),
            __('messages.components.avaliationReport.bmiLabel4'),
            __('messages.components.avaliationReport.bmiLabel5'),
            __('messages.components.avaliationReport.bmiLabel6'),
            __('messages.components.avaliationReport.bmiLabel7'),
            __('messages.components.avaliationReport.bmiLabel8'),
        ];
    }

    public function getRankingColors(): array
    {
        return [
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_4,
            Constants::RANK_COLOR_6,
            Constants::RANK_COLOR_7,
            Constants::RANK_COLOR_8,
        ];
    }
}
