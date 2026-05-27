<?php

namespace App\Presenters;

use App\Helpers\CheckinFields\CheckinFieldRegistry;

final class CheckinConfigPresenter
{
    public const DEFAULT_INTERVAL_DAYS = 7;
    public const DEFAULT_LINK_EXPIRES_HOURS = 48;

    public static function getIntervalDayOptions(): array
    {
        return [3, 7, 10, 14, 21, 30];
    }

    public static function getLinkExpiresOptions(): array
    {
        return [12, 24, 48, 72, 96, 168];
    }

    /**
     * @return array<int, array{value:string,label:string,supports_options:bool}>
     */
    public static function getAvailableFieldTypes(): array
    {
        $options = [];
        foreach (array_keys(CheckinFieldRegistry::all()) as $type) {
            $Field = CheckinFieldRegistry::make($type);
            if (!$Field || !$Field->showInConfigForm()) {
                continue;
            }

            $options[] = [
                'value' => $type,
                'label' => $Field->getFieldTypeLabel(),
                'supports_options' => $Field->supportsOptions(),
            ];
        }

        usort($options, fn($a, $b) => strcmp((string) $a['value'], (string) $b['value']));

        return $options;
    }
}
