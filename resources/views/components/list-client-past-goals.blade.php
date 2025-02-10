@inject('mGoal', 'App\Models\Goal')
@inject('SysUtils', 'App\Helpers\SysUtils')

@foreach ($arrPastGoals as $arrGoal)
    @php
    $Goal = $mGoal::find($arrGoal['id']);
    $title = __('messages.models.Goal.fields.deadline') . ': ' . $SysUtils::reformatDate($Goal->deadline, 'Y-m-d', __('messages.dateFormat'));
    @endphp

    <x-card title="{{ $title }}" closed="true">
        @include('app.client.partials.cardGoalsContent', [
            'GOAL' => $Goal,
            'CAN_EDIT' => false,
            'VIEW_ONLY' => true
        ])
    </x-card>
@endforeach

@if ($showMoreButton)
    <div id="list-client-past-goals-more" class="text-center">
        @csrf

        <a href="javascript:;"
        class="btn btn-primary btn-user btn-sm"
        data-ccid="{{ $Goal->client->codedId }}"
        data-bdline="{{ $Goal->deadline }}">
            {{ __('messages.buttonLoadMore') }}
        </a>
    </div>
@endif
