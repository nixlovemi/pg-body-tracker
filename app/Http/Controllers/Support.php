<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Mail\SupportContact;
use Illuminate\Support\Facades\Mail;
use App\Helpers\SysUtils;

class Support extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('app.support.index', [
            'PAGE_TITLE' => __('messages.pages.support.menuTitle'),
        ]);
    }

    public function doSend(Request $request)
    {
        $request->validate([
            'contact-name' => 'required|string|min:2|max:60',
            'contact-email' => 'required|email|max:255',
            'contact-subject' => 'required|string|max:255',
            'contact-message' => 'required|string|max:5000',
        ]);

        // Send mail
        Mail::to(env('SUPPORT_EMAIL'))
            ->send(
              new SupportContact(
                    SysUtils::getLoggedInUser(),
                    $request->input('contact-subject'),
                    $request->input('contact-message'),
                )
            );

        return redirect()->route('app.support.index')
            ->with('success', __('messages.pages.support.requestSent'));
    }
}
