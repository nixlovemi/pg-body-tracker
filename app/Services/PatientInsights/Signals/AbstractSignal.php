<?php

namespace App\Services\PatientInsights\Signals;

use App\Services\PatientInsights\SignalContract;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;
use Carbon\Carbon;

abstract class AbstractSignal implements SignalContract
{
    public function isPremium(): bool
    {
        return false;
    }

    protected function maxRiskPoints(): int
    {
        return 3;
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    protected function result(
        string $level,
        int $riskPoints,
        string $message,
        $value = null,
        array $meta = []
    ): SignalResult {
        $meta = $this->enrichMetaWithIndicatorText($value, $meta);

        return new SignalResult(
            $this->key(),
            $this->label(),
            $level,
            max(0, min($this->maxRiskPoints(), $riskPoints)),
            $this->maxRiskPoints(),
            $this->isPremium(),
            $message,
            $value,
            $meta
        );
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     * @return array<string, mixed>
     */
    private function enrichMetaWithIndicatorText($value, array $meta): array
    {
        if (isset($meta['indicator_text']) && is_string($meta['indicator_text']) && trim($meta['indicator_text']) !== '') {
            return $meta;
        }

        $indicatorText = $this->indicatorText($value, $meta);
        if ($indicatorText !== '') {
            $meta['indicator_text'] = $indicatorText;
        }

        return $meta;
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    protected function indicatorText($value, array $meta): string
    {
        if (is_numeric($value)) {
            return (string) __('messages.services.patientInsights.indicator.generic', [
                'value' => $this->formatNumber((float) $value, 2),
            ]);
        }

        return '';
    }

    protected function formatNumber(float $value, int $decimals = 1): string
    {
        return number_format(
            $value,
            $decimals,
            (string) __('messages.decimalSeparator'),
            (string) __('messages.thousandSeparator')
        );
    }

    protected function cfg(string $key, $default = null)
    {
        $signalCfg = (array) config('patient_insights.thresholds.' . $this->key(), []);
        return array_key_exists($key, $signalCfg) ? $signalCfg[$key] : $default;
    }

    /**
     * @param array<string, mixed> $replace
     */
    protected function t(string $relativeKey, array $replace = []): string
    {
        return (string) __('messages.services.patientInsights.signals.' . $this->key() . '.' . $relativeKey, $replace);
    }

    /**
     * @param \DateTimeInterface|string|null $dateValue
     */
    protected function daysBetween(SignalContext $context, $dateValue): ?int
    {
        if (!$dateValue) {
            return null;
        }

        $date = method_exists($dateValue, 'copy')
            ? $dateValue->copy()
            : Carbon::parse((string) $dateValue);

        return $date->startOfDay()->diffInDays($context->now()->copy()->startOfDay());
    }

    /**
     * @param array<int, float> $values
     */
    protected function stddev(array $values): float
    {
        $count = count($values);
        if ($count <= 1) {
            return 0.0;
        }

        $mean = array_sum($values) / $count;
        $variance = 0.0;
        foreach ($values as $value) {
            $variance += ($value - $mean) * ($value - $mean);
        }

        return sqrt($variance / $count);
    }
}
