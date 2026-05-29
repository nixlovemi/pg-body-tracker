<?php

namespace App\Services\PatientInsights;

use App\Helpers\SysUtils;
use App\Models\Client;
use App\Models\PatientSignalSnapshot;
use Carbon\Carbon;

class PatientInsightsSnapshotService
{
    /**
     * @return array<string, int|string>
     */
    public function snapshotDaily(?Carbon $now = null, bool $persist = true): array
    {
        $contextNow = $this->resolveNow($now);
        $snapshotDate = $contextNow->copy()->format('Y-m-d');

        $totals = [
            'snapshot_date' => $snapshotDate,
            'clients_scanned' => 0,
            'snapshots_evaluated' => 0,
            'snapshots_written' => 0,
            'errors' => 0,
        ];

        Client::query()
            ->select('id')
            ->orderBy('id', 'asc')
            ->chunkById(200, function ($clients) use ($contextNow, $snapshotDate, $persist, &$totals) {
                foreach ($clients as $clientRef) {
                    $totals['clients_scanned']++;

                    try {
                        $client = Client::query()->find($clientRef->id);
                        if (!$client) {
                            continue;
                        }

                        $card = $this->buildLiveFreeCardData($client, $contextNow);
                        if ($persist) {
                            PatientSignalSnapshot::query()->updateOrCreate(
                                [
                                    'client_id' => $client->id,
                                    'snapshot_date' => $snapshotDate,
                                ],
                                [
                                    'status' => (string) $card['status'],
                                    'risk_percent' => (float) $card['risk_percent'],
                                    'signal_count' => (int) $card['signal_count'],
                                    'summary_json' => (array) $card['summary'],
                                    'reasons_json' => (array) $card['reasons'],
                                ]
                            );

                            $totals['snapshots_written']++;
                        }

                        $totals['snapshots_evaluated']++;
                    } catch (\Throwable $th) {
                        $totals['errors']++;
                    }
                }
            });

        return $totals;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildFreeCard(Client $client, ?Carbon $now = null, int $historyDays = 14): array
    {
        return $this->buildCard($client, false, $now, $historyDays);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPremiumCard(Client $client, ?Carbon $now = null, int $historyDays = 14): array
    {
        return $this->buildCard($client, true, $now, $historyDays);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCard(Client $client, bool $includePremiumSignals, ?Carbon $now = null, int $historyDays = 14): array
    {
        $contextNow = $this->resolveNow($now);
        $live = $this->buildLiveCardData($client, $contextNow, $includePremiumSignals);

        $historyDays = (int) $this->historyCfg('window_days', $historyDays);
        $startDate = $contextNow->copy()->subDays(max(0, $historyDays))->format('Y-m-d');
        $history = PatientSignalSnapshot::query()
            ->where('client_id', $client->id)
            ->where('snapshot_date', '>=', $startDate)
            ->orderBy('snapshot_date', 'desc')
            ->get(['snapshot_date', 'status', 'risk_percent', 'summary_json'])
            ->map(function (PatientSignalSnapshot $snapshot) {
                $dateYmd = $snapshot->snapshot_date?->format('Y-m-d') ?: null;
                $summary = (array) ($snapshot->summary_json ?? []);
                $confidencePercent = isset($summary['confidence_percent']) && is_numeric($summary['confidence_percent'])
                    ? (float) $summary['confidence_percent']
                    : null;

                $isLowConfidence = array_key_exists('is_low_confidence', $summary)
                    ? (bool) $summary['is_low_confidence']
                    : ($confidencePercent !== null && $confidencePercent < $this->minConfidencePercent());

                $shouldShowRiskPercent = !$isLowConfidence && ($confidencePercent === null || $confidencePercent > 0.0);

                return [
                    'snapshot_date' => $dateYmd,
                    'snapshot_date_label' => $dateYmd ? SysUtils::reformatDate($dateYmd, 'Y-m-d', __('messages.dateFormat')) : null,
                    'status' => (string) $snapshot->status,
                    'status_label' => $this->statusLabel((string) $snapshot->status),
                    'risk_percent' => (float) $snapshot->risk_percent,
                    'confidence_percent' => $confidencePercent,
                    'is_low_confidence' => $isLowConfidence,
                    'should_show_risk_percent' => $shouldShowRiskPercent,
                ];
            })
            ->values()
            ->all();

        $todayYmd = $contextNow->format('Y-m-d');
        $hasTodaySnapshot = false;
        foreach ($history as $item) {
            if (($item['snapshot_date'] ?? null) === $todayYmd) {
                $hasTodaySnapshot = true;
                break;
            }
        }

        if (!$hasTodaySnapshot) {
            $liveConfidencePercent = (float) ($live['summary']['confidence_percent'] ?? 0.0);
            $liveIsLowConfidence = (bool) ($live['summary']['is_low_confidence'] ?? false);

            array_unshift($history, [
                'snapshot_date' => $todayYmd,
                'snapshot_date_label' => SysUtils::reformatDate($todayYmd, 'Y-m-d', __('messages.dateFormat')),
                'status' => (string) $live['status'],
                'status_label' => $this->statusLabel((string) $live['status']),
                'risk_percent' => (float) $live['risk_percent'],
                'confidence_percent' => $liveConfidencePercent,
                'is_low_confidence' => $liveIsLowConfidence,
                'should_show_risk_percent' => !$liveIsLowConfidence && $liveConfidencePercent > 0.0,
            ]);
        }

        $historyMode = (string) $this->historyCfg('mode', 'weekly');
        if ($historyMode === 'weekly') {
            $history = $this->reduceHistoryWeekly($history);
        }

        $maxItems = max(1, (int) $this->historyCfg('max_items', 8));
        $history = array_slice($history, 0, $maxItems);

        return [
            'status' => (string) $live['status'],
            'status_label' => $this->statusLabel((string) $live['status']),
            'risk_percent' => (float) $live['risk_percent'],
            'signal_count' => (int) $live['signal_count'],
            'confidence_percent' => (float) ($live['summary']['confidence_percent'] ?? 0.0),
            'is_low_confidence' => (bool) ($live['summary']['is_low_confidence'] ?? false),
            'min_confidence_percent' => (float) ($live['summary']['min_confidence_percent'] ?? 60.0),
            'reasons' => (array) $live['reasons'],
            'history' => $history,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLiveFreeCardData(Client $client, Carbon $now): array
    {
        return $this->buildLiveCardData($client, $now, false);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLiveCardData(Client $client, Carbon $now, bool $includePremiumSignals): array
    {
        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), $includePremiumSignals, $now);
        $summary = (array) ($result['summary'] ?? []);
        $signals = (array) ($result['signals'] ?? []);

        return [
            'status' => (string) ($summary['status'] ?? ''),
            'risk_percent' => (float) ($summary['risk_percent'] ?? 0.0),
            'signal_count' => (int) ($summary['signal_count'] ?? count($signals)),
            'summary' => $summary,
            'reasons' => $this->topReasons($signals),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $signals
     * @return array<int, array<string, mixed>>
     */
    private function topReasons(array $signals, int $limit = 3): array
    {
        $signalsByKey = [];
        foreach ($signals as $signal) {
            $signalsByKey[(string) ($signal['key'] ?? '')] = $signal;
        }

        usort($signals, function (array $a, array $b): int {
            $aRisk = (int) ($a['risk_points'] ?? 0);
            $bRisk = (int) ($b['risk_points'] ?? 0);
            if ($aRisk === $bRisk) {
                return strcmp((string) ($a['key'] ?? ''), (string) ($b['key'] ?? ''));
            }

            return $bRisk <=> $aRisk;
        });

        $top = array_slice($signals, 0, max(0, $limit));

        $alignedVariability = $signalsByKey['weight_variability_7d'] ?? null;
        $isAlignedVariability = is_array($alignedVariability)
            && (bool) ((array) ($alignedVariability['meta'] ?? [])['goal_aligned'] ?? false);

        if ($isAlignedVariability && $limit > 0) {
            $alreadyIncluded = false;
            foreach ($top as $signal) {
                if ((string) ($signal['key'] ?? '') === 'weight_variability_7d') {
                    $alreadyIncluded = true;
                    break;
                }
            }

            if (!$alreadyIncluded) {
                $top[count($top) - 1] = $alignedVariability;
            }
        }

        return array_map(function (array $signal): array {
            $meta = (array) ($signal['meta'] ?? []);
            $indicatorText = (string) ($meta['indicator_text'] ?? '');

            return [
                'key' => (string) ($signal['key'] ?? ''),
                'label' => (string) ($signal['label'] ?? ''),
                'level' => (string) ($signal['level'] ?? ''),
                'risk_points' => (int) ($signal['risk_points'] ?? 0),
                'message' => (string) ($signal['message'] ?? ''),
                'indicator_text' => $indicatorText,
            ];
        }, $top);
    }

    /**
     * @param array<int, array<string, mixed>> $history
     * @return array<int, array<string, mixed>>
     */
    private function reduceHistoryWeekly(array $history): array
    {
        $weekly = [];

        foreach ($history as $item) {
            $dateYmd = (string) ($item['snapshot_date'] ?? '');
            if ($dateYmd === '') {
                continue;
            }

            $weekKey = Carbon::parse($dateYmd)->format('o-W');
            if (array_key_exists($weekKey, $weekly)) {
                continue;
            }

            $weekly[$weekKey] = $item;
        }

        return array_values($weekly);
    }

    private function historyCfg(string $key, $default = null)
    {
        $cfg = (array) config('patient_insights.free_card.history', []);
        return array_key_exists($key, $cfg) ? $cfg[$key] : $default;
    }

    private function minConfidencePercent(): float
    {
        return (float) config('patient_insights.summary.min_confidence_percent', 60.0);
    }

    private function statusLabel(string $status): string
    {
        $key = 'messages.pages.client.register.insightsStatus.' . $status;
        $label = (string) __($key);

        if ($label === $key) {
            return $status;
        }

        return $label;
    }

    private function resolveNow(?Carbon $now): Carbon
    {
        return $now ? $now->copy() : now()->setTimezone(env('APP_TIME_ZONE'));
    }
}
