<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Models\Goal;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;
use Carbon\Carbon;

class GoalProgressPaceSignal extends AbstractSignal
{
    public function key(): string
    {
        return 'goal_progress_pace';
    }

    public function label(): string
    {
        return $this->t('label');
    }

    public function evaluate(SignalContext $context): ?SignalResult
    {
        $goal = $context->currentGoal();
        if (!$goal) {
            return null;
        }

        $goalStart = Carbon::parse($goal->created_at)->startOfDay();
        $goalDeadline = Carbon::parse($goal->deadline)->startOfDay();

        $totalDays = max(1, $goalStart->diffInDays($goalDeadline));
        $elapsedDays = max(0, min($totalDays, $goalStart->diffInDays($context->now()->copy()->startOfDay())));
        $minElapsedDays = max(0, (int) $this->cfg('min_elapsed_days', 7));

        $expectedProgress = round(($elapsedDays / $totalDays) * 100, 2);
        $actualProgress = (float) $goal->percentageTowardsGoal();
        $delta = round($actualProgress - $expectedProgress, 2);

        if ((string) $goal->objective === Goal::OBJECTIVE_HEALTH) {
            return $this->result(PatientSignalLevel::INFO, 0, $this->t('messages.info_health_goal'), $actualProgress, [
                'actual_progress_percent' => $actualProgress,
                'expected_progress_percent' => $expectedProgress,
                'delta_percent' => $delta,
                'objective_health' => true,
            ]);
        }

        if ($elapsedDays < $minElapsedDays) {
            return $this->result(PatientSignalLevel::INFO, 0, $this->t('messages.info_recent_goal'), $actualProgress, [
                'actual_progress_percent' => $actualProgress,
                'expected_progress_percent' => $expectedProgress,
                'delta_percent' => $delta,
                'elapsed_days' => $elapsedDays,
                'min_elapsed_days' => $minElapsedDays,
            ]);
        }

        if ($context->now()->copy()->startOfDay()->gt($goalDeadline) && $actualProgress < 100.0) {
            return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk_deadline_passed'), $actualProgress, [
                'actual_progress_percent' => $actualProgress,
                'expected_progress_percent' => $expectedProgress,
                'delta_percent' => $delta,
            ]);
        }

        $goodDelta = (float) $this->cfg('good_delta_percent', -10);
        $attentionDelta = (float) $this->cfg('attention_delta_percent', -25);

        if ($delta >= $goodDelta) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good'), $actualProgress, [
                'actual_progress_percent' => $actualProgress,
                'expected_progress_percent' => $expectedProgress,
                'delta_percent' => $delta,
            ]);
        }

        if ($delta >= $attentionDelta) {
            return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention'), $actualProgress, [
                'actual_progress_percent' => $actualProgress,
                'expected_progress_percent' => $expectedProgress,
                'delta_percent' => $delta,
            ]);
        }

        return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk'), $actualProgress, [
            'actual_progress_percent' => $actualProgress,
            'expected_progress_percent' => $expectedProgress,
            'delta_percent' => $delta,
        ]);
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    protected function indicatorText($value, array $meta): string
    {
        $actual = $meta['actual_progress_percent'] ?? null;
        $expected = $meta['expected_progress_percent'] ?? null;
        $delta = $meta['delta_percent'] ?? null;
        $elapsedDays = $meta['elapsed_days'] ?? null;
        $minElapsedDays = $meta['min_elapsed_days'] ?? null;
        $objectiveHealth = $meta['objective_health'] ?? null;

        if ($objectiveHealth === true) {
            return $this->t('indicator.health_goal');
        }

        if (is_numeric($elapsedDays) && is_numeric($minElapsedDays)) {
            return $this->t('indicator.recent_goal', [
                'elapsed_days' => $this->formatNumber((float) $elapsedDays, 0),
                'min_elapsed_days' => $this->formatNumber((float) $minElapsedDays, 0),
            ]);
        }

        if (is_numeric($actual) && is_numeric($expected) && is_numeric($delta)) {
            return $this->t('indicator.progress_pace', [
                'actual' => $this->formatNumber((float) $actual, 1),
                'expected' => $this->formatNumber((float) $expected, 1),
                'delta' => $this->formatNumber((float) $delta, 1),
            ]);
        }

        return parent::indicatorText($value, $meta);
    }
}
