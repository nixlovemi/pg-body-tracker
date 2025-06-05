<?php

namespace App\Helpers\Report;

use Okipa\LaravelTable\Column;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\SysUtils;

abstract class ReportColumns
{
    public static function clientFullName(): Column
    {
        return Column::make('full_name')
            ->title(__('messages.models.Client.name'))
            ->format(function(Model $Model) {
                return $Model->getName();
            });
    }

    public static function clientCreatedAt(): Column
    {
        return Column::make('created_at')
            ->title(__('messages.pages.report.ClientsWithoutGoals.columns.createdAt'))
            ->format(function(Model $Model) {
                return $Model->getFormattedCreatedAt();
            });
    }

    public static function clientGender(): Column
    {
        return Column::make('gender')
            ->title(__('messages.models.Client.fields.gender'))
            ->format(function(Model $Model) {
                return $Model->getGenderStr();
            });
    }

    public static function clientAge(): Column
    {
        return Column::make('age')
            ->title(__('messages.models.Client.fields.age'))
            ->format(function(Model $Model) {
                return $Model->getAge();
            });
    }

    public static function clientEmail(): Column
    {
        return Column::make('email')
            ->title(__('messages.pages.client.table.colEmail'))
            ->format(function(Model $Model) {
                return $Model->email;
            });
    }

    public static function clientPhone(): Column
    {
        return Column::make('phone')
            ->title(__('messages.pages.client.table.colPhone'))
            ->format(function(Model $Model) {
                return $Model->phone;
            });
    }

    public static function clientHeight(): Column
    {
        return Column::make('height')
            ->title(__('messages.models.Client.fields.height'))
            ->format(function(Model $Model) {
                return $Model->getFormattedHeight();
            });
    }

    public static function clientWeight(): Column
    {
        return Column::make('weight')
            ->title(__('messages.models.Client.fields.weight'))
            ->format(function(Model $Model) {
                return $Model->getFormattedCurrentWeight();
            });
    }

    public static function clientBmi(): Column
    {
        return Column::make('bmi')
            ->title(__('messages.components.avaliationReport.bmi'))
            ->format(function(Model $Model) {
                $avaliation = $Model->getLastAvaliation();
                return $avaliation?->getFormattedBmi() ?? '';
            });
    }

    public static function clientFirstAvaliationDate(): Column
    {
        return Column::make('first_avaliation_date')
            ->title(__('messages.pages.report.ClientProgress.columns.firstAvaliationDate'))
            ->format(function(Model $Model) {
                $first = $Model->getFirstAvaliation();
                if (!$first) {
                    return '';
                }

                return $first->getFormattedDate();
            });
    }

    public static function clientLastAvaliationDate(): Column
    {
        return Column::make('last_avaliation_date')
            ->title(__('messages.pages.report.ClientProgress.columns.lastAvaliationDate'))
            ->format(function(Model $Model) {
                $last = $Model->getLastAvaliation();
                if (!$last) {
                    return '';
                }

                return $last->getFormattedDate();
            });
    }

    public static function clientLastAvaliationDaysToNow(): Column
    {
        return Column::make('dueDays')
            ->title(__('messages.pages.report.OverdueEvaluations.columns.daysOverdue'))
            ->format(function(Model $Model) {
                $lastAvaliation = $Model->getLastAvaliation();
                $daysOverdue = SysUtils::applyTimezone($lastAvaliation->date)->diffInDays(now());
                return '<div class="text-center">' . $daysOverdue . '</div>';
            });
    }

    public static function clientWeightFirstLast(): Column
    {
        return Column::make('weight_first_last')
            ->title(__('messages.pages.report.ClientProgress.columns.weightFirstLast'))
            ->format(function(Model $Model) {
                $first = $Model->getFirstAvaliation();
                $last = $Model->getLastAvaliation();
                if (!$first || !$last) {
                    return '';
                }

                return sprintf('%s -> %s', $first->getFormattedWeight(), $last->getFormattedWeight());
            });
    }

    public static function clientBodyFatFirstLast(): Column
    {
        return Column::make('body_fat_perc_first_last')
            ->title(__('messages.pages.report.ClientProgress.columns.bodyFatPercFirstLast'))
            ->format(function(Model $Model) {
                $first = $Model->getFirstAvaliation();
                $last = $Model->getLastAvaliation();
                if (!$first || !$last) {
                    return '';
                }

                return sprintf('%s -> %s', $first->getFormattedBodyFat(), $last->getFormattedBodyFat());
            });
    }

    public static function clientMuscleMassPercFirstLast(): Column
    {
        return Column::make('muscle_mass_perc_first_last')
            ->title(__('messages.pages.report.ClientProgress.columns.muscleMassPercFirstLast'))
            ->format(function(Model $Model) {
                $first = $Model->getFirstAvaliation();
                $last = $Model->getLastAvaliation();
                if (!$first || !$last) {
                    return '';
                }

                return sprintf('%s -> %s', $first->getFormattedMuscleMassPerc(), $last->getFormattedMuscleMassPerc());
            });
    }

