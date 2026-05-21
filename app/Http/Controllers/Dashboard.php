<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\SysUtils;

class Dashboard extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $user = SysUtils::getLoggedInUser();
        $clientCount = $user ? $user->getClientCount() : 0;
        $avaliationCount = $user ? $user->getAvaliationCount() : 0;

        return view('app.dashboard.index', [
            'PAGE_TITLE' => 'Dashboard',
            'CLIENT_COUNT' => $clientCount,
            'AVALIATION_COUNT' => $avaliationCount,
        ]);
    }

    public function clientsWithoutAvaliation30Days()
    {
        return view('app.dashboard.clientsWithoutAvaliation30Days', [
            'PAGE_TITLE' => __('messages.components.DashCardClientsWithoutAvaliation30Days.title'),
        ]);
    }

    public function clientsWithGoalsDueThisWeek()
    {
        return view('app.dashboard.clientsWithGoalsDueThisWeek', [
            'PAGE_TITLE' => __('messages.components.DashCardClientsWithGoalsDueThisWeek.title'),
        ]);
    }
}
