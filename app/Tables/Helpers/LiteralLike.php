<?php

namespace App\Tables\Helpers;

final class LiteralLike
{
    public static function contains(string $value): string
    {
        return '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $value) . '%';
    }
}
