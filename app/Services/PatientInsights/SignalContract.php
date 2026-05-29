<?php

namespace App\Services\PatientInsights;

interface SignalContract
{
    public function key(): string;

    public function label(): string;

    public function isPremium(): bool;

    public function evaluate(SignalContext $context): ?SignalResult;
}
