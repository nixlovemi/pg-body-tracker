<?php

namespace App\Services\PatientInsights;

class SignalResult
{
    /** @var string */
    private $key;

    /** @var string */
    private $label;

    /** @var string */
    private $level;

    /** @var int */
    private $riskPoints;

    /** @var int */
    private $maxRiskPoints;

    /** @var bool */
    private $premium;

    /** @var string */
    private $message;

    /** @var mixed */
    private $value;

    /** @var array<string, mixed> */
    private $meta;

    /**
     * @param mixed $value
     * @param array<string, mixed> $meta
     */
    public function __construct(
        string $key,
        string $label,
        string $level,
        int $riskPoints,
        int $maxRiskPoints,
        bool $premium,
        string $message,
        $value = null,
        array $meta = []
    ) {
        $this->key = $key;
        $this->label = $label;
        $this->level = $level;
        $this->riskPoints = $riskPoints;
        $this->maxRiskPoints = $maxRiskPoints;
        $this->premium = $premium;
        $this->message = $message;
        $this->value = $value;
        $this->meta = $meta;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getRiskPoints(): int
    {
        return $this->riskPoints;
    }

    public function getMaxRiskPoints(): int
    {
        return $this->maxRiskPoints;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'level' => $this->level,
            'risk_points' => $this->riskPoints,
            'max_risk_points' => $this->maxRiskPoints,
            'is_premium' => $this->premium,
            'message' => $this->message,
            'value' => $this->value,
            'meta' => $this->meta,
        ];
    }
}
