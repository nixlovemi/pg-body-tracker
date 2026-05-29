<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Models\Goal;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;

class WeightVariability7dSignal extends AbstractSignal
{
    public function key(): string
    {
        return 'weight_variability_7d';
    }

    public function label(): string
    {
        return $this->t('label');
    }

    public function evaluate(SignalContext $context): ?SignalResult
    {
        $avaliations = $context->avaliationsWithinDays(7);
        if ($avaliations->count() < 3) {
            return null;
        }

        $weights = $avaliations->pluck('weight_kg')->map(function ($value) {
            return (float) $value;
        })->all();

        $mean = count($weights) > 0 ? (array_sum($weights) / count($weights)) : 0.0;
        if ($mean <= 0.0) {
            return null;
        }

        $stddev = $this->stddev($weights);
        $variabilityPct = ($stddev / $mean) * 100;

        $attentionThreshold = (float) $this->cfg('attention_percent', 0.5);
        $riskThreshold = (float) $this->cfg('risk_percent', 1.0);

        if ($variabilityPct <= $attentionThreshold) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good'), round($variabilityPct, 2), ['variability_percent' => round($variabilityPct, 2)]);
        }

        if ($variabilityPct <= $riskThreshold) {
            if ($this->isModerateVariabilityAlignedWithGoal($context)) {
                return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good_aligned'), round($variabilityPct, 2), [
                    'variability_percent' => round($variabilityPct, 2),
                    'goal_aligned' => true,
                ]);
            }

            return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention'), round($variabilityPct, 2), ['variability_percent' => round($variabilityPct, 2)]);
        }

        return $this->result(PatientSignalLevel::RISK, 2, $this->t('messages.risk'), round($variabilityPct, 2), ['variability_percent' => round($variabilityPct, 2)]);
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    protected function indicatorText($value, array $meta): string
    {
        $variability = $meta['variability_percent'] ?? null;
        if (is_numeric($variability)) {
            return $this->t('indicator.variability_percent', [
                'percent' => $this->formatNumber((float) $variability, 2),
            ]);
        }

        return parent::indicatorText($value, $meta);
    }

    private function isModerateVariabilityAlignedWithGoal(SignalContext $context): bool
    {
        $goal = $context->currentGoal();
        if (!$goal) {
            return false;
        }

        $objective = (string) $goal->objective;
        if ($objective !== Goal::OBJECTIVE_WEIGHT_LOSS && $objective !== Goal::OBJECTIVE_MUSCLE_GAIN) {
            return false;
        }

        $window = $context->avaliationsWithinDays(7);
        if ($window->count() < 2) {
            return false;
        }

        $firstWeight = (float) ($window->first()->weight_kg ?? 0.0);
        $lastWeight = (float) ($window->last()->weight_kg ?? 0.0);
        if ($firstWeight <= 0.0 || $lastWeight <= 0.0) {
            return false;
        }

        $deltaPercent = (($lastWeight - $firstWeight) / $firstWeight) * 100;
        $noiseMargin = (float) $this->cfg('trend_alignment_noise_percent', 0.15);

        if (abs($deltaPercent) < $noiseMargin) {
            return false;
        }

        if ($objective === Goal::OBJECTIVE_WEIGHT_LOSS) {
            return $deltaPercent < 0;
        }

        return $deltaPercent > 0;
    }
}
