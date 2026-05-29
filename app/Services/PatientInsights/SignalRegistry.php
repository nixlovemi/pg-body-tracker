<?php

namespace App\Services\PatientInsights;

class SignalRegistry
{
    /** @var array<int, SignalContract> */
    private $signals = [];

    /**
     * @param array<int, string|SignalContract>|null $signalList
     */
    public function __construct(?array $signalList = null)
    {
        $configuredSignals = $signalList ?? (array) config('patient_insights.signals', []);

        foreach ($configuredSignals as $signalDef) {
            $signal = $this->buildSignal($signalDef);
            if (!$signal) {
                continue;
            }

            $this->signals[] = $signal;
        }
    }

    /**
     * @return array<int, SignalContract>
     */
    public function all(): array
    {
        return $this->signals;
    }

    /**
     * @param string|SignalContract $signalDef
     */
    private function buildSignal($signalDef): ?SignalContract
    {
        if ($signalDef instanceof SignalContract) {
            return $signalDef;
        }

        if (!is_string($signalDef) || !class_exists($signalDef)) {
            return null;
        }

        $signal = app($signalDef);
        return $signal instanceof SignalContract ? $signal : null;
    }
}
