<?php

namespace App\Enums;

final class CheckinFieldType
{
    public const WEIGHT = 'weight';
    public const SELECT = 'select';
    public const TEXTAREA = 'textarea';
    public const YES_NO = 'yes_no';

    private function __construct()
    {
    }
}
