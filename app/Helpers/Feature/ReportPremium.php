<?php

namespace App\Helpers\Feature;

use App\Helpers\Report\ReportAbstract;

class ReportPremium extends FeatureAbstract
{
    private ReportAbstract $report;

    public static function make(ReportAbstract $report): self
    {
        $class = new self();
        $class->report = $report;
        return $class;
    }

    public function getName(): string
    {
        return 'Report Premium';
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.ReportPremium.validateMessage');
    }

    public function validate(): bool
    {
        if (false === $this->report->premiumOnly()) {
            return true;
        }

        return $this->getPlanType() === self::FEATURE_PLAN_TYPE_PREMIUM;
    }
}
