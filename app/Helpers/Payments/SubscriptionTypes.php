<?php

namespace App\Helpers\Payments;

use App\Helpers\SysUtils;

abstract class SubscriptionTypes
{
    public const PLAN_MONTHLY = 'monthly';
    public const PLAN_QUARTERLY = 'quarterly';
    public const PLAN_SEMIANNUAL = 'semiannual';
    public const PLAN_ANNUAL = 'annual';
    private const PLANS = [
        self::PLAN_MONTHLY => ['price' => 44, 'frequency' => 1, 'frequency_type' => 'months'],
        self::PLAN_QUARTERLY => ['price' => 117, 'frequency' => 3, 'frequency_type' => 'months'],
        self::PLAN_SEMIANNUAL => ['price' => 204, 'frequency' => 6, 'frequency_type' => 'months'],
        self::PLAN_ANNUAL => ['price' => 360, 'frequency' => 12, 'frequency_type' => 'months'],
    ];

    public static function getLowestPricePlan(): string
    {
        $lowestPlan = array_reduce(self::PLANS, function ($carry, $item) {
            if ($carry === null) {
                return $item;
            }

            $itemMonthly = self::calculatePricePerMonth($item['price'], $item['frequency']);
            $carryMonthly = self::calculatePricePerMonth($carry['price'], $carry['frequency']);
            return $itemMonthly < $carryMonthly ? $item : $carry;
        });

        return SysUtils::formatDbToNumber($lowestPlan['price']/$lowestPlan['frequency'], 2);
    }

    public static function getPlans(): array
    {
        return array_keys(self::PLANS);
    }

    public static function getPlanLabel(string $plan): string
    {
        return __('messages.pages.premium.plans.' . $plan);
    }

    public static function getPlanPrice(string $plan): float
    {
        $details = self::getPlanDetails($plan);
        return round($details['price'], 2);
    }

    public static function getPlanPricePerMonth(string $plan): float
    {
        $details = self::getPlanDetails($plan);
        $frequency = $details['frequency'];
        $price = $details['price'];
        return self::calculatePricePerMonth($price, $frequency);
    }

    public static function getPlanFrequency(string $plan): int
    {
        $details = self::getPlanDetails($plan);
        return (int) $details['frequency'];
    }

    public static function getPlanFrequencyType(string $plan): string
    {
        $details = self::getPlanDetails($plan);
        return $details['frequency_type'];
    }

    private static function getPlanDetails(string $plan): ?array
    {
        $plans = self::PLANS[$plan] ?? null;
        if ($plans === null) {
            throw new \InvalidArgumentException("Invalid plan type: $plan");
        }

        return $plans;
    }

    private static function calculatePricePerMonth(float $price, int $frequency): float
    {
        return round($price / $frequency, 2);
    }
}
