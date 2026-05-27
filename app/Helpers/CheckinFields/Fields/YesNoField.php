<?php

namespace App\Helpers\CheckinFields\Fields;

use App\Enums\CheckinFieldType;
use App\Helpers\ApiResponse;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;

class YesNoField extends AbstractCheckinField
{
    public const VALUE_YES = 'yes';
    public const VALUE_NO = 'no';

    public function getFieldType(): string
    {
        return CheckinFieldType::YES_NO;
    }

    public function getFieldTypeLabel(): string
    {
        return __('messages.pages.checkin.config.fieldTypeYesNo');
    }

    public function getFollowupInputView(): string
    {
        return 'app.checkin.fields.input-select';
    }

    public function getFormOptions(): array
    {
        return [
            self::VALUE_YES => __('messages.yes'),
            self::VALUE_NO => __('messages.no'),
        ];
    }

    public function getFieldKey(): string
    {
        return (string) ($this->config['field_key'] ?? 'yes_no_field');
    }

    public function getResponseType(): string
    {
        return AvaliationCheckinField::RESPONSE_TYPE_BOOLEAN;
    }

    /**
     * @param mixed $value
     */
    public function validateResponse($value): ApiResponse
    {
        $normalized = $this->normalizeResponse($value);
        if ($normalized !== self::VALUE_YES && $normalized !== self::VALUE_NO) {
            return new ApiResponse(true, __('messages.helpers.modelValidation.invalidField', ['attribute' => $this->getFieldKey()]));
        }

        return $this->successValidation();
    }

    public function normalizeResponse(mixed $value): mixed
    {
        if (is_bool($value)) {
            return $value ? self::VALUE_YES : self::VALUE_NO;
        }

        $normalized = strtolower(trim((string) $value));

        if ($normalized === '1' || $normalized === 'true') {
            return self::VALUE_YES;
        }

        if ($normalized === '0' || $normalized === 'false') {
            return self::VALUE_NO;
        }

        return $normalized;
    }

    public function formatResponseForDisplay(mixed $response): string
    {
        $normalized = $this->normalizeResponse($response);

        return $this->getFormOptions()[$normalized] ?? trim((string) $normalized);
    }

    public function applyToAvaliation(Avaliation $avaliation, mixed $normalizedValue): void
    {
        // Yes/No answers are stored in avaliation_checkin_fields.
    }
}
