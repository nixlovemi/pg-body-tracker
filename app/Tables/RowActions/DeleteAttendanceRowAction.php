<?php

namespace App\Tables\RowActions;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Okipa\LaravelTable\Abstracts\AbstractRowAction;

class DeleteAttendanceRowAction extends AbstractRowAction
{
    public string $modelKey = 'meeting_date';

    protected function identifier(): string
    {
        // The unique identifier that is required to retrieve the row action.
        return 'delete-attendance-row-action';
    }

    protected function class(Model $model): array
    {
        // The CSS class that will be added to the row action link.
        // Note: you can use conditional class merging as specified here: https://laravel.com/docs/blade#conditionally-merge-classes
        return ['text-danger', 'font-weight-bold'];
    }

    protected function title(Model $model): string
    {
        // The title that will be set to the row action link.
        return __('Deletar');
    }

    protected function icon(Model $model): string
    {
        // The icon that will appear in the row action link.
        return '<i class="fa-solid fas fa-trash fa-fw"></i>';
    }

    protected function defaultConfirmationQuestion(Model $model): string|null
    {
        // The default row action confirmation question that will be asked before execution.
        // Return `null` if you do not want any confirmation question to be asked by default.
        return __('Você deseja deletar a presença da data :date?', [
            'date' => date('d/m/Y', strtotime($model->meeting_date)),
        ]);
    }

    protected function defaultFeedbackMessage(Model $model): string|null
    {
        // The default row action feedback message that will be triggered on execution.
        // Return `null` if you do not want any feedback message to be triggered by default.
        return __('Presença da data :date deletada com sucesso', [
            'date' => date('d/m/Y', strtotime($model->meeting_date)),
        ]);
    }

    /** @return mixed|void */
    public function action(Model $model, Component $livewire)
    {
        // The treatment that will be executed on click on the row action link.
        // Use the `$livewire` param to interact with the Livewire table component and emit events for example.
        \App\Models\Attendance::where('meeting_date', $model->meeting_date)->delete();
    }
}
