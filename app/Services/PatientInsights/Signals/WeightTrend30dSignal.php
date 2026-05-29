<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Models\Goal;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;

class WeightTrend30dSignal extends AbstractSignal
{
    public function key(): string
    {
        return 'weight_trend_30d';
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
        $avaliations = $context->avaliationsWithinDays(30);
        if ($avaliations->count() < 3) {
            return null;
        }

        $first = (float) $avaliations->first()->weight_kg;
        $last = (float) $avaliations->last()->weight_kg;
        if ($first <= 0.0) {
            return null;
        }

        $pctChange = (($last - $first) / $first) * 100;
        $goal = $context->currentGoal();

        if (!$goal || $goal->objective === Goal::OBJECTIVE_HEALTH) {
            return $this->result(
                PatientSignalLevel::INFO,
                0,
                $this->t('messages.info_no_goal'),
                round($pctChange, 2),
                ['percent_change' => round($pctChange, 2), 'window_days' => 30]
            );
        }

        $delta = (float) $this->cfg('neutral_margin_percent', 0.5);

        if ($goal->objective === Goal::OBJECTIVE_WEIGHT_LOSS) {
            if ($pctChange <= -$delta) {
                return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good_loss'), round($pctChange, 2), ['percent_change' => round($pctChange, 2)]);
            }

            if ($pctChange < $delta) {
                return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention_loss'), round($pctChange, 2), ['percent_change' => round($pctChange, 2)]);
            }

            return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk_loss'), round($pctChange, 2), ['percent_change' => round($pctChange, 2)]);
        }

        if ($pctChange >= $delta) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good_gain'), round($pctChange, 2), ['percent_change' => round($pctChange, 2)]);
        }

        if ($pctChange > -$delta) {
            return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention_gain'), round($pctChange, 2), ['percent_change' => round($pctChange, 2)]);
        }

        return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk_gain'), round($pctChange, 2), ['percent_change' => round($pctChange, 2)]);
    }
}
