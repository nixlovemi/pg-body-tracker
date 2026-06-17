<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Fat Mass (Massa Gorda) - Calculated from body weight and body fat percentage
 * Used by nutritionists to monitor fat loss in absolute values (kg)
 */
class FatMass extends AvaliationFieldInfoAbstract
{
    private const ARR_RANKING_IDX = [0, 1, 3, 5, 7];

    public function defineIdealValues(): void
    {
        // Ideal body fat percentage ranges (WHO/CDC standards)
        // Ideal fat mass calculated from ideal % body fat
        $bodyFatPerc = $this->Avaliation->getBodyFatPerc();
        $weight = $this->Avaliation->weight_kg;

        // For men: 10-20% is healthy, women: 18-25% is healthy
        // Using midpoint for ideal: men 15%, women 21.5%
        if ($this->Avaliation->client->isMale()) {
            $idealFatMin = ($weight * 0.10); // 10% body fat
            $idealFatMax = ($weight * 0.20); // 20% body fat
        } else {
            $idealFatMin = ($weight * 0.18); // 18% body fat
            $idealFatMax = ($weight * 0.25); // 25% body fat
        }

        $this->setManIdealValues($idealFatMin, $idealFatMax);
        $this->setWomanIdealValues($idealFatMin, $idealFatMax);
    }

    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getFatMassKg();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedFatMass();
    }

    public function getManRanking(): array
    {
        // Approximate ranges in kg based on weight and body fat %
        // These are loose estimates; ideal values set in defineIdealValues()
        return [8, 15, 20, 25, 30, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [12, 18, 23, 28, 33, PHP_INT_MAX];
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
        $rank = -1;
        foreach ($this->getRankingForLoop() as $key => $maxValue) {
            if ($this->Avaliation->getFatMassKg() < $maxValue) {
                $rank = $key + 1;
                break;
            }
        }

        return $rank;
    }
}
