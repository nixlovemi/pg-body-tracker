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
use App\Helpers\SysUtils;
use App\Helpers\Permissions;
use App\Helpers\Icons;

class AvaliationsTable extends AbstractTableConfiguration
{
    public int $clientId;
    public bool $canEdit = false;
    private Client $Client;

    /**
     * @throws Exception
     */
    private function init(): void
    {
        $this->Client = Client::find($this->clientId);
        if ($this->Client && !Client::fHasAccess($this->Client)) {
            throw new \Exception(__('messages.modelErrorNoAccess'));
        }
    }

    protected function table(): Table
    {
        $this->init();

        return Table::make()
            ->model(Avaliation::class)
            ->query(function(Builder $query) {
                return $query->where('client_id', '=', $this->Client->id ?? 0);
            })
            ->rowActions(fn(Avaliation $Avaliation) => [
                (new OpenModalRowAction(
                    __('messages.tableActionView'),
                    route('app.avaliation.htmlModalView', ['codedId' => $Avaliation->codedId, 'json' => 1]),
                    Icons::EYE
                ))->when(SysUtils::getLoggedInUser()?->hasPermission(Permissions::ACL_AVALIATION_EDIT)),

                (new OpenModalRowAction(
                    __('messages.tableActionEdit'),
                    route('app.avaliation.htmlModalEdit', ['codedId' => $Avaliation->codedId, 'json' => 1]),
                    Icons::EDIT
                ))->when($this->canEdit && SysUtils::getLoggedInUser()?->hasPermission(Permissions::ACL_AVALIATION_EDIT)),

                (new DestroyRowAction())
                    ->when($this->canEdit && SysUtils::getLoggedInUser()?->hasPermission(Permissions::ACL_AVALIATION_EDIT))
                    ->confirmationQuestion(__('messages.pages.avaliation.deleteConfirmation'))
                    ->feedbackMessage(__('messages.pages.avaliation.deleteSuccess')),
            ]);
    }

    protected function columns(): array
    {
        return [
            Column::make('date')
                ->sortByDefault('desc')
                ->title(__('messages.models.Avaliation.fields.date'))
                ->format(function(Avaliation $Avaliation) {
                    return SysUtils::reformatDate($Avaliation?->date, 'Y-m-d', __('messages.dateFormat'));
                })
                ->sortable(),
            Column::make('weight_kg')
                ->title(__('messages.models.Client.fields.weight') . ' (kg)')
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation?->weight_kg, 3, __('messages.decimalSeparator'), __('messages.thousandSeparator'));
                }),
            Column::make('body_fat_perc')
                ->title(__('messages.models.Avaliation.fields.body_fat_perc'))
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation?->body_fat_perc, 2, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' %';
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.labelFatMass'))
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation->getFatMassKg(), 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' kg';
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.labelLeanMass'))
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation->getLeanMassKg(), 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' kg';
                }),
            Column::make('')
                ->title(__('messages.models.Avaliation.labelTmb'))
                ->format(function(Avaliation $Avaliation) {
                    return number_format($Avaliation->getTmb(), 3, __('messages.decimalSeparator'), __('messages.thousandSeparator')) . ' kcal';
                }),
        ];
    }

    protected function results(): array
    {
        return [
            // The table results configuration.
            // As results are optional on tables, you may delete this method if you do not use it.
        ];
    }
}
