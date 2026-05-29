<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;

class AvaliationFrequencySignal extends AbstractSignal
{
    public function key(): string
    {
        return 'avaliation_frequency';
    }

    public function label(): string
    {
        return $this->t('label');
    }

    public function evaluate(SignalContext $context): ?SignalResult
    {
        $avgDays = $context->client()->getAvgDaysBtwAvaliations();
        if ($avgDays === null) {
            return $this->result(PatientSignalLevel::INFO, 0, $this->t('messages.info_no_history'), null, []);
        }

        $goodDays = (float) $this->cfg('good_days_max', 21);
        $attentionDays = (float) $this->cfg('attention_days_max', 35);

        if ($avgDays <= $goodDays) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good'), $avgDays, ['avg_days_between_avaliations' => $avgDays]);
        }

        if ($avgDays <= $attentionDays) {
            return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention'), $avgDays, ['avg_days_between_avaliations' => $avgDays]);
        }

        return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk'), $avgDays, ['avg_days_between_avaliations' => $avgDays]);
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    protected function indicatorText($value, array $meta): string
    {
        $avgDays = $meta['avg_days_between_avaliations'] ?? null;
        if (is_numeric($avgDays)) {
            return $this->t('indicator.avg_days', [
                'days' => $this->formatNumber((float) $avgDays, 1),
            ]);
        }

        return parent::indicatorText($value, $meta);
    }
}
