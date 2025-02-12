<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Omron, Tanita, InBody & Renpho, Withings, Garmin
 */
class BodyAge extends AvaliationFieldInfoAbstract
{
    public function getFieldSuffix(): string
    {
        return __('messages.components.avaliationReport.years');
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getBodyAge();
    }

    public function getFieldLabel(): string
    {
        $ageDiff = $this->getFieldValue() - $this->Avaliation->age;
        $prefix = $ageDiff > 0 ? '+' : '';

        return $this->Avaliation->getFormattedBodyAge()
            . ' / ' . $prefix . ($this->getFieldValue() - $this->Avaliation->age)
            . ' ' . $this->getFieldSuffix();
    }

    public function getManRanking(): array
    {
        // based on diff btw bodyAge and age
        return [-5, -2, 1, 4, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return $this->getManRanking();
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.bodyAgeLabel1'),
            __('messages.components.avaliationReport.bodyAgeLabel2'),
            __('messages.components.avaliationReport.bodyAgeLabel3'),
            __('messages.components.avaliationReport.bodyAgeLabel4'),
            __('messages.components.avaliationReport.bodyAgeLabel5'),
        ];
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings([0, 1, 2, 5, 7], true), 'color');
    }

    public function getRankNbr(): int
    {
        $skRank = -1;
        foreach ($this->getRankingForLoop() as $rank => $maxValue) {
            if (($this->getFieldValue() - $this->Avaliation->age) <= $maxValue) {
                $skRank = $rank + 1;
                break;
            }
        }

        return $skRank;
    }
}
