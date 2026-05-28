<?php

namespace App\DTO\Checkin;

final class CheckinFieldConfigDTO
{
    private string $fieldType = '';
    private string $fieldKey = '';
    private string $label = '';
    private bool $required = false;

    /** @var array<string, string> */
    private array $options = [];

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return (new self())
            ->setFieldType((string) ($data['field_type'] ?? ''))
            ->setFieldKey((string) ($data['field_key'] ?? ''))
            ->setLabel((string) ($data['label'] ?? ''))
            ->setRequired((bool) ($data['required'] ?? false))
            ->setOptions((array) ($data['options'] ?? []));
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'field_type' => $this->fieldType,
            'field_key' => $this->fieldKey,
            'label' => $this->label,
            'required' => $this->required,
            'options' => $this->options,
        ];
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function setFieldType(string $fieldType): self
    {
        $this->fieldType = trim($fieldType);

        return $this;
    }

    public function getFieldKey(): string
    {
        return $this->fieldKey;
    }

    public function setFieldKey(string $fieldKey): self
    {
        $this->fieldKey = trim($fieldKey);

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = trim($label);

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /** @return array<string, string> */
    public function getOptions(): array
    {
        return $this->options;
    }

    /** @param array<string|int, mixed> $options */
    public function setOptions(array $options): self
    {
        $normalized = [];
        foreach ($options as $key => $value) {
            $normalized[(string) $key] = (string) $value;
        }

        $this->options = $normalized;

        return $this;
    }
}
