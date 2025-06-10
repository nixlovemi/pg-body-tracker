<?php

namespace App\Presenters;

final class LayoutSitePresenter
{
    public static function getMenuLinks(string $currentRoute): array
    {
        $prefix = $currentRoute === 'site.home' ? '' : route('site.home') . '/';

        return [
            ['title' => __('messages.pages.siteHome.header.menuHome'), 'url' => $prefix . '#home'],
            ['title' => __('messages.pages.siteHome.header.menuFeatures'), 'url' => $prefix . '#features'],
            ['title' => __('messages.pages.siteHome.header.menuAbout'), 'url' => $prefix . '#about'],
            ['title' => __('messages.pages.siteHome.header.menuWhyUs'), 'url' => $prefix . '#why'],
            ['title' => __('messages.pages.siteHome.header.menuVersions'), 'url' => $prefix . '#versions'],
            ['title' => __('messages.pages.siteHome.header.menuLogin'), 'url' => route('app.login')],
        ];
    }
}
