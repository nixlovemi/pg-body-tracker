<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Tanita Health Management & Omron Healthcare
 */
class VisceralFat extends AvaliationFieldInfoAbstract
{
    public function defineIdealValues(): void
    {
        $this->setManIdealValues($this->getManRanking()[0], $this->getManRanking()[0]);
        $this->setWomanIdealValues($this->getWomanRanking()[0], $this->getWomanRanking()[0]);
    }

    public function getIdealLabel(): string
    {
        return '< '
            . \App\Helpers\SysUtils::formatDbToNumber($this->getMinIdealValue(), 1) . $this->getFieldSuffix();
    }

    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getVisceralFatKg();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedVisceralFatKg();
    }

    public function getManRanking(): array
    {
        // Healthy, moderate e high
        return [2.0, 3.5, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [1.8, 3.0, PHP_INT_MAX];
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
        return [
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_4,
            Constants::RANK_COLOR_6,
        ];
    }
}
