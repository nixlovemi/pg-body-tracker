<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

/**
 * Based on WHO
 */
class WaistToHipRatio extends AvaliationFieldInfoAbstract
{
    public function defineIdealValues(): void
    {
        $this->setManIdealValues($this->getManRanking()[0], $this->getManRanking()[0]);
        $this->setWomanIdealValues($this->getWomanRanking()[0], $this->getWomanRanking()[0]);
    }

    protected function getIdealLabel(): string
    {
        return '< ' .  \App\Helpers\SysUtils::formatDbToNumber($this->getMinIdealValue(), 1);
    }

    public function getFieldSuffix(): string
    {
        return '';
    }

    public function getFieldValue(): float
    {
        return $this->Avaliation->getWaistToHipRatio();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedHipToWaistRatio();
    }

    public function getManRanking(): array
    {
        return [0.9, 1.0, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [0.8, 0.85, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        return  [
            __('messages.components.avaliationReport.rankBarLabel1'),
            __('messages.components.avaliationReport.VisceralFat2'),
            __('messages.components.avaliationReport.VisceralFat3'),
        ];
    }

    public function getRankingColors(): array
    {
        return array_column(Constants::getRankings([1, 4, 7]), 'color');
    }
}
