<?php

namespace App\Http\Controllers;

class Site extends Controller
{
    public function home()
    {
        return view('site.home', [
            'PAGE_TITLE' => 'Home',
            'META_DESCRIPTION' => __('messages.pages.siteHome.metaDescription'),
        ]);
    }

    public function privacy()
    {
        return view('site.privacy', [
            'PAGE_TITLE' => __('messages.pages.sitePrivacy.title'),
            'META_DESCRIPTION' => __('messages.pages.sitePrivacy.metaDescription'),
        ]);
    }

    public function terms()
    {
        return view('site.terms', [
            'PAGE_TITLE' => __('messages.pages.siteTerms.title'),
            'META_DESCRIPTION' => __('messages.pages.siteTerms.metaDescription'),
        ]);
    }

    public function faq()
    {
        return view('site.faq', [
            'PAGE_TITLE' => __('messages.pages.siteFaq.title'),
            'META_DESCRIPTION' => __('messages.pages.siteFaq.metaDescription'),
        ]);
    }
}
