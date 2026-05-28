<?php

namespace App\Presenters;

use App\Helpers\Icons;
use App\Helpers\Payments\SubscriptionTypes;
use App\Helpers\SysUtils;
use Mockery\Matcher\Subset;

final class SubscriptionUpgradePresenter
{
    public static function getFeaturesFreePremium(bool $free = true): array
    {
        $maxClientsLabel = $free ?
            __('messages.pages.premium.freeVsPremium.features.line1Free'):
            __('messages.pages.premium.freeVsPremium.features.line1Premium');

        $data = [
            ['label' => $maxClientsLabel, 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line2'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line3'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line4'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line5'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line6'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line7'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line8'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line9'), 'free' => true],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line10'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line11'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line12'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line13'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line14'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line15'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line16'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line17'), 'free' => false],
            ['label' => __('messages.pages.premium.freeVsPremium.features.line18'), 'free' => false],
        ];

        return $data;
    }

    public static function getIconTrue(): string
    {
        return '<span class="text-success mr-2">' . Icons::CHECK . '</span>';
    }

    public static function getIconFalse(): string
    {
        return '<span class="text-danger mr-2">' . Icons::TIMES . '</span>';
    }

    public static function getLowestPricePlan(): string
    {
        return SubscriptionTypes::getLowestPricePlan();
    }

    public static function getPlans(): array
    {
        $plans = SubscriptionTypes::getPlans();
        $formattedPlans = [];
        foreach ($plans as $plan) {
            $label = SubscriptionTypes::getPlanLabel($plan);
            $price = SubscriptionTypes::getPlanPrice($plan);
            $frequency = SubscriptionTypes::getPlanFrequency($plan);
            $priceMonth = SubscriptionTypes::getPlanPricePerMonth($plan);

            $formattedPlans[$plan] = [
                'label' => $label,
                'frequency' => $frequency,
                'price' => $price,
                'formatted_price' => SysUtils::formatDbToNumber($price, 2),
                'price_month' => $priceMonth,
                'formatted_price_month' => SysUtils::formatDbToNumber($priceMonth, 2),
                'formatted_price_year' => SysUtils::formatDbToNumber($priceMonth * 12, 2),
            ];
        }

        return $formattedPlans;
    }

    public static function getDefaultPlanKey(): string
    {
        return SubscriptionTypes::PLAN_ANNUAL;
    }
}
