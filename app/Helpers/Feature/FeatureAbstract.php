<?php

namespace App\Helpers\Feature;

use App\Models\User;
use App\Helpers\SysUtils;

abstract class FeatureAbstract
{
    public const FEATURE_PLAN_TYPE_FREE = 'free';
    public const FEATURE_PLAN_TYPE_PREMIUM = 'premium';

    public function __construct(public ?User $User=null)
    {
        if ($this->User === null) {
            $this->User = SysUtils::getLoggedInUser();
        }
    }
    abstract public function getName(): string;
    /** Overwrite it when needed */
    public function validate(): bool
    {
        return $this->getPlanType() === self::FEATURE_PLAN_TYPE_PREMIUM;
    }
    abstract public function getValidateMsg(): string;
    final public static function fGetPlanTypes(): array
    {
        return [
            self::FEATURE_PLAN_TYPE_FREE,
            self::FEATURE_PLAN_TYPE_PREMIUM,
        ];
    }
    final public function getPlanType(): string
    {
        return $this->User->getPlanType();
    }
    final public static function fGetLabelPlanType(string $planType): string
    {
        return match ($planType) {
            self::FEATURE_PLAN_TYPE_FREE => __('messages.components.Features.labelFreePlan'),
            self::FEATURE_PLAN_TYPE_PREMIUM => __('messages.components.Features.labelPremiumPlan'),
            default => __('messages.components.Features.labelFreePlan'),
        };
    }
}
