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
        return '';
    }

    public function getFieldValue(): float|int
    {
        if ($this->Avaliation->visceral_fat_level === null) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }
        return $this->Avaliation->visceral_fat_level;
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedVisceralFatLevel();
    }

    public function getManRanking(): array
    {
        // low, moderated, high, very high
        return [9, 14, 20, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return $this->getManRanking();
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.VisceralFat1'),
            __('messages.components.avaliationReport.VisceralFat2'),
            __('messages.components.avaliationReport.VisceralFat3'),
            __('messages.components.avaliationReport.VisceralFat4'),
        ];
    }

    public function getRankingColors(): array
    {
        return [
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_4,
            Constants::RANK_COLOR_6,
            Constants::RANK_COLOR_8,
        ];
    }
}
