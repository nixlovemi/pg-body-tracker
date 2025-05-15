<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

class BodyAdiposityIndex extends AvaliationFieldInfoAbstract
{
    public function getFieldSuffix(): string
    {
        return '%';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getBAI();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedBAI();
    }

    public function getManRanking(): array
    {
        return [8, 21, 26, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [21, 33, 39, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        $obesity = preg_replace('/\d+/', '', __('messages.components.avaliationReport.bmiLabel6'));

        return [
            __('messages.components.avaliationReport.rankBarLabel1'),
            __('messages.components.avaliationReport.rankBarLabel2'),
            __('messages.components.avaliationReport.bmiLabel5'),
            $obesity,
        ];
    }

    public function getRankingColors(): array
    {
        return [
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_5,
            Constants::RANK_COLOR_7,
        ];
    }
}
