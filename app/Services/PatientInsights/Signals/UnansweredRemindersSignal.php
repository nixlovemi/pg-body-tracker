<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;
use Carbon\Carbon;

class UnansweredRemindersSignal extends AbstractSignal
{
    public function key(): string
    {
        return 'unanswered_reminders';
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

        $sentAt = $this->resolveLastSentAt($checkinConfig);
        if (!$sentAt) {
            return $this->result(PatientSignalLevel::INFO, 0, $this->t('messages.info_no_dispatch_history'), null, []);
        }

        $sentDateYmd = $sentAt->copy()->format('Y-m-d');
        $lastResponseYmd = $checkinConfig->last_checkin_date?->format('Y-m-d');
        $hasPendingResponse = $lastResponseYmd === null || $lastResponseYmd < $sentDateYmd;

        $count = (int) $checkinConfig->unanswered_reminders_sent;
        $maxReminders = max(0, (int) env('CHECKIN_MAX_REMINDERS_PER_CYCLE', 1));
        $reminderAtLimit = $maxReminders > 0 && $count >= $maxReminders;

        if (!$hasPendingResponse) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good_responded'), $count, [
                'unanswered_reminders' => $count,
                'max_reminders_per_cycle' => $maxReminders,
                'has_pending_response' => false,
            ]);
        }

        $expiryHours = max(1, (int) ($checkinConfig->link_expires_hours ?? 24));
        $hoursWithoutResponse = max(0, $sentAt->diffInHours($context->now()));
        $expiryRatio = round($hoursWithoutResponse / $expiryHours, 2);

        $attentionRatio = (float) $this->cfg('attention_expiry_ratio', 1.0);
        $riskRatio = (float) $this->cfg('risk_expiry_ratio', 2.0);
        $reminderRiskBonus = max(0, (int) $this->cfg('reminder_risk_bonus', 1));

        $riskPoints = 0;
        $message = $this->t('messages.good_within_window');

        if ($expiryRatio >= $riskRatio) {
            $riskPoints = 2;
            $message = $this->t('messages.risk_delay');
        } elseif ($expiryRatio >= $attentionRatio) {
            $riskPoints = 1;
            $message = $this->t('messages.attention_delay');
        }

        if ($count > 0 && $expiryRatio < $attentionRatio) {
            $riskPoints = max($riskPoints, 1);
            $message = $this->t('messages.attention_reminder_sent');
        }

        if ($reminderAtLimit && $expiryRatio >= $attentionRatio) {
            $riskPoints = min($this->maxRiskPoints(), $riskPoints + $reminderRiskBonus);
            $message = $this->t('messages.risk_with_reminder');
        }

        return $this->result($this->riskPointsToLevel($riskPoints), $riskPoints, $message, $count, [
            'unanswered_reminders' => $count,
            'max_reminders_per_cycle' => $maxReminders,
            'hours_without_response' => $hoursWithoutResponse,
            'link_expires_hours' => $expiryHours,
            'expiry_ratio' => $expiryRatio,
            'has_pending_response' => true,
        ]);
    }

    private function resolveLastSentAt($checkinConfig): ?Carbon
    {
        if ($checkinConfig->last_checkin_sent_at) {
            return Carbon::parse($checkinConfig->last_checkin_sent_at);
        }

        if ($checkinConfig->last_checkin_sent_date) {
            return Carbon::parse($checkinConfig->last_checkin_sent_date)->startOfDay();
        }

        return null;
    }

    private function riskPointsToLevel(int $riskPoints): string
    {
        if ($riskPoints <= 0) {
            return PatientSignalLevel::GOOD;
        }

        if ($riskPoints === 1) {
            return PatientSignalLevel::ATTENTION;
        }

        return PatientSignalLevel::RISK;
    }
}
