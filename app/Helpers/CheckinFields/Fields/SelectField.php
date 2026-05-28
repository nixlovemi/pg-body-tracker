<?php

namespace App\Helpers\CheckinFields\Fields;

use App\Enums\CheckinFieldType;
use App\Helpers\ApiResponse;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;

class SelectField extends AbstractCheckinField
{
    public function getFieldType(): string
    {
        return CheckinFieldType::SELECT;
    }

    public function getFieldTypeLabel(): string
    {
        return __('messages.pages.checkin.config.fieldTypeSelect');
    }

    public function getFollowupInputView(): string
    {
        return 'app.checkin.fields.input-select';
    }

    public function getFormOptions(): array
    {
        return $this->normalizeOptions($this->config['options'] ?? []);
    }

    public function getFieldKey(): string
    {
        return (string) ($this->config['field_key'] ?? 'select_field');
    }

    public function getResponseType(): string
    {
        return AvaliationCheckinField::RESPONSE_TYPE_SELECT;
    }

    public function supportsOptions(): bool
    {
        return true;
    }

    /**
     * @param mixed $value
     */
    public function validateResponse($value): ApiResponse
    {
        if (!is_scalar($value) && $value !== null) {
            return new ApiResponse(true, __('messages.helpers.modelValidation.invalidField', ['attribute' => $this->getFieldKey()]));
        }

        $value = (string) $value;
        $options = (array) ($this->config['options'] ?? []);
        if (!empty($options) && !array_key_exists($value, $options)) {
            return new ApiResponse(true, __('messages.helpers.modelValidation.invalidField', ['attribute' => $this->getFieldKey()]));
        }

        return $this->successValidation();
    }

    public function normalizeResponse(mixed $value): mixed
    {
        return trim((string) $value);
    }

    public function formatResponseForDisplay(mixed $response): string
    {
        $normalized = trim((string) $response);
        $options = $this->getFormOptions();

        if (array_key_exists($normalized, $options)) {
            return $this->translateMaybe((string) $options[$normalized]);
        }

        return $normalized;
    }

    public function applyToAvaliation(Avaliation $avaliation, mixed $normalizedValue): void
    {
        // Select answers are stored in avaliation_checkin_fields.
    }
}
