<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;
use App\Helpers\SysUtils;

class EstimatedIdealWeight extends AvaliationFieldInfoAbstract
{
    private const BMI_IDEAL_MIN = 18.5;
    private const BMI_IDEAL_MAX = 24.9;
    private const IDEAL_MARGIN_PERCENT = 0.05;

    public function defineIdealValues(): void
    {
        if ($this->Avaliation->height_cm === null) {
            $this->setManIdealValues(0, 0);
            $this->setWomanIdealValues(0, 0);
            return;
        }

        $estimatedIdealWeight = $this->getEstimatedIdealWeight();
        $idealWeightMin = $estimatedIdealWeight * (1 - self::IDEAL_MARGIN_PERCENT);
        $idealWeightMax = $estimatedIdealWeight * (1 + self::IDEAL_MARGIN_PERCENT);

        $this->setManIdealValues($idealWeightMin, $idealWeightMax);
        $this->setWomanIdealValues($idealWeightMin, $idealWeightMax);
    }

    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        if ($this->Avaliation->height_cm === null) {
            return Constants::RETURN_INT_CANT_CALCULATE;
        }

        return $this->getEstimatedIdealWeight();
    }

    public function getFieldLabel(): string
    {
        $value = $this->getFieldValue();
        if ($value === Constants::RETURN_INT_CANT_CALCULATE) {
            return '-';
        }

        return SysUtils::formatDbToNumber($value, 1) . $this->getFieldSuffix();
    }

    public function getManRanking(): array
    {
        return [$this->getMinIdealValue(), $this->getMaxIdealValue(), PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return $this->getManRanking();
    }

    protected function getIdealLabel(): string
    {
        $idealLabel = parent::getIdealLabel();

        if ($this->Avaliation->weight_kg === null) {
            return $idealLabel;
        }

        $currentWeightLabel = SysUtils::formatDbToNumber($this->Avaliation->weight_kg, 1) . $this->getFieldSuffix();
        $currentWeightPrefix = __('messages.components.avaliationReport.currentWeightShort');

        return $idealLabel . ' (' . $currentWeightPrefix . ' ' . $currentWeightLabel . ')';
    }

    public function getRankingLabels(): array
    {
        return [
            __('messages.components.avaliationReport.estimatedIdealWeightBelow'),
            __('messages.components.avaliationReport.estimatedIdealWeightWithin'),
            __('messages.components.avaliationReport.estimatedIdealWeightAbove'),
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
        if ($this->Avaliation->height_cm === null || $this->Avaliation->weight_kg === null) {
            return -1;
        }

        if ($this->Avaliation->weight_kg < $this->getMinIdealValue()) {
            return 1;
        }

        if ($this->Avaliation->weight_kg <= $this->getMaxIdealValue()) {
            return 2;
        }

        return 3;
    }

    private function getEstimatedIdealWeight(): float
    {
        $heightM = $this->Avaliation->height_cm / 100;
        $bmiTarget = (self::BMI_IDEAL_MIN + self::BMI_IDEAL_MAX) / 2;

        return round($bmiTarget * ($heightM ** 2), 2);
    }
}
