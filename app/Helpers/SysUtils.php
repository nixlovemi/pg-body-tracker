<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\SessionGuard;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\File;

final class SysUtils {

    private const ENCODE_FROM_CHARS = '+/=';
    private const ENCODE_TO_CHARS = '-;$';

    public static function getWebAuth(): SessionGuard
    {
        return Auth::guard('web');
    }

    public static function getLoggedInUser(): ?User
    {
        $userId = SysUtils::getWebAuth()->id() ?? 0;
        if ($userId == 0) {
            $User = auth()->user();
            $userId = $User->id ?? 0;
        }
        return User::find($userId);
    }

    public static function loginUser(User $User): bool
    {
        $Auth = SysUtils::getWebAuth();
        if (false === $Auth->loginUsingId($User->id)) {
            return false;
        }

        return true;
    }

    public static function logout(bool $flushSession=true): void
    {
        $User = SysUtils::getLoggedInUser();
        if ($User) {
            try {
                SysUtils::getWebAuth()->logout();
            } catch (\Throwable $th) { dd($th); }
        }

        if ($flushSession) {
            // flushing the session will remove CSRF Token's value
            session()->flush();
        }
    }

    public static function isLoggedIn(): bool
    {
        return self::getLoggedInUser() !== null;
    }

    public static function applyTimezone($date)
    {
        return \Carbon\Carbon::parse($date)->timezone(getenv('APP_TIME_ZONE'));
    }

    public static function timezoneDate($date, $format): string
    {
        if (empty($date)) {
            return '';
        }
        return \Carbon\Carbon::parse($date)->setTimezone(env('APP_TIME_ZONE'))->format($format);
    }

    public static function timezoneNow($format): string
    {
        return \Carbon\Carbon::now()->setTimezone(env('APP_TIME_ZONE'))->format($format);
    }

    public static function encodeStr(string $text): string
    {
        $base64 = base64_encode($text);
        $replacedB64 = strtr($base64, self::ENCODE_FROM_CHARS, self::ENCODE_TO_CHARS);
        $rotStr = str_rot13($replacedB64);

        // remove chars like %24 because they are not allowed in URL
        $rotStr = preg_replace('/[^a-zA-Z0-9]/', '', $rotStr);

        return $rotStr;
    }

    public static function decodeStr(string $encodedId): ?string
    {
        $unRot = str_rot13($encodedId);
        $unreplaceB64 = strtr($unRot, self::ENCODE_TO_CHARS, self::ENCODE_FROM_CHARS);
        $originalStr = base64_decode($unreplaceB64);
        $originalWithoutSpecial = preg_replace ('/[^\p{L}\p{N}]/u', '@', $originalStr);

        return $originalWithoutSpecial;
    }

    public static function sanitizeFileNameForUpload(string $fileName): string
    {
        $fileName = str_replace(
            ['Ã', 'ã', 'Á', 'á', 'Â', 'â', 'À', 'à', 'É', 'é', 'Ê', 'ê', 'Í', 'í', 'Ó', 'ó', 'Ô', 'ô', 'Õ', 'õ', 'Ú', 'ú', 'Ç', 'ç'],
            ['A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'E', 'e', 'E', 'e', 'I', 'i', 'O', 'o', 'O', 'o', 'o', 'o', 'u', 'u', 'C', 'c'],
            $fileName,
        );
        $fileName = preg_replace('/[^a-zA-Z0-9\.\-]/', '_', $fileName);
        return $fileName;
    }

    public static function getArrayOnlyKeys(array $array, array $keys): array
    {
        if (!count($keys) > 0) {
            return [];
        }

        return array_filter($array, function($key) use ($keys) {
            return false !== array_search($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    public static function formatNumberToDb(string $number, int $decimals, string $decimalSep, string $thousandSep): float
    {
        $newNumber = str_replace(['R$', '$', '€', '£', '¥', $thousandSep], '', $number);
        $newNumber = trim($newNumber);
        $newNumber = str_replace($decimalSep, '.', $newNumber);

        return (float) number_format((float) $newNumber, $decimals, '.', '');
    }

    public static function formatDbToNumber(string $number, int $decimals): string
    {
        return number_format((float) $number, $decimals, __('messages.decimalSeparator'), __('messages.thousandSeparator'));
    }

    public static function formatCurrencyBr(float $value, int $decimals=2, string $currency=''): string
    {
        $result = $currency . ' ' . number_format($value, $decimals, ',', '.');
        return trim($result);
    }

    public static function reformatDate(string $date, string $fromFormat, string $toFormat): string
    {
        $dateObj = DateTime::createFromFormat($fromFormat, $date);
        return $dateObj->format($toFormat);
    }

    public static function hexToRGB(string $hex): array
    {
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 6) {
            list($r, $g, $b) = str_split($hex, 2);
        } elseif (strlen($hex) == 3) {
            list($r, $g, $b) = str_split(str_repeat($hex, 2), 2);
        } else {
            return [0, 0, 0];
        }
        return [
            hexdec($r),
            hexdec($g),
            hexdec($b)
        ];
    }

    public static function getImageBase64(string $filePath): ?string
    {
        // check if file exists
        if (@file_exists($filePath)) {
            return self::getBase64String($filePath);
        }

        // remove /public/ if it exists in the path
        if (strpos($filePath, '/public') === 0) {
            $filePath = str_replace('/public', '', $filePath);
        }

        // check if file exists in public
        $publicPath = str_replace(env('APP_PREFIX_FOLDER') . DIRECTORY_SEPARATOR, '', public_path(self::getOsPhotosFolder($filePath, env('APP_PREFIX_FOLDER'))));
        if (file_exists($publicPath)) {
            return self::getBase64String($publicPath);
        }

        // check if file exists in storage
        $idx = strpos($filePath, 'storage/');
        $publicPath = substr($filePath, $idx + strlen('storage/'));
        $publicPath = storage_path(self::getOsPhotosFolder(DIRECTORY_SEPARATOR . $publicPath));
        if (File::exists($publicPath)) {
            return self::getBase64String($publicPath);
        }

        return null;
    }

    public static function getBase64String(string $filePath): string
    {
        $mimeType = mime_content_type($filePath);
        $base64 = base64_encode(file_get_contents($filePath));
        return "data:$mimeType;base64,$base64";
    }

    public static function getOsPhotosFolder(string $basePath, string $prefix='app'): string
    {
        // Because of the chance I did from /app to /admin, because if I use /app in the server we get /public/app
        // here the public folder is under /admin (ENV config) BUT all other things for storage are under /storage/app
        $basePath = str_replace('/', DIRECTORY_SEPARATOR, $basePath);
        return $prefix.$basePath;
    }

    public static function getFormattedDeltaText(float $delta, string $sufix): string
    {
        $signal = $delta >= 0 ? '+' : '-';
        $arrow = $delta >= 0 ? Icons::ARROW_UP : Icons::ARROW_DOWN;

        return sprintf(
            '<span>%s %s %s</span>',
            $signal,
            SysUtils::formatDbToNumber(abs($delta), 1) . $sufix,
            $arrow,
        );
    }
}
