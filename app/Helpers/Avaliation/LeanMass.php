<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Lean Mass (Massa Magra) - Fat-Free Mass in kg
 * Essential for calculating protein requirements (1.6-2.2g per kg of lean mass)
 * Used by nutritionists to monitor muscle gain/preservation
 */
class LeanMass extends AvaliationFieldInfoAbstract
{
    public function defineIdealValues(): void
    {
        // Ideal lean mass is weight - (ideal body fat %)
        // For men: 15% ideal fat = 85% lean, women: 21.5% ideal fat = 78.5% lean
        $weight = $this->Avaliation->weight_kg;

        if ($this->Avaliation->client->isMale()) {
            // Men: healthy BMI + 15% body fat = ~85% lean mass
            $idealLeanMin = $weight * 0.80;
            $idealLeanMax = $weight * 0.90;
        } else {
            // Women: healthy BMI + 21.5% body fat = ~78.5% lean mass
            $idealLeanMin = $weight * 0.75;
            $idealLeanMax = $weight * 0.85;
        }

        $this->setManIdealValues($idealLeanMin, $idealLeanMax);
        $this->setWomanIdealValues($idealLeanMin, $idealLeanMax);
    }

    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getLeanMassKg();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedLeanMass();
    }

    public function getManRanking(): array
    {
        return [$this->getMinIdealValue(), $this->getMaxIdealValue(), PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return $this->getManRanking();
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.leanMassBelow'),
            __('messages.components.avaliationReport.leanMassWithin'),
            __('messages.components.avaliationReport.leanMassAbove'),
        ];
    }

    public function getRankingColors(): array
    {
        return [
            Constants::RANK_COLOR_4,
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_6,
        ];
    }

    public function getRankNbr(): int
    {
        $value = $this->getFieldValue();

        if ($value < $this->getMinIdealValue()) {
            return 1;
        }

        if ($value <= $this->getMaxIdealValue()) {
            return 2;
        }

        return 3;
    }
}
