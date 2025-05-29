@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
*/
@endphp

<div class="row no-gutters align-items-center">
    <div class="col mr-2">
        <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
            {{ __('messages.components.avaliationReport.cardInfo') }}
        </div>
        <div class="h6 mb-1 text-gray-800">
            <span class="font-weight-bold">{{ __('messages.models.Client.fields.first_name') }}:</span>
            {{ $AVALIATION->client->getName() }}
        </div>
        <div class="h6 mb-1 text-gray-800">
            <span class="font-weight-bold">{{ __('messages.models.Client.fields.gender') }}:</span>
            {{ $AVALIATION->client->getGenderStr() }}
        </div>
        <div class="h6 mb-1 text-gray-800">
            <span class="font-weight-bold">{{ __('messages.models.Client.fields.age') }}:</span>
            {{ $AVALIATION->age }}
        </div>
        <div class="h6 mb-1 text-gray-800">
            <span class="font-weight-bold">{{ __('messages.models.Client.fields.height') }}:</span>
            {{ $AVALIATION->height_cm }} cm
        </div>
        <div class="h6 mb-1 text-gray-800">
            <span class="font-weight-bold">{{ __('messages.components.avaliationReport.method') }}:</span>
            {{ $AVALIATION->getFormattedCalculatePercFatBy() }}
        </div>
        <div class="h6 mb-1 text-gray-800">
            <span class="font-weight-bold">{{ __('messages.models.Avaliation.fields.date') }}:</span>
            {{ $AVALIATION->getFormattedDate() }}
        </div>
    </div>
</div>
