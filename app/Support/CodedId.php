<?php

namespace App\Support;

use App\Helpers\SysUtils;

final class CodedId
{
    public static function decode(string $value): ?int
    {
        $decoded = SysUtils::decodeStr($value);

        return ctype_digit((string) $decoded) && (int) $decoded > 0
            ? (int) $decoded
            : null;
    }
}
