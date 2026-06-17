<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Trunk Fat Percentage card.
 * Segmented body fat analysis for trunk region.
 */
class TrunkFatPercentage extends AvaliationFieldInfoAbstract
{
    private const ARR_RANKING_IDX = [0, 2, 4, 6];

    public function defineIdealValues(): void
    {
        // Trunk fat: 10-20% is generally ideal for both sexes
        $this->setManIdealValues(10, 20);
        $this->setWomanIdealValues(15, 25);
    }

    public function getFieldSuffix(): string
    {
        return '%';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getTrunkFatPerc();
    }

    public function getFieldLabel(): string
    {
        $value = $this->getFieldValue();
        if ($value === Constants::RETURN_INT_CANT_CALCULATE) {
            return '-';
        }
        return \App\Helpers\SysUtils::formatDbToNumber($value, 1) . $this->getFieldSuffix();
    }

    protected function getIdealLabel(): string
    {
        return $this->getMinIdealValue() . ' - ' . $this->getMaxIdealValue() . $this->getFieldSuffix();
    }

    public function getManRanking(): array
    {
        // Trunk fat ranges: <15% low, 15-22% ideal, 22-30% moderate, >30% high
        return [15, 22, 30, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        // Women higher ranges: <20% low, 20-28% ideal, 28-36% moderate, >36% high
        return [20, 28, 36, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        return array_column(Constants::getRankings(self::ARR_RANKING_IDX), 'label');
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings(self::ARR_RANKING_IDX), 'color');
    }

    public function getRankNbr(): int
    {
        $fieldValue = $this->getFieldValue();
        if ($fieldValue == Constants::RETURN_INT_CANT_CALCULATE) {
            return -1;
        }

        $rank = -1;
        foreach ($this->getRankingForLoop() as $key => $maxValue) {
            if ($fieldValue < $maxValue) {
                $rank = $key + 1;
                break;
            }
        }

        return $rank;
    }
}
