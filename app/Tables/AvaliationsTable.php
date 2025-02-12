<?php

namespace App\Tables;

use App\Models\Avaliation;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\RowActions\DestroyRowAction;
use App\Tables\RowActions\OpenModalRowAction;
use Okipa\LaravelTable\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;
use App\Models\User;
use App\Helpers\SysUtils;
use App\Helpers\Permissions;
use App\Helpers\Icons;
use Illuminate\Support\Facades\DB;
use App\Tables\Filters\DateRangeFilter;
use Okipa\LaravelTable\RowActions\RedirectRowAction;
use App\Helpers\Constants;

class AvaliationsTable extends AbstractTableConfiguration
{
    public int $clientId = 0;
    public bool $canEdit = false;
    private ?Client $Client;
    private User $User;

    /**
     * @throws Exception
     */
    private function init(): void
    {
        $this->User = SysUtils::getLoggedInUser();
        $this->Client = Client::find($this->clientId);
    }

    protected function table(): Table
    {
        $this->init();

        return Table::make()
            ->model(Avaliation::class)
            ->query(function(Builder $query) {
                $query->select([
                        'avaliations.*',
                        DB::raw("CONCAT(clients.first_name, ' ', clients.last_name) AS full_name")
                    ])
                    ->join('clients', 'clients.id', '=', 'avaliations.client_id')
                    ->where('clients.user_id', '=', $this->User->id);

                if ($this->Client) {
                    $query->where('avaliations.client_id', '=', $this->Client->id ?? 0);
                }

                return $query;
            })
            ->rowActions(fn(Avaliation $Avaliation) => [
                $this->getViewReportRowAction($Avaliation),
                $this->getViewRowAction($Avaliation),
                $this->getEditRowAction($Avaliation),
                $this->getDeleteRowAction($Avaliation),
            ])
            ->filters([
                new DateRangeFilter('date'),
            ]);
    }

    protected function columns(): array
    {
        $arrCols = [
            Column::make('date')
                ->sortByDefault('desc')
                ->title(__('messages.models.Avaliation.fields.date'))
                ->format(function(Avaliation $Avaliation) {
                    return SysUtils::reformatDate($Avaliation?->date, 'Y-m-d', __('messages.dateFormat'));
                })
                ->sortable(),
        ];

        if (null === $this->Client) {
            $arrCols[] = Column::make('full_name')
                ->title(__('messages.models.Client.name'))
                ->format(function(Avaliation $Avaliation) {
                    return $Avaliation->full_name;
                })
                ->sortable()
                ->searchable(function($query, string $searchBy) {
                    return $query->whereRaw("CONCAT(clients.first_name, ' ', clients.last_name) LIKE '%{$searchBy}%'");
                });
        }

        return array_merge($arrCols, [
            Column::make('')
                ->title(__('messages.models.Client.fields.weight') . ' (kg)')
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation?->weight_kg, 1, __('messages.decimalSeparator'), __('messages.thousandSeparator'));
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.fields.body_fat_perc'))
                ->format(function(Avaliation $Avaliation) {
                    if (Constants::RETURN_INT_CANT_CALCULATE == $Avaliation?->getBodyFatPerc()) {
                        return '';
                    }

                    return number_format($Avaliation?->getBodyFatPerc(), 2, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' %';
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.labelFatMass'))
                ->format(function(Avaliation $Avaliation) {
                    if (Constants::RETURN_INT_CANT_CALCULATE == $Avaliation?->getBodyFatPerc()) {
                        return '';
                    }

                    return number_format($Avaliation->getFatMassKg(), 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' kg';
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.labelLeanMass'))
                ->format(function(Avaliation $Avaliation) {
                    if (Constants::RETURN_INT_CANT_CALCULATE == $Avaliation?->getBodyFatPerc()) {
                        return '';
                    }

                    return number_format($Avaliation->getLeanMassKg(), 1, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' kg';
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.labelTmb'))
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation->getTmb(), 0, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' kcal';
                })
            ]
        );
    }

    protected function results(): array
    {
        return [
            // The table results configuration.
            // As results are optional on tables, you may delete this method if you do not use it.
        ];
    }

    private function getViewReportRowAction(Avaliation $Avaliation): RedirectRowAction
    {
        $routeName = 'app.avaliation.viewReport';

        return (new RedirectRowAction(
            route($routeName, ['codedId' => $Avaliation->codedId]),
            'VER RELATÓRIO',
            Icons::FILE_REPORT,
            ['link-info'],
            null,
            null,
            true
        ))->when(Permissions::checkPermission($routeName), $this->User);
    }

    private function getViewRowAction(Avaliation $Avaliation): OpenModalRowAction
    {
        $routeName = 'app.avaliation.htmlModalView';

        return (new OpenModalRowAction(
            __('messages.tableActionView'),
            route($routeName, ['codedId' => $Avaliation->codedId, 'json' => 1]),
            Icons::EYE
        ))->when(Permissions::checkPermission($routeName), $this->User);
    }

    private function getEditRowAction(Avaliation $Avaliation): OpenModalRowAction
    {
        $routeName = 'app.avaliation.htmlModalEdit';

        return (new OpenModalRowAction(
            __('messages.tableActionEdit'),
            route($routeName, ['codedId' => $Avaliation->codedId, 'json' => 1]),
            Icons::EDIT
        ))->when(Permissions::checkPermission($routeName,$this->User));
    }

    private function getDeleteRowAction(Avaliation $Avaliation): DestroyRowAction
    {
        return (new DestroyRowAction())
            ->when(
                $this->canEdit &&
                $this->User?->hasPermission(
                    Permissions::ACL_AVALIATION_EDIT
                )
            )
            ->confirmationQuestion(__('messages.pages.avaliation.deleteConfirmation'))
            ->feedbackMessage(__('messages.pages.avaliation.deleteSuccess'));
    }
}
