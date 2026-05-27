<?php

namespace App\Services\Checkin;

use App\DTO\Checkin\CheckinFieldConfigDTO;
use App\Helpers\CheckinFields\CheckinFieldRegistry;
use App\Helpers\CheckinFields\Fields\WeightField;
use Illuminate\Support\Str;

class CheckinFieldConfigNormalizer
{
    /**
     * @param array<int|string, mixed> $rawConfig
     * @return array<int, array<string, mixed>>
     */
    public function normalize(array $rawConfig): array
    {
        $normalized = [];
        $usedFieldKeys = [];
        $allowedCustomTypes = $this->getAllowedCustomTypes();

        foreach (array_values($rawConfig) as $index => $field) {
            if (!is_array($field)) {
                continue;
            }

            $label = trim((string) ($field['label'] ?? ''));
            $fieldKey = trim((string) ($field['field_key'] ?? ''));
            if ($fieldKey === '') {
                $fieldKey = $this->buildFieldKey($label, (int) $index);
            }

            $fieldType = trim((string) ($field['field_type'] ?? ''));
            if ($fieldKey === '' || $fieldType === '' || !isset($allowedCustomTypes[$fieldType])) {
                continue;
            }

            if ($fieldKey === WeightField::FIELD_KEY) {
                continue;
            }

            $fieldConfigDTO = CheckinFieldConfigDTO::fromArray($field)
                ->setFieldType($fieldType)
                ->setFieldKey($fieldKey)
                ->setLabel($label);

            $fieldConfig = $fieldConfigDTO->toArray();

            $Field = CheckinFieldRegistry::make($fieldType, $fieldConfig);
            if (!$Field || !$Field->showInConfigForm()) {
                continue;
            }

            $normalizedField = $Field->normalizeFieldConfigDTO($fieldConfigDTO)->toArray();
            $normalizedKey = trim((string) ($normalizedField['field_key'] ?? ''));
            if ($normalizedKey === '' || isset($usedFieldKeys[$normalizedKey])) {
                continue;
            }

            $usedFieldKeys[$normalizedKey] = true;
            $normalized[] = $normalizedField;
        }

        return $normalized;
    }

    /** @return array<string, true> */
    private function getAllowedCustomTypes(): array
    {
        $allowed = [];
        foreach (array_keys(CheckinFieldRegistry::all()) as $type) {
            $Field = CheckinFieldRegistry::make($type);
            if ($Field && $Field->showInConfigForm()) {
                $allowed[$type] = true;
            }
        }

        return $allowed;
    }

    private function buildFieldKey(string $label, int $index): string
    {
        $base = Str::slug($label, '_');
        if ($base === '') {
            $base = 'field_' . ($index + 1);
        }

        return substr($base, 0, 60);
    }
}
