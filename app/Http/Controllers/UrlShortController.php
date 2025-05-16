<?php

namespace App\Http\Controllers;

class UrlShortController extends Controller
{
    public function redirect($key)
    {
        $UrlShort = \App\Models\UrlShort::where('key', $key)->first();
        if ($UrlShort) {
            return redirect()->away($UrlShort->original_url);
        }

        return view('app.404');
    }
}