    public static function clientBasalMetabolismFirstLast(): Column
    {
        return Column::make('basal_metabolism_first_last')
            ->title(__('messages.pages.report.ClientProgress.columns.basalMetabolismFirstLast'))
            ->format(function(Model $Model) {
                $first = $Model->getFirstAvaliation();
                $last = $Model->getLastAvaliation();
                if (!$first || !$last) {
                    return '';
                }

                return sprintf('%s -> %s', $first->getFormattedTmb(), $last->getFormattedTmb());
            });
    }

    public static function clientAvaliationsCount(): Column
    {
        return Column::make('avaliation_count')
            ->title(__('messages.pages.report.ClientProgress.columns.avaliationNbr'))
            ->format(function(Model $Model) {
                return '<div class="text-center">' . $Model->avaliations->count() . '</div>';
            });
    }

    public static function clientLastBodyFat(): Column
    {
        return Column::make('last_body_fat')
            ->title(__('messages.components.avaliationReport.bodyFat'))
            ->format(function(Model $Model) {
                $avaliation = $Model->getLastAvaliation();
                return $avaliation?->getFormattedBodyFat() ?? '';
            });
    }

    public static function clientAvgDaysBtwAvaliations(): Column
    {
        return Column::make('avg_days_btw_evaluations')
            ->title(__('messages.pages.report.EvaluationFrequency.columns.avgDaysBtwEvaluations'))
            ->format(function(Model $Model) {
                return $Model->getAvgDaysBtwAvaliations();
            });
    }

    public static function clientMuscleGainSinceStart(): Column
    {
        return Column::make('muscle_gain')
            ->title(__('messages.pages.report.EvolutionRanking.columns.muscleGain'))
            ->format(function(Model $Model) {
                return SysUtils::getFormattedDeltaText($Model->getEvolutionRankingMuscleGainAttribute(), '%');
            });
    }

    public static function clientFatLossSinceStart(): Column
    {
        return Column::make('fat_loss')
            ->title(__('messages.pages.report.EvolutionRanking.columns.fatLoss'))
            ->format(function(Model $Model) {
                return SysUtils::getFormattedDeltaText($Model->getEvolutionRankingFatLossAttribute(), '%');
            });
    }

    public static function clientEvolutionScoreSinceStart(): Column
    {
        return Column::make('score')
            ->title(__('messages.pages.report.EvolutionRanking.columns.score'))
            ->format(function(Model $Model) {
                return SysUtils::getFormattedDeltaText($Model->getEvolutionRankingScoreAttribute(), '%');
            });
    }

    public static function clientCurrentGoalName(): Column
    {
        return Column::make('goal')
            ->title(__('messages.models.Goal.name'))
            ->format(function(Model $Model) {
                $Goal = $Model->getCurrentGoal();
                if (!$Goal) {
                    return '';
                }

                return $Goal->getObjectivieString();
            });
    }

    public static function clientCurrentGoalInitialWeight(): Column
    {
        return Column::make('initial_weight')
            ->title(__('messages.models.Goal.fields.initial_weight'))
            ->format(function(Model $Model) {
                $goal = $Model->getCurrentGoal();
                return $goal->getFormattedInitialWeight();
            });
    }

    public static function clientCurrentGoalTargetWeight(): Column
    {
        return Column::make('target_weight')
            ->title(__('messages.models.Goal.fields.target_weight'))
            ->format(function(Model $Model) {
                $goal = $Model->getCurrentGoal();
                return $goal->getFormattedTargetWeight();
            });
    }

    public static function clientCurrentGoalDeadline(): Column
    {
        return Column::make('deadline')
            ->title(__('messages.models.Goal.fields.deadline'))
            ->format(function(Model $Model) {
                $goal = $Model->getCurrentGoal();
                if (!$goal || !$goal->deadline) {
                    return '';
                }

                return SysUtils::applyTimezone($goal->deadline)->format(__('messages.dateFormat'));
            });
    }

    public static function clientCurrentProgress(): Column
    {
        return Column::make('progress')
            ->title(__('messages.pages.goal.modalAddGoal.labelProgress'))
            ->format(function(Model $Model) {
                $goal = $Model->getCurrentGoal();
                return round($goal->percentageTowardsGoal(), 2) . '%';
            });
    }

    public static function clientCurrentRemainingDays(): Column
    {
        return Column::make('remaining_days')
            ->title(__('messages.pages.goal.modalAddGoal.labelDaysToDeadline'))
            ->format(function(Model $Model) {
                $goal = $Model->getCurrentGoal();
                return '<div class="text-center">' . $goal->remainingDays() . '</div>';
            });
    }
}
