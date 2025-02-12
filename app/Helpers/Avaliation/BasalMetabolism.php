<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Harris-Benedict Method
 */
class BasalMetabolism extends AvaliationFieldInfoAbstract
{
    public function getFieldSuffix(): string
    {
        return 'kcal';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getTmb();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedTmb();
    }

    public function getManRanking(): array
    {
        // low, normal e high
        $age = $this->Avaliation->age;
        if (abs($age) < 29) {
            return [1600, 1900, PHP_INT_MAX];
        }

        if (abs($age) < 39) {
            return [1500, 1800, PHP_INT_MAX];
        }

        if (abs($age) < 49) {
            return [1450, 1750, PHP_INT_MAX];
        }

        if (abs($age) < 59) {
            return [1400, 1700, PHP_INT_MAX];
        }

        return [1300, 1600, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        // low, normal e high
        $age = $this->Avaliation->age;
        if (abs($age) < 29) {
            return [1350, 1550, PHP_INT_MAX];
        }

        if (abs($age) < 39) {
            return [1300, 1500, PHP_INT_MAX];
        }

        if (abs($age) < 49) {
            return [1250, 1450, PHP_INT_MAX];
        }

        if (abs($age) < 59) {
            return [1200, 1400, PHP_INT_MAX];
        }

        return [1150, 1350, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.rankBarLabel1'),
            __('messages.components.avaliationReport.rankBarLabel2'),
            __('messages.components.avaliationReport.VisceralFat3'),
        ];
    }

    public function getRankingColors(): array
    {
        return [
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_6,
        ];
    }
}
