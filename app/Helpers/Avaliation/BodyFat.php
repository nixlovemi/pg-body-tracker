<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * World Health Organization (WHO)
 */
class BodyFat extends AvaliationFieldInfoAbstract
{
    public function getFieldSuffix(): string
    {
        return '%';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getBodyFatPerc();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedBodyFat();
    }

    public function getManRanking(): array
    {
        return [13, 24, 27, 29, 32, 35, 38, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [23, 31, 34, 36, 39, 42, 45, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        return array_column(Constants::getRankings(), 'label');
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings(), 'color');
    }

    public function getRankNbr(): int
    {
        $skRank = -1;
        foreach ($this->getRankingForLoop() as $rank => $maxValue) {
            if (abs($this->getFieldValue()) <= $maxValue) {
                $skRank = $rank + 1;
                break;
            }
        }

        return $skRank;
    }
}
