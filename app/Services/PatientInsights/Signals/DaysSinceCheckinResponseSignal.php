<?php

namespace App\Services\PatientInsights\Signals;

use App\Enums\PatientSignalLevel;
use App\Services\PatientInsights\SignalContext;
use App\Services\PatientInsights\SignalResult;

class DaysSinceCheckinResponseSignal extends AbstractSignal
{
    public function key(): string
    {
        return 'days_since_checkin_response';
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

        $referenceDate = $checkinConfig->last_checkin_date ?: $checkinConfig->last_checkin_sent_date;
        $daysWithoutResponse = $this->daysBetween($context, $referenceDate);
        if ($daysWithoutResponse === null) {
            return $this->result(PatientSignalLevel::INFO, 0, $this->t('messages.info_no_history'), null, []);
        }

        $attentionDays = (int) $this->cfg('attention_days', 10);
        $riskDays = (int) $this->cfg('risk_days', 14);

        if ($daysWithoutResponse <= $attentionDays) {
            return $this->result(PatientSignalLevel::GOOD, 0, $this->t('messages.good'), $daysWithoutResponse, ['days_without_response' => $daysWithoutResponse]);
        }

        if ($daysWithoutResponse <= $riskDays) {
            return $this->result(PatientSignalLevel::ATTENTION, 1, $this->t('messages.attention'), $daysWithoutResponse, ['days_without_response' => $daysWithoutResponse]);
        }

        return $this->result(PatientSignalLevel::RISK, 3, $this->t('messages.risk'), $daysWithoutResponse, ['days_without_response' => $daysWithoutResponse]);
    }
}
