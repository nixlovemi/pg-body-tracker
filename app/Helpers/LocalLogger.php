<?php

namespace App\Helpers;

use App\Helpers\SysUtils;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

final class LocalLogger
{
    public static function log(string $text, ?array $logVars=[]): void
    {
        $date = \Carbon\Carbon::now();
        $fileName = 'logs/' . SysUtils::timezoneDate($date, 'D') . '.log';
        $fullText = '[' . SysUtils::timezoneDate($date, 'd/m/Y H:i:s') . '] ' . $text . '. ';
        $fullText .= self::getLogVarsText($logVars);

        Storage::disk('local')->append(
            $fileName,
            $fullText
        );
    }

    public static function getLogVars(): array
    {
        return [
            'userId' => /*SysUtils::getLoggedInUser()?->id*/ null, // TODO: Uncomment this line
            'reqMethod' => $_SERVER['REQUEST_METHOD'] ?? null,
            'route' => Route::currentRouteName(),
            'request' => $_REQUEST,
            'backTrace' => array_slice(debug_backtrace(), 0, 5, true)
        ];
    }

    private static function getLogVarsText(?array $logVars=[]): string
    {
        return "JSON: " . json_encode(array_merge(
            self::getLogVars(),
            $logVars
        ));
    }
}
