<?php

namespace App\Helpers\CheckinFields;

use App\Enums\CheckinFieldType;
use App\Helpers\CheckinFields\Fields\YesNoField;
use App\Helpers\CheckinFields\Fields\SelectField;
use App\Helpers\CheckinFields\Fields\TextAreaField;
use App\Helpers\CheckinFields\Fields\WeightField;

class CheckinFieldRegistry
{
    /** @var array<string, class-string<CheckinFieldContract>> */
    private static $fields = [];

    private static $bootstrapped = false;

    public static function register(string $fieldType, string $fieldClass): void
    {
        if (!class_exists($fieldClass)) {
            return;
        }

        if (!is_subclass_of($fieldClass, CheckinFieldContract::class)) {
            return;
        }

        self::$fields[$fieldType] = $fieldClass;
    }

    public static function registerDefaults(): void
    {
        if (self::$bootstrapped) {
            return;
        }

        self::register(CheckinFieldType::WEIGHT, WeightField::class);
        self::register(CheckinFieldType::SELECT, SelectField::class);
        self::register(CheckinFieldType::TEXTAREA, TextAreaField::class);
        self::register(CheckinFieldType::YES_NO, YesNoField::class);

        self::$bootstrapped = true;
    }

    public static function make(string $fieldType, array $config = []): ?CheckinFieldContract
    {
        self::registerDefaults();

        $fieldClass = self::$fields[$fieldType] ?? null;
        if (!$fieldClass) {
            return null;
        }

        return new $fieldClass($config);
    }

    /**
     * @return array<string, class-string<CheckinFieldContract>>
     */
    public static function all(): array
    {
        self::registerDefaults();
        return self::$fields;
    }
}
