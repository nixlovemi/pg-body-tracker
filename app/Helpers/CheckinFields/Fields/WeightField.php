<?php

namespace App\Helpers\CheckinFields\Fields;

use App\Enums\CheckinFieldType;
use App\Helpers\ApiResponse;
use App\Helpers\SysUtils;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;

class WeightField extends AbstractCheckinField
{
    public const FIELD_KEY = 'weight_kg';

    public function getFieldType(): string
    {
        return CheckinFieldType::WEIGHT;
    }

    public function getFieldTypeLabel(): string
    {
        return __('messages.models.Client.fields.weight');
    }

    public function getDisplayLabel(): string
    {
        return __('messages.pages.checkin.followup.weightLabel');
    }

    public function showInConfigForm(): bool
    {
        return false;
    }

    public function getFieldKey(): string
    {
        return self::FIELD_KEY;
    }

    public function getResponseType(): string
    {
        return AvaliationCheckinField::RESPONSE_TYPE_NUMBER;
    }

    /**
     * @param mixed $value
     */
    public function validateResponse(mixed $value): ApiResponse
    {
        $weight = $this->parseWeight($value);
        if (is_null($weight)) {
            return new ApiResponse(true, __('messages.models.Client.fields.weight') . ': invalid');
        }

        if ($weight < 20 || $weight > 400) {
            return new ApiResponse(true, __('messages.models.Client.fields.weight') . ': out of range');
        }

        return $this->successValidation();
    }

    public function normalizeResponse(mixed $value): mixed
    {
        $weight = $this->parseWeight($value);
        if (is_null($weight)) {
            return null;
        }

        return round($weight, 1);
    }

    public function formatResponseForDisplay(mixed $response): string
    {
        if (!is_numeric($response)) {
            return trim((string) $response);
        }

        return number_format((float) $response, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator'));
    }

    public function applyToAvaliation(Avaliation $avaliation, mixed $normalizedValue): void
    {
        $avaliation->weight_kg = (float) $normalizedValue;
        $avaliation->calculate_perc_fat_by = Avaliation::CALCULATE_PERC_FAT_BY_MEASURES;
    }

    private function parseWeight(mixed $value): ?float
    {
        if (is_null($value)) {
            return null;
        }

        $weightRaw = trim((string) $value);
        if ($weightRaw === '') {
            return null;
        }

        return SysUtils::formatNumberToDb(
            $weightRaw,
            3,
            __('messages.decimalSeparator'),
            __('messages.thousandSeparator')
        );
    }
}
