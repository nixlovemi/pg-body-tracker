<?php

namespace App\Helpers\CheckinFields\Fields;

use App\DTO\Checkin\CheckinFieldConfigDTO;
use App\Helpers\ApiResponse;
use App\Helpers\CheckinFields\CheckinFieldContract;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

abstract class AbstractCheckinField implements CheckinFieldContract
{
    /** @var array<string, mixed> */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getFieldMeta(): array
    {
        return $this->config;
    }

    abstract public function getFieldTypeLabel(): string;

    public function getDisplayLabel(): string
    {
        $label = trim((string) ($this->config['label'] ?? ''));
        if ($label !== '') {
            return $this->translateMaybe($label);
        }

        $fieldKey = (string) ($this->config['field_key'] ?? $this->getFieldKey());

        return ucfirst(str_replace('_', ' ', $fieldKey));
    }

    public function showInConfigForm(): bool
    {
        return true;
    }

    public function getFollowupInputView(): string
    {
        return 'app.checkin.fields.input-text';
    }

    public function getFormOptions(): array
    {
        return [];
    }

    public function supportsOptions(): bool
    {
        return false;
    }

    public function normalizeFieldConfigDTO(CheckinFieldConfigDTO $fieldConfig): CheckinFieldConfigDTO
    {
        $label = trim($fieldConfig->getLabel());
        $fieldKey = trim($fieldConfig->getFieldKey() !== '' ? $fieldConfig->getFieldKey() : $this->getFieldKey());
        $options = $this->normalizeOptions($fieldConfig->getOptions());

        if (!$this->supportsOptions()) {
            $options = [];
        }

        return $fieldConfig
            ->setFieldType($this->getFieldType())
            ->setFieldKey($fieldKey)
            ->setLabel($label !== '' ? $label : $this->getDisplayLabel())
            ->setRequired($fieldConfig->isRequired())
            ->setOptions($options);
    }

    public function normalizeFieldConfig(array $field): array
    {
        return $this->normalizeFieldConfigDTO(CheckinFieldConfigDTO::fromArray($field))->toArray();
    }

    public function formatResponseForDisplay(mixed $response): string
    {
        return trim((string) $response);
    }

    /** @return array<string, string> */
    protected function normalizeOptions(mixed $options): array
    {
        if (!is_array($options)) {
            return [];
        }

        if ($options === []) {
            return [];
        }

        if (array_keys($options) === range(0, count($options) - 1)) {
            $normalized = [];
            foreach ($options as $option) {
                $optionValue = trim((string) $option);
                if ($optionValue !== '') {
                    $normalized[$optionValue] = $optionValue;
                }
            }

            return $normalized;
        }

        $normalized = [];
        foreach ($options as $key => $value) {
            $normalizedKey = trim((string) $key);
            $normalizedValue = trim((string) $value);
            if ($normalizedKey !== '' && $normalizedValue !== '') {
                $normalized[$normalizedKey] = $normalizedValue;
            }
        }

        return $normalized;
    }

    protected function translateMaybe(string $value): string
    {
        if (Str::startsWith($value, 'messages.') && Lang::has($value)) {
            return __($value);
        }

        return $value;
    }

    protected function successValidation(): ApiResponse
    {
        return new ApiResponse(false, 'ok');
    }
}
