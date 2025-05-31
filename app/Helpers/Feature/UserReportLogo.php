<?php

namespace App\Helpers\Feature;

class UserReportLogo extends FeatureAbstract
{
    public function getName(): string
    {
        return 'User Report Logo';
    }

    public function validate(): bool
    {
        return $this->getPlanType() === self::FEATURE_PLAN_TYPE_PREMIUM;
    }

    public function getValidateMsg(): string
    {
        return __('messages.components.Features.UserReportLogo.validateMessage');
    }
}
