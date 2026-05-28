<?php

namespace App\Helpers\CheckinFields;

use App\DTO\Checkin\CheckinFieldConfigDTO;
use App\Helpers\ApiResponse;
use App\Models\Avaliation;

interface CheckinFieldContract
{
    public function getFieldType(): string;

    public function getFieldTypeLabel(): string;

    public function getDisplayLabel(): string;

    public function showInConfigForm(): bool;

    public function getFollowupInputView(): string;

    /** @return array<string, string> */
    public function getFormOptions(): array;

    public function supportsOptions(): bool;

    public function normalizeFieldConfigDTO(CheckinFieldConfigDTO $fieldConfig): CheckinFieldConfigDTO;

    /** @param array<string, mixed> $field */
    public function normalizeFieldConfig(array $field): array;

    public function getFieldKey(): string;

    public function getResponseType(): string;

    public function validateResponse(mixed $value): ApiResponse;

    public function normalizeResponse(mixed $value): mixed;

    public function formatResponseForDisplay(mixed $response): string;

    public function applyToAvaliation(Avaliation $avaliation, mixed $normalizedValue): void;

    public function getFieldMeta(): array;
}
