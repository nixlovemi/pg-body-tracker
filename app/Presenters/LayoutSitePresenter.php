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

    public static function getFaq(): array
    {
        $arrFaq = [];

        for ($i = 1; $i <= 8; $i++) {
            $questionKey = "messages.pages.siteFaq.faq.question{$i}";
            $answerKey = "messages.pages.siteFaq.faq.answer{$i}";

            if (__($questionKey) !== $questionKey && __($answerKey) !== $answerKey) {
                $arrFaq[] = [
                    'question' => __($questionKey),
                    'answer' => __($answerKey),
                ];
            }
        }

        return $arrFaq;
    }
}
