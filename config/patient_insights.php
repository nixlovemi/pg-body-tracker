<?php

use App\Services\PatientInsights\Signals\AvaliationFrequencySignal;
use App\Services\PatientInsights\Signals\CheckinResponseRate30dSignal;
use App\Services\PatientInsights\Signals\DaysSinceCheckinResponseSignal;
use App\Services\PatientInsights\Signals\GoalProgressPaceSignal;
use App\Services\PatientInsights\Signals\UnansweredRemindersSignal;
use App\Services\PatientInsights\Signals\WeightTrend14dSignal;
use App\Services\PatientInsights\Signals\WeightTrend30dSignal;
use App\Services\PatientInsights\Signals\WeightVariability7dSignal;

return [
    // Add/remove signals here without touching engine internals.
    'signals' => [
        WeightTrend14dSignal::class,
        WeightTrend30dSignal::class,
        WeightVariability7dSignal::class,
        CheckinResponseRate30dSignal::class,
        DaysSinceCheckinResponseSignal::class,
        UnansweredRemindersSignal::class,
        GoalProgressPaceSignal::class,
        AvaliationFrequencySignal::class,
    ],

    'summary' => [
        'min_confidence_percent' => 60.0,
    ],

    'free_card' => [
        'history' => [
            // 'weekly' keeps the most recent snapshot per week.
            // 'daily' keeps raw chronological snapshots.
            'mode' => 'weekly',
            'max_items' => 8,
            'window_days' => 84,
        ],
    ],

    // Thresholds can be tuned without code changes in signal subclasses.
    'thresholds' => [
        'weight_trend_14d' => [
            'neutral_margin_percent' => 0.3,
        ],
        'weight_trend_30d' => [
            'neutral_margin_percent' => 0.5,
        ],
        'weight_variability_7d' => [
            'attention_percent' => 0.5,
            'risk_percent' => 1.0,
        ],
        'checkin_response_rate_30d' => [
            'good_percent' => 80,
            'attention_percent' => 50,
        ],
        'days_since_checkin_response' => [
            'attention_days' => 10,
            'risk_days' => 14,
        ],
        'unanswered_reminders' => [
            'attention_expiry_ratio' => 1.0,
            'risk_expiry_ratio' => 2.0,
            'reminder_risk_bonus' => 1,
        ],
        'goal_progress_pace' => [
            'min_elapsed_days' => 7,
            'good_delta_percent' => -10,
            'attention_delta_percent' => -25,
        ],
        'avaliation_frequency' => [
            'good_days_max' => 21,
            'attention_days_max' => 35,
        ],
    ],
];
