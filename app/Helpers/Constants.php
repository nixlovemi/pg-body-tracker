<?php

namespace App\Helpers;

final class Constants {
    public const USER_DEFAULT_IMAGE_PATH = '/images/no-user.jpg';
    public const REGEX_PHONE_NUMBER = '/(?=.*[0-9])[- +()0-9]+/';
    public const WHATS_LINK_URL = 'https://api.whatsapp.com/send?phone=%s&text=%s';
    public const FORM_ADD = 'add';
    public const FORM_EDIT = 'edit';
    public const FORM_VIEW = 'view';
    public const FORM_ACTIONS = [
        self::FORM_ADD,
        self::FORM_EDIT,
        self::FORM_VIEW,
    ];
    public const RETURN_INT_CANT_CALCULATE = -999;

    public const GRAPH_COLOR_LIGHT_PINK = 'rgba(255, 179, 186, 0.7)';
    public const GRAPH_COLOR_PEACH = 'rgba(255, 223, 186, 0.7)';
    public const GRAPH_COLOR_YELLOW = 'rgba(255, 255, 186, 0.7)';
    public const GRAPH_COLOR_MINT = 'rgba(186, 255, 201, 0.7)';
    public const GRAPH_COLOR_LIGHT_BLUE = 'rgba(186, 225, 255, 0.7)';
    public const GRAPH_COLOR_LILAC = 'rgba(218, 186, 255, 0.7)';
    public const GRAPH_COLOR_LAVENDER = 'rgba(255, 186, 250, 0.7)';
    public const GRAPH_COLOR_LIGHT_GREEN = 'rgba(204, 255, 229, 0.7)';

    public const RANK_COLOR_DEFAULT = '#858796';
    public const RANK_COLOR_1 = '#8BC34A';
    public const RANK_COLOR_2 = '#4CAF50';
    public const RANK_COLOR_3 = '#CDDC39';
    public const RANK_COLOR_4 = '#FFEB3B';
    public const RANK_COLOR_5 = '#FFC107';
    public const RANK_COLOR_6 = '#FF9800';
    public const RANK_COLOR_7 = '#F44336';
    public const RANK_COLOR_8 = '#B71C1C';
    public const RANK_COLORS = [
        self::RANK_COLOR_1,
        self::RANK_COLOR_2,
        self::RANK_COLOR_3,
        self::RANK_COLOR_4,
        self::RANK_COLOR_5,
        self::RANK_COLOR_6,
        self::RANK_COLOR_7,
        self::RANK_COLOR_8,
    ];

    public const FI_FIELD_LABEL = 'fieldLabel';
    public const FI_FIELD_VALUE = 'fieldValue';
    public const FI_FIELD_SUFFIX = 'fieldSuffix';
    public const FI_RANK = 'rank';
    public const FI_RANK_IDX = 'rankIdx';
    public const FI_RANK_LABEL = 'rankLabel';
    public const FI_RANK_COLOR = 'rankColor';
    public const FI_IDEAL_MIN = 'idealMin';
    public const FI_IDEAL_MAX = 'idealMax';
    public const FI_IDEAL_LABEL = 'idealLabel';

    public static function getRankBarInfo(): array {
        return self::getRankings();
    }

    public static function getRankings(
        array $showOnlyIndexes=[],
        bool $removeNbrFromLabel=false,
    ): array {
        $rankings = [];
        foreach (self::RANK_COLORS as $index => $color) {
            if (count($showOnlyIndexes) > 0 && !in_array($index, $showOnlyIndexes)) {
                continue;
            }

            $rank = $index + 1;
            $label = __("messages.components.avaliationReport.rankBarLabel{$rank}");
            if ($removeNbrFromLabel) {
                $label = preg_replace('/\d+/', '', $label);
            }
            $labelMin = __("messages.components.avaliationReport.rankBarLabelMin{$rank}");
            if ($removeNbrFromLabel) {
                $labelMin = preg_replace('/\d+/', '', $labelMin);
            }

            $rankings[] = [
                'color' => $color,
                'rank' => $rank,
                'label' => $label,
                'labelMin' => $labelMin,
            ];
        }
        return $rankings;
    }
}
