<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Client extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('app.client.index', [
            'PAGE_TITLE' => __('messages.pages.client.index.title'),
        ]);
    }

    public function add()
    {
        return 'ADD CLIENT';
    }

    public function edit()
    {
        return 'EDIT CLIENT';
    }
}
