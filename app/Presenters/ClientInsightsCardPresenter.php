<?php

namespace App\Presenters;

use App\Enums\PatientEvolutionStatus;

final class ClientInsightsCardPresenter
{
    /**
     * @param array<string, mixed>|null $card
     * @param bool $isPremiumPlan
     * @return array<string, mixed>|null
     */
    public static function present(?array $card, bool $isPremiumPlan): ?array
    {
        if (!$card) {
            return null;
        }

        $status = (string) ($card['status'] ?? '');
        $confidencePercent = (float) ($card['confidence_percent'] ?? 0.0);
        $isLowConfidence = (bool) ($card['is_low_confidence'] ?? false);
        $hasAnalysisData = $confidencePercent > 0;
        $isFreePlan = !$isPremiumPlan;

        $reasons = is_array($card['reasons'] ?? null) ? $card['reasons'] : [];
        $history = is_array($card['history'] ?? null) ? $card['history'] : [];
        $suggestions = self::buildSuggestions($status, $isLowConfidence, $hasAnalysisData, $isPremiumPlan);
        $cta = self::buildFreeCta($status, $isLowConfidence, $hasAnalysisData);

        return [
            'card_title' => $isPremiumPlan
                ? (string) __('messages.pages.client.register.cardInsightsPremium')
                : (string) __('messages.pages.client.register.cardInsights'),
            'status' => $status,
            'status_label' => (string) ($card['status_label'] ?? '-'),
            'status_badge_class' => self::statusBadgeClass($status),
            'risk_percent_display' => self::formatPercent((float) ($card['risk_percent'] ?? 0.0)),
            'analysis_coverage_display' => self::formatPercent($confidencePercent),
            'min_confidence_display' => self::formatPercent((float) ($card['min_confidence_percent'] ?? 60.0)),
            'show_risk_percent' => $hasAnalysisData && !$isLowConfidence,
            'show_analysis_coverage' => $hasAnalysisData,
            'show_low_confidence_alert' => $isLowConfidence,
            'show_reasons' => $isPremiumPlan && $hasAnalysisData && count($reasons) > 0,
            'show_history' => $isPremiumPlan && count($history) > 0,
            'reasons' => self::presentReasons($reasons),
            'history' => self::presentHistory($history),
            'show_suggestions' => $isPremiumPlan && count($suggestions) > 0,
            'suggestions_title' => (string) __('messages.pages.client.register.insightsSuggestionsTitle'),
            'suggestions' => $suggestions,
            'show_cta' => $isFreePlan,
            'cta_title' => (string) ($cta['title'] ?? __('messages.pages.client.register.insightsCtaTitle')),
            'cta_body' => (string) ($cta['body'] ?? __('messages.pages.client.register.insightsCtaBody')),
            'cta_button_label' => (string) __('messages.pages.client.register.insightsCtaButton'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function buildSuggestions(string $status, bool $isLowConfidence, bool $hasAnalysisData, bool $isPremiumPlan): array
    {
        if (!$hasAnalysisData || $isLowConfidence) {
            $base = [
                (string) __('messages.pages.client.register.insightsSuggestionLowData1'),
                (string) __('messages.pages.client.register.insightsSuggestionLowData2'),
            ];

            if (!$isPremiumPlan) {
                $base[] = (string) __('messages.pages.client.register.insightsSuggestionLowDataFreeExtra');
            }

            return $base;
        }

        if ($status === PatientEvolutionStatus::RISK_OF_ABANDONMENT) {
            return [
                (string) __('messages.pages.client.register.insightsSuggestionRisk1'),
                (string) __('messages.pages.client.register.insightsSuggestionRisk2'),
                (string) __('messages.pages.client.register.insightsSuggestionRisk3'),
            ];
        }

        if ($status === PatientEvolutionStatus::STABLE_ATTENTION) {
            return [
                (string) __('messages.pages.client.register.insightsSuggestionAttention1'),
                (string) __('messages.pages.client.register.insightsSuggestionAttention2'),
                (string) __('messages.pages.client.register.insightsSuggestionAttention3'),
            ];
        }

        return [
            (string) __('messages.pages.client.register.insightsSuggestionWell1'),
            (string) __('messages.pages.client.register.insightsSuggestionWell2'),
            (string) __('messages.pages.client.register.insightsSuggestionWell3'),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function buildFreeCta(string $status, bool $isLowConfidence, bool $hasAnalysisData): array
    {
        if (!$hasAnalysisData || $isLowConfidence) {
            return [
                'title' => (string) __('messages.pages.client.register.insightsCtaTitleLowData'),
                'body' => (string) __('messages.pages.client.register.insightsCtaBodyLowData'),
            ];
        }

        if ($status === PatientEvolutionStatus::RISK_OF_ABANDONMENT) {
            return [
                'title' => (string) __('messages.pages.client.register.insightsCtaTitleRisk'),
                'body' => (string) __('messages.pages.client.register.insightsCtaBodyRisk'),
            ];
        }

        if ($status === PatientEvolutionStatus::STABLE_ATTENTION) {
            return [
                'title' => (string) __('messages.pages.client.register.insightsCtaTitleAttention'),
                'body' => (string) __('messages.pages.client.register.insightsCtaBodyAttention'),
            ];
        }

        return [
            'title' => (string) __('messages.pages.client.register.insightsCtaTitleWell'),
            'body' => (string) __('messages.pages.client.register.insightsCtaBodyWell'),
        ];
    }

    private static function statusBadgeClass(string $status): string
    {
        if ($status === PatientEvolutionStatus::EVOLVING_WELL) {
            return 'success';
        }

        if ($status === PatientEvolutionStatus::STABLE_ATTENTION) {
            return 'warning';
        }

        if ($status === PatientEvolutionStatus::RISK_OF_ABANDONMENT) {
            return 'danger';
        }

        return 'secondary';
    }

    /**
     * @param array<int, array<string, mixed>> $reasons
     * @return array<int, array<string, mixed>>
     */
    private static function presentReasons(array $reasons): array
    {
        return array_map(function (array $reason): array {
            $indicatorText = (string) ($reason['indicator_text'] ?? '');

            return [
                'label' => (string) ($reason['label'] ?? '-'),
                'message' => (string) ($reason['message'] ?? ''),
                'indicator_text' => $indicatorText,
                'has_indicator_text' => trim($indicatorText) !== '',
            ];
        }, $reasons);
    }

    /**
     * @param array<int, array<string, mixed>> $history
     * @return array<int, array<string, mixed>>
     */
    private static function presentHistory(array $history): array
    {
        return array_map(function (array $item): array {
            $status = (string) ($item['status'] ?? '');
            $dateLabel = (string) ($item['snapshot_date_label'] ?? '-');
            $showRisk = (bool) ($item['should_show_risk_percent'] ?? false);

            $tooltipSuffix = $showRisk
                ? self::formatPercent((float) ($item['risk_percent'] ?? 0.0)) . '%'
                : (string) __('messages.pages.client.register.insightsHistoryTooltipLimitedData');

            return [
                'status_label' => (string) ($item['status_label'] ?? '-'),
                'snapshot_date_label' => $dateLabel,
                'badge_class' => self::statusBadgeClass($status),
                'tooltip' => $dateLabel . ' - ' . $tooltipSuffix,
            ];
        }, $history);
    }

    private static function formatPercent(float $value): string
    {
        return number_format(
            $value,
            1,
            (string) __('messages.decimalSeparator'),
            (string) __('messages.thousandSeparator')
        );
    }
}
