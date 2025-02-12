<?php

namespace App\Helpers\Avaliation;

use App\Helpers\Constants;

class FatFreeMassIndex extends AvaliationFieldInfoAbstract
{
    protected function defineIdealValues(): void
    {
        $this->setManIdealValues($this->getManRanking()[0], $this->getManRanking()[0]);
        $this->setWomanIdealValues($this->getWomanRanking()[0], $this->getWomanRanking()[0]);
    }

    protected function getIdealLabel(): string
    {
        return '> ' . \App\Helpers\SysUtils::formatDbToNumber($this->getMinIdealValue(), 2);
    }

    public function getFieldSuffix(): string
    {
        return '';
    }

    public function getFieldValue(): float|int
    {
        return $this->Avaliation->getFFMI();
    }

    public function getFieldLabel(): string
    {
        return $this->Avaliation->getFormattedFFMI();
    }

    public function getManRanking(): array
    {
        return [17, 18.9, 20.9, 22.9, PHP_INT_MAX];
    }

    public function getWomanRanking(): array
    {
        return [14, 15.9, 17.9, 20, PHP_INT_MAX];
    }

    public function getRankingLabels(): array
    {
        // below avg, avg, above avg, excelent, very high
        return [
            __('messages.components.avaliationReport.FFMILabel1'),
            __('messages.components.avaliationReport.FFMILabel2'),
            __('messages.components.avaliationReport.FFMILabel3'),
            __('messages.components.avaliationReport.FFMILabel4'),
            __('messages.components.avaliationReport.FFMILabel5'),
        ];
    }

    public function getRankingColors(): array
    {
        // inverse color bcause of the way the ranking is done
        return [
            Constants::RANK_COLOR_7,
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_1,
            Constants::RANK_COLOR_2,
            Constants::RANK_COLOR_2,
        ];
    }
}
