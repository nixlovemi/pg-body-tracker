<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Wang et al. (1999) – "Body composition: methods and applications"
 */
class BodyWater extends AvaliationFieldInfoAbstract
{
    private const ARR_RANKING_IDX = [0, 1, 6];

    public function getFieldSuffix(): string
    {
        return '%';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getBodyWaterPerc();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedBodyWaterPerc()
            . ' / ' . $this->Avaliation->getFormattedBodyWaterKg();
    }

    public function getManRanking(): array
    {
        // low, normal e high
        return [50, 65, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        // low, normal e high
        return [45, 60, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        return array_column(Constants::getRankings(self::ARR_RANKING_IDX, true), 'label');
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings(self::ARR_RANKING_IDX), 'color');
    }
}
