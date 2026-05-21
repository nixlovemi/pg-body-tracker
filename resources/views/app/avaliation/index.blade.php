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
    @if ($Permissions::checkPermission($Permissions::ACL_AVALIATION_EDIT) && ($CLIENT_COUNT ?? 0) > 0)
        <a
            href="javascript:;"
            class="btn btn-primary {{ (($AVALIATION_COUNT ?? 0) > 0) ? '' : 'd-none' }}"
            id="btn-add-avaliations"
        >
            {!! $Icons::PLUS !!}
            {{ __('messages.modalAddTitle', [
                'modelName' => __('messages.models.Avaliation.name')
            ]) }}
        </a>
    @endif

    @if (($CLIENT_COUNT ?? 0) <= 0)
        <div class="card mt-2 border-left-warning">
            <div class="card-body">
                <h5 class="mb-2">{{ __('messages.pages.avaliation.index.emptyNoClientTitle') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.pages.avaliation.index.emptyNoClientDescription') }}</p>
                <a href="{{ route('app.client.add', ['prefillSelf' => 1]) }}" class="btn btn-primary">
                    {{ __('messages.pages.avaliation.index.emptyNoClientCta') }}
                </a>
            </div>
        </div>
    @elseif (($AVALIATION_COUNT ?? 0) <= 0)
        <div class="card mt-2 border-left-info">
            <div class="card-body">
                <h5 class="mb-2">{{ __('messages.pages.avaliation.index.emptyTitle') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.pages.avaliation.index.emptyDescription') }}</p>
                @if ($Permissions::checkPermission($Permissions::ACL_AVALIATION_EDIT))
                    <a
                        href="javascript:;"
                        class="btn btn-primary"
                        id="btn-add-avaliations-empty-state"
                        onclick="document.getElementById('btn-add-avaliations')?.click(); return false;"
                    >
                        {{ __('messages.pages.avaliation.index.emptyCta') }}
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if (($CLIENT_COUNT ?? 0) > 0)
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
    @endif
@endsection

@if ($RevDateFeature->validate())
    @php
    $paramOpenAvaliation = $_REQUEST['openAvaliation'] ?? null;
    $paramOpenAvaliationCodedId = $_REQUEST['openAvaliationCID'] ?? null;
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openAvaliationModal = function () {
                if (typeof showJsonAjaxModal === 'function' && typeof JS_APP_PREFIX !== 'undefined') {
                    showJsonAjaxModal('GET', `/${JS_APP_PREFIX}/avaliation/htmlModalSelectClient`, {
                        'json': 1
                    });
                    return;
                }

                let mainBtn = document.getElementById('btn-add-avaliations');
                if (mainBtn) {
                    mainBtn.click();
                }
            };

            let emptyStateBtn = document.getElementById('btn-add-avaliations-empty-state');
            if (emptyStateBtn) {
                emptyStateBtn.addEventListener('click', function() {
                    openAvaliationModal();
                });
            }

            @if ($paramOpenAvaliation)
                // click the button to open the modal
                openAvaliationModal();

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
