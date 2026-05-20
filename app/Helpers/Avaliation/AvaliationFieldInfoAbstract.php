<?php

namespace App\Helpers\Avaliation;

use App\Models\Avaliation;
use App\Helpers\SysUtils;
use App\Helpers\Constants;

abstract class AvaliationFieldInfoAbstract
{
    private array $arrManIdealValues = [];
    private array $arrWomanIdealValues = [];

    public function __construct(
        public Avaliation $Avaliation)
    {
        $this->defineIdealValues();
        $this->ensureIdealValuesSet();
    }

    /** Like "kg", "kcal", etc */
    abstract public function getFieldSuffix(): string;
    abstract public function getFieldValue(): float|int;
    /** Like "45 kg", "1560 kcal", etc */
    abstract public function getFieldLabel(): string;
    abstract public function getManRanking(): array;
    abstract public function getWomanRanking(): array;
    abstract public function getRankingLabels(): array;
    abstract public function getRankingColors(): array;

    protected function ensureIdealValuesSet(): void
    {
        if (empty($this->arrManIdealValues) || empty($this->arrWomanIdealValues)) {
            throw new \LogicException('Ideal values for both sexes must be set before calling getFieldInfo().');
        }
    }
    protected function getRankingForLoop(): array
    {
        return ($this->Avaliation->client->isMale()) ? $this->getManRanking(): $this->getWomanRanking();
    }
    /** Not IDX. First rank should be 1. */
    protected function getRankNbr(): int
    {
        $skRank = -1;
        foreach ($this->getRankingForLoop() as $rank => $maxValue) {
            if (abs($this->getFieldValue()) < $maxValue) {
                $skRank = $rank + 1;
                break;
            }
        }

        return $skRank;
    }
    protected function getRankIdx(): int
    {
        return $this->getRankNbr() - 1;
    }
    protected function getRankLabel(): string
    {
        $arrRankings = $this->getRankingLabels();
        $skRankIdx = $this->getRankIdx();

        return $arrRankings[$skRankIdx] ?? '';
    }
    protected function getRankColor(): string
    {
        $arrRankings = $this->getRankingColors();
        $skRankIdx = $this->getRankIdx();

        return $arrRankings[$skRankIdx] ?? Constants::RANK_COLOR_DEFAULT;
    }
    protected function defineIdealValues(): void
    {
        if (empty($this->getManRanking()) || empty($this->getWomanRanking())) {
            throw new \LogicException('Both array rankings must be set before calling defineIdealValues().');
        }

        $this->setManIdealValues($this->getManRanking()[0], $this->getManRanking()[1]);
        $this->setWomanIdealValues($this->getWomanRanking()[0], $this->getWomanRanking()[1]);
    }
    protected function getIdealLabel(): string
    {
        return SysUtils::formatDbToNumber($this->getMinIdealValue(), 1) . $this->getFieldSuffix()
            . ' - ' . SysUtils::formatDbToNumber($this->getMaxIdealValue(), 1) . $this->getFieldSuffix();
    }
    public function getMinIdealValue(): float
    {
        return $this->Avaliation->client->isMale() ? $this->getManIdealMinValue() : $this->getWomanIdealMinValue();
    }
    public function getMaxIdealValue(): ?float
    {
        return $this->Avaliation->client->isMale() ? $this->getManIdealMaxValue() : $this->getWomanIdealMaxValue();
    }

    protected function setManIdealValues(string $min, ?string $max): void
    {
        $this->arrManIdealValues = [
            'min' => $min,
            'max' => $max
        ];
    }
    protected function getManIdealMinValue(): string
    {
        return $this->arrManIdealValues['min'];
    }
    protected function getManIdealMaxValue(): ?string
    {
        return $this->arrManIdealValues['max'];
    }
    protected function setWomanIdealValues(string $min, ?string $max): void
    {
        $this->arrWomanIdealValues = [
            'min' => $min,
            'max' => $max
        ];
    }
    protected function getWomanIdealMinValue(): string
    {
        return $this->arrWomanIdealValues['min'];
    }
    protected function getWomanIdealMaxValue(): ?string
    {
        return $this->arrWomanIdealValues['max'];
    }

    public function getFieldInfo(): array
    {
        $fieldLabel = $this->getFieldLabel();
        $fieldValue = $this->getFieldValue();
        $rankLabel = $this->getRankLabel();
        $rankColor = $this->getRankColor();
        $idealLabel = $this->getIdealLabel();

        if ($fieldValue == Constants::RETURN_INT_CANT_CALCULATE) {
            $fieldLabel = __('messages.components.avaliationReport.notCalculated');
            $fieldValue = null;
            $rankLabel = '';
            $idealLabel = '';
            $rankColor = Constants::RANK_COLOR_DEFAULT;
        }

        return [
            Constants::FI_FIELD_LABEL => $fieldLabel,
            Constants::FI_FIELD_VALUE => $fieldValue,
            Constants::FI_FIELD_SUFFIX => $this->getFieldSuffix(),
            Constants::FI_RANK => $this->getRankNbr(),
            Constants::FI_RANK_IDX => $this->getRankIdx(),
            Constants::FI_RANK_LABEL => $rankLabel,
            Constants::FI_RANK_COLOR => $rankColor,
            Constants::FI_IDEAL_MIN => SysUtils::formatDbToNumber($this->getMinIdealValue(), 1) . $this->getFieldSuffix(),
            Constants::FI_IDEAL_MAX => SysUtils::formatDbToNumber($this->getMaxIdealValue(), 1) . $this->getFieldSuffix(),
            Constants::FI_IDEAL_LABEL => $idealLabel,
        ];
    }
}
