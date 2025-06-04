<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;

class EvolutionRanking extends ReportAbstract
{
    public function premiumOnly(): bool
    {
        return true;
    }

    public function getIcon(): string
    {
        return Icons::TROPHY;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.EvolutionRanking.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.EvolutionRanking.description');
    }

    public function getModel(): Model
    {
        return new Client();
    }

    public function applyFilter(Builder &$query)
    {
        $User = SysUtils::getLoggedInUser();
        $userId = $User->id;

        $query->where('user_id', $userId)
            ->whereHas('avaliations', fn ($query) => $query, '>=', 2)
            ->with(['avaliations' => fn ($query) => $query->orderBy('date', 'asc')])
            ->get()
            ->filter(fn ($client) => $client->avaliations->count() >= 2)
            ->sortByDesc('evolution_ranking_score');
    }

    /**
     * Returns the columns to be displayed in the report.
     *
     * @return array[Okipa\LaravelTable\Column]
     */
    public function getColumns(): array
    {
        return [
            Column::make('fullName')
                ->title(__('messages.models.Client.name'))
                ->format(function(Model $Model) {
                    return $Model->getName();
                }),

            Column::make('avaliations_count')
                ->title(__('messages.menu.avaliation'))
                ->format(function(Model $Model) {
                    return $Model->avaliations->count();
                }),

            Column::make('muscle_gain')
                ->title(__('messages.pages.report.EvolutionRanking.columns.muscleGain'))
                ->format(function(Model $Model) {
                    return SysUtils::getFormattedDeltaText($Model->getEvolutionRankingMuscleGainAttribute(), '%');
                }),

            Column::make('fat_loss')
                ->title(__('messages.pages.report.EvolutionRanking.columns.fatLoss'))
                ->format(function(Model $Model) {
                    return SysUtils::getFormattedDeltaText($Model->getEvolutionRankingFatLossAttribute(), '%');
                }),

            Column::make('score')
                ->title(__('messages.pages.report.EvolutionRanking.columns.score'))
                ->format(function(Model $Model) {
                    return SysUtils::getFormattedDeltaText($Model->getEvolutionRankingScoreAttribute(), '%');
                }),

            Column::make('last_avaliation')
                ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
                ->format(function(Model $Model) {
                    return $Model->getLastAvaliation()->getFormattedDate();
                }),
        ];
    }
}
