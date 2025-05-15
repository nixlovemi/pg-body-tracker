<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Heyward & Wagner (2004) & Tanita Health Management & Omron Healthcare
 */
class BoneMass extends AvaliationFieldInfoAbstract
{
    private const ARR_RANKING_IDX = [0, 1, 6];

    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getBoneMassKg();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedBoneMassKg();
    }

    public function getManRanking(): array
    {
        // low, normal e high
        $weight = $this->Avaliation->weight_kg;
        if (abs($weight) < 60) {
            return [2.65, 2.95, PHP_INT_MAX];
        }

        if (abs($weight) < 75) {
            return [2.9, 3.2, PHP_INT_MAX];
        }

        return [3.2, 3.6, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        // low, normal e high
        $weight = $this->Avaliation->weight_kg;
        if (abs($weight) < 45) {
            return [1.95, 2.4, PHP_INT_MAX];
        }

        if (abs($weight) < 60) {
            return [2.1, 2.6, PHP_INT_MAX];
        }

        return [2.4, 3, PHP_INT_MAX];
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
