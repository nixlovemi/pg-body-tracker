<?php

namespace App\Helpers\CheckinFields\Fields;

use App\Enums\CheckinFieldType;
use App\Helpers\ApiResponse;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;

class TextAreaField extends AbstractCheckinField
{
    public function getFieldType(): string
    {
        return CheckinFieldType::TEXTAREA;
    }

    public function getFieldTypeLabel(): string
    {
        return __('messages.pages.checkin.config.fieldTypeTextarea');
    }

    public function getFollowupInputView(): string
    {
        return 'app.checkin.fields.input-textarea';
    }

    public function getFieldKey(): string
    {
        return (string) ($this->config['field_key'] ?? 'notes');
    }

    public function getResponseType(): string
    {
        return AvaliationCheckinField::RESPONSE_TYPE_TEXTAREA;
    }

    /**
     * @param mixed $value
     */
    public function validateResponse($value): ApiResponse
    {
        if (!is_scalar($value) && $value !== null) {
            return new ApiResponse(true, __('messages.helpers.modelValidation.invalidField', ['attribute' => $this->getFieldKey()]));
        }

        $length = mb_strlen((string) $value);
        if ($length > 5000) {
            return new ApiResponse(true, __('messages.helpers.modelValidation.invalidField', ['attribute' => $this->getFieldKey()]));
        }

        return $this->successValidation();
    }

    public function normalizeResponse(mixed $value): mixed
    {
        return trim((string) $value);
    }

    /**
     * @param mixed $normalizedValue
     */
    public function applyToAvaliation(Avaliation $avaliation, $normalizedValue): void
    {
        // Text responses are stored in avaliation_checkin_fields.
    }
}
