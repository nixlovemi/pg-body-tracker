<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;

class CheckinResponseRate30dSignal extends AbstractSignal
{
    public function key(): string
    {
        return 'checkin_response_rate_30d';
    }

    public function label(): string
    {
        return $this->t('label');
    }

    public function isPremium(): bool
    {
        return true;
    }

    public function evaluate(SignalContext $context): ?SignalResult
    {
        $checkinConfig = $context->checkinConfig();
        if (!$checkinConfig || !$checkinConfig->active) {
            return null;
        }

        $intervalDays = max(1, (int) $checkinConfig->interval_days);
        $expectedCheckins = max(1, (int) floor(30 / $intervalDays));
        $responses = $context->checkinResponsesWithinDays(30);

        $daysSinceLastSent = $this->daysBetween($context, $checkinConfig->last_checkin_sent_date);
        $daysSinceConfigCreated = $this->daysBetween($context, $checkinConfig->created_at);

        // If there is already a response in the current cycle window, avoid
        // diluting adherence against a full 30-day expectation.
        if ($responses > 0) {
            if (!is_null($daysSinceLastSent) && $daysSinceLastSent < $intervalDays) {
                $expectedCheckins = max(1, $responses);
            } elseif (is_null($daysSinceLastSent) && !is_null($daysSinceConfigCreated) && $daysSinceConfigCreated < $intervalDays) {
                $expectedCheckins = max(1, $responses);
            }
        }

        $rate = min(100.0, round(($responses / $expectedCheckins) * 100, 2));
        $good = (float) $this->cfg('good_percent', 80);
        $attention = (float) $this->cfg('attention_percent', 50);

        if ($rate >= $good) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good'), $rate, [
                'rate_percent' => $rate,
                'responses' => $responses,
                'expected' => $expectedCheckins,
                'days_since_last_sent' => $daysSinceLastSent,
            ]);
        }

        if ($rate >= $attention) {
            return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention'), $rate, [
                'rate_percent' => $rate,
                'responses' => $responses,
                'expected' => $expectedCheckins,
                'days_since_last_sent' => $daysSinceLastSent,
            ]);
        }

        return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk'), $rate, [
            'rate_percent' => $rate,
            'responses' => $responses,
            'expected' => $expectedCheckins,
            'days_since_last_sent' => $daysSinceLastSent,
        ]);
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    protected function indicatorText($value, array $meta): string
    {
        $ratePercent = $meta['rate_percent'] ?? null;
        $responses = $meta['responses'] ?? null;
        $expected = $meta['expected'] ?? null;

        if (is_numeric($ratePercent) && is_numeric($responses) && is_numeric($expected)) {
            return $this->t('indicator.rate_percent', [
                'percent' => $this->formatNumber((float) $ratePercent, 1),
                'responses' => (int) $responses,
                'expected' => (int) $expected,
            ]);
        }

        return parent::indicatorText($value, $meta);
    }
}
