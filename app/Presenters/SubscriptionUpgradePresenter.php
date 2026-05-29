<?php

namespace App\Presenters;

use App\Helpers\Icons;
use App\Helpers\Payments\SubscriptionTypes;
use App\Helpers\SysUtils;

final class SubscriptionUpgradePresenter
{
    public static function getFeaturesFreePremium(bool $free = true): array
    {
        $features = __('messages.pages.premium.freeVsPremium.features');
        if (!is_array($features)) {
            return [];
        }

        $groupedByLine = [];
        foreach ($features as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (!preg_match('/^line(\d+)(Free|Premium|FreeEnabled)$/', $key, $matches)) {
                continue;
            }

            $lineNumber = (int) $matches[1];
            $variant = $matches[2];

            if (!isset($groupedByLine[$lineNumber])) {
                $groupedByLine[$lineNumber] = [
                    'free_label' => null,
                    'premium_label' => null,
                    'free_enabled' => null,
                ];
            }

            if ($variant === 'Free') {
                if (!is_string($value)) {
                    continue;
                }

                $groupedByLine[$lineNumber]['free_label'] = $value;
                continue;
            }

            if ($variant === 'Premium') {
                if (!is_string($value)) {
                    continue;
                }

                $groupedByLine[$lineNumber]['premium_label'] = $value;
                continue;
            }

            if (is_bool($value)) {
                $groupedByLine[$lineNumber]['free_enabled'] = $value;
            }
        }

        if (count($groupedByLine) === 0) {
            return [];
        }

        ksort($groupedByLine);

        $data = [];
        foreach ($groupedByLine as $lineNumber => $lineData) {
            $label = self::resolveFeatureLabel($lineData, $free);
            if ($label === '') {
                continue;
            }

            $isAvailableForFreePlan = self::isAvailableForFreePlan($lineData);
            $data[] = [
                'label' => $label,
                'free' => $isAvailableForFreePlan,
            ];
        }

        return $data;
    }

    /**
     * @param array{free_label: string|null, premium_label: string|null, free_enabled: bool|null} $lineData
     */
    private static function resolveFeatureLabel(array $lineData, bool $free): string
    {
        if ($free && is_string($lineData['free_label'])) {
            return $lineData['free_label'];
        }

        if (!$free && is_string($lineData['premium_label'])) {
            return $lineData['premium_label'];
        }

        if ($free && is_string($lineData['premium_label'])) {
            return $lineData['premium_label'];
        }

        if (!$free && is_string($lineData['free_label'])) {
            return $lineData['free_label'];
        }

        return '';
    }

    /**
     * @param array{free_label: string|null, premium_label: string|null, free_enabled: bool|null} $lineData
     */
    private static function isAvailableForFreePlan(array $lineData): bool
    {
        if (is_bool($lineData['free_enabled'])) {
            return $lineData['free_enabled'];
        }

        return is_string($lineData['free_label']);
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
