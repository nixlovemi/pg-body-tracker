<?php

namespace App\Services\PatientInsights;

use App\Enums\PatientEvolutionStatus;
use App\Enums\PatientSignalLevel;
use App\Models\Client;
use Carbon\Carbon;

class PatientSignalEngine
{
    /** @var SignalRegistry */
    private $registry;

    public function __construct(SignalRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return array<string, mixed>
     */
    public function evaluateForClient(Client $client, bool $includePremiumSignals = true, ?Carbon $now = null): array
    {
        $context = SignalContext::fromClient($client, $now);
        $results = [];

        foreach ($this->registry->all() as $signal) {
            if (!$includePremiumSignals && $signal->isPremium()) {
                continue;
            }

            $result = $signal->evaluate($context);
            if (!$result) {
                continue;
            }

            $results[] = $result;
        }

        return [
            'client_id' => $client->id,
            'signals' => array_map(function (SignalResult $result) {
                return $result->toArray();
            }, $results),
            'summary' => $this->buildSummary($results),
        ];
    }

    /**
     * @param array<int, SignalResult> $results
     * @return array<string, mixed>
     */
    private function buildSummary(array $results): array
    {
        if (count($results) === 0) {
            return [
                'signal_count' => 0,
                'risk_points' => 0,
                'max_risk_points' => 0,
                'risk_percent' => 0.0,
                'status' => PatientEvolutionStatus::STABLE_ATTENTION,
                'confidence_percent' => 0.0,
                'is_low_confidence' => true,
                'min_confidence_percent' => $this->minConfidencePercent(),
                'levels' => [
                    PatientSignalLevel::GOOD => 0,
                    PatientSignalLevel::INFO => 0,
                    PatientSignalLevel::ATTENTION => 0,
                    PatientSignalLevel::RISK => 0,
                ],
            ];
        }

        $riskPoints = 0;
        $maxRiskPoints = 0;
        $levels = [
            PatientSignalLevel::GOOD => 0,
            PatientSignalLevel::INFO => 0,
            PatientSignalLevel::ATTENTION => 0,
            PatientSignalLevel::RISK => 0,
        ];

        foreach ($results as $result) {
            $riskPoints += $result->getRiskPoints();
            $maxRiskPoints += $result->getMaxRiskPoints();
            $levels[$result->getLevel()] = (int) ($levels[$result->getLevel()] ?? 0) + 1;
        }

        $riskPercent = $maxRiskPoints > 0
            ? round(($riskPoints / $maxRiskPoints) * 100, 1)
            : 0.0;

        $signalCount = count($results);
        $infoCount = (int) ($levels[PatientSignalLevel::INFO] ?? 0);
        $conclusiveCount = max(0, $signalCount - $infoCount);
        $confidencePercent = $signalCount > 0
            ? round(($conclusiveCount / $signalCount) * 100, 1)
            : 0.0;

        $minConfidencePercent = $this->minConfidencePercent();
        $isLowConfidence = $confidencePercent < $minConfidencePercent;
        $status = $this->resolveStatus($riskPercent);
        if ($isLowConfidence) {
            $status = PatientEvolutionStatus::STABLE_ATTENTION;
        }

        return [
            'signal_count' => $signalCount,
            'risk_points' => $riskPoints,
            'max_risk_points' => $maxRiskPoints,
            'risk_percent' => $riskPercent,
            'status' => $status,
            'confidence_percent' => $confidencePercent,
            'is_low_confidence' => $isLowConfidence,
            'min_confidence_percent' => $minConfidencePercent,
            'levels' => $levels,
        ];
    }

    private function minConfidencePercent(): float
    {
        return (float) config('patient_insights.summary.min_confidence_percent', 60.0);
    }

    private function resolveStatus(float $riskPercent): string
    {
        if ($riskPercent >= 67.0) {
            return PatientEvolutionStatus::RISK_OF_ABANDONMENT;
        }

        if ($riskPercent >= 34.0) {
            return PatientEvolutionStatus::STABLE_ATTENTION;
        }

        return PatientEvolutionStatus::EVOLVING_WELL;
    }
}
