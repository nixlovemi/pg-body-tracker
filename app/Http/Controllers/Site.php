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
}
