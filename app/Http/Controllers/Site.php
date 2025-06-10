<?php

namespace App\Http\Controllers;

class Site extends Controller
{
    public function home()
    {
        return view('site.home', [
            'PAGE_TITLE' => 'Home',
        ]);
    }

    public function privacy()
    {
        return view('site.privacy', [
            'PAGE_TITLE' => __('messages.pages.sitePrivacy.title'),
        ]);
    }

    public function terms()
    {
        return view('site.terms', [
            'PAGE_TITLE' => __('messages.pages.siteTerms.title'),
        ]);
    }

    public function faq()
    {
        return view('site.faq', [
            'PAGE_TITLE' => __('messages.pages.siteFaq.title'),
        ]);
    }
}
