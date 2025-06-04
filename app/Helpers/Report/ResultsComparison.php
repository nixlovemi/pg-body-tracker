<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;

class ResultsComparison extends ReportAbstract
{
    public function premiumOnly(): bool
    {
        return true;
    }

    public function getIcon(): string
    {
        return Icons::EXCHANGE_ALT;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.ResultsComparison.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.ResultsComparison.description');
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
            ->whereHas('avaliations', function ($query) {}, '>=', 2)
            ->with(['avaliations' => function ($query) {
                $query->orderBy('date', 'asc');
            }])
            ->orderByRaw("CONCAT(first_name, ' ', last_name)");
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

            Column::make('avaliationDate1')
                ->title(__('messages.models.Avaliation.fields.date') . ' 1')
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    return $avaliation1->getFormattedDate();
                }),

            Column::make('avaliationDate2')
                ->title(__('messages.models.Avaliation.fields.date') . ' 2')
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    return $avaliation2->getFormattedDate();
                }),

            Column::make('deltaWeight')
                ->title(__('messages.pages.report.ResultsComparison.columns.deltaWeight'))
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    $deltaWeight = $avaliation2->weight_kg - $avaliation1->weight_kg;
                    return SysUtils::getFormattedDeltaText($deltaWeight, 'kg');
                }),

            Column::make('deltaBodyFat')
                ->title(__('messages.pages.report.ResultsComparison.columns.deltaBodyFat'))
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    $deltaBodyFat = $avaliation2->getBodyFatPerc() - $avaliation1->getBodyFatPerc();
                    return SysUtils::getFormattedDeltaText($deltaBodyFat, '%');
                }),

            Column::make('deltaMuscleMass')
                ->title(__('messages.models.Avaliation.fields.muscle_mass_perc'))
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    $deltaMMass = $avaliation2->getMuscleMassPerc() - $avaliation1->getMuscleMassPerc();
                    return SysUtils::getFormattedDeltaText($deltaMMass, '%');
                }),

            Column::make('deltaSkeletalMuscle')
                ->title(__('messages.components.avaliationReport.skeletalMuscle'))
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    $deltaSkeletal = $avaliation2->getSkeletalMuscleMassPerc() - $avaliation1->getSkeletalMuscleMassPerc();
                    return SysUtils::getFormattedDeltaText($deltaSkeletal, '%');
                }),

            Column::make('deltaBMI')
                ->title(__('messages.components.avaliationReport.bmi'))
                ->format(function(Model $Model) {
                    [$avaliation2, $avaliation1] = $this->getMostRecentAvaliations($Model);
                    $deltaBmi = $avaliation2->getBmi() - $avaliation1->getBmi();
                    return SysUtils::getFormattedDeltaText($deltaBmi, '');
                }),
        ];
    }

    private function getMostRecentAvaliations(Model $Model): array
    {
        return $Model->getTwoLastAvaliations();
    }
}
