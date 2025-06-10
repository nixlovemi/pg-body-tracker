<?php

namespace App\Presenters;

final class LayoutSitePresenter
{
    public static function getMenuLinks(): array
    {
        return [
            ['title' => __('messages.pages.siteHome.header.menuHome'), 'url' => '#home'],
            ['title' => __('messages.pages.siteHome.header.menuFeatures'), 'url' => '#features'],
            ['title' => __('messages.pages.siteHome.header.menuAbout'), 'url' => '#about'],
            ['title' => __('messages.pages.siteHome.header.menuWhyUs'), 'url' => '#why'],
            ['title' => __('messages.pages.siteHome.header.menuVersions'), 'url' => '#versions'],
        ];
    }
}
