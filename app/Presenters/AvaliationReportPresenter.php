<?php

namespace App\Presenters;

use App\Models\Avaliation;

final class AvaliationReportPresenter
{
    public static function getUserLogoBase64(Avaliation $Avaliation): string
    {
        return $Avaliation->client->user?->info?->getLogoBase64();
    }

    public static function getUserInfoLoopArray(Avaliation $Avaliation): array
    {
        $items = array_filter([
            'title',
            'license_text',
            'whatsapp_phone',
        ], function ($item) use ($Avaliation) {
            return !empty($Avaliation->client->user?->info?->{$item});
        });

        // loop and add key 'value' with the value of the field
        return array_map(function ($item) use ($Avaliation) {
            return [
                'value' => $Avaliation->client->user?->info->{$item}
            ];
        }, $items);
    }

    public static function getSocialLinks(Avaliation $Avaliation)
    {
        $items = array_filter([
            ['field' => 'link_telegram', 'icon' => 'fab fa-telegram'],
            ['field' => 'link_facebook', 'icon' => 'fab fa-facebook'],
            ['field' => 'link_instagram', 'icon' => 'fab fa-instagram'],
            ['field' => 'link_twitter', 'icon' => 'fab fa-twitter'],
            ['field' => 'link_youtube', 'icon' => 'fab fa-youtube'],
            ['field' => 'link_website', 'icon' => 'fas fa-globe']
        ], function ($item) use ($Avaliation) {
            return !empty($Avaliation->client->user?->info?->{$item['field']});
        });

        // loop and add key 'value' with the value of the field
        return array_map(function ($item) use ($Avaliation) {
            return [
                'icon' => $item['icon'],
                'value' => $Avaliation->client->user?->info->{$item['field']}
            ];
        }, $items);
    }
}
