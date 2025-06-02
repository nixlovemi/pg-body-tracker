@inject('Permissions', 'App\Helpers\Permissions')
@inject('SysUtils', 'App\Helpers\SysUtils')
@inject('Icons', 'App\Helpers\Icons')
@inject('RevaluationDate', 'App\Helpers\Feature\RevaluationDate')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
$userId = $SysUtils::getLoggedInUser()?->id ?? 0;
$RevDateFeature = new $RevaluationDate();
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @if ($Permissions::checkPermission($Permissions::ACL_AVALIATION_EDIT))
        <a href="javascript:;" class="btn btn-primary" id="btn-add-avaliations">
            {!! $Icons::PLUS !!}
            {{ __('messages.modalAddTitle', [
                'modelName' => __('messages.models.Avaliation.name')
            ]) }}
        </a>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card mt-2">
                <div class="card-body px-2 py-0">
                    <livewire:table
                        :config="App\Tables\AvaliationsTable::class"
                        :configParams="[
                            'canEdit' => 1
                        ]"
                    />
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($RevDateFeature->validate())
    @php
    $paramOpenAvaliation = $_REQUEST['openAvaliation'] ?? null;
    $paramOpenAvaliationCodedId = $_REQUEST['openAvaliationCID'] ?? null;
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($paramOpenAvaliation)
                // click the button to open the modal
                document.getElementById('btn-add-avaliations').click();

                // wait 2 seconds then select the client (client_cid = ZD)
                setTimeout(function () {
                    // select option, client with client_cid = $paramOpenAvaliationCodedId
                    let value = '{{ $paramOpenAvaliationCodedId }}';
                    let radio = document.querySelector(`input[name="client_cid"][value="${value}"]`);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }, 1250);
            @endif
        });
    </script>
@endif
