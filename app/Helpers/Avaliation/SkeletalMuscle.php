<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Based on Omron & Janssen et al., 2000
 */
class SkeletalMuscle extends AvaliationFieldInfoAbstract
{
    private CONST ARR_RANKING_IDX = [0, 1, 5, 6, 7];

    public function getFieldSuffix(): string
    {
        return '%';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getSkeletalMuscleMassPerc();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedSkeletalMuscleMassPerc()
            . ' / ' . $this->Avaliation->getFormattedSkeletalMuscleMassKg();
    }

    public function getManRanking(): array
    {
        return [33, 39, 44, 50, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [26, 32, 36, 41, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        // low, normal, high 1, high 2, high 3
        return array_column(Constants::getRankings(self::ARR_RANKING_IDX), 'label');
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings(self::ARR_RANKING_IDX), 'color');
    }
}
