@inject('Permissions', 'App\Helpers\Permissions')
@inject('SysUtils', 'App\Helpers\SysUtils')
@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
$userId = $SysUtils::getLoggedInUser()?->id ?? 0;
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    @if ($Permissions::checkPermission($Permissions::ACL_CLIENT_EDIT) && ($CLIENT_COUNT ?? 0) > 0)
        <a href="{{ route('app.client.add') }}" class="btn btn-primary">
            {!! $Icons::PLUS !!}
            {{ __('messages.modalAddTitle', [
                'modelName' => __('messages.models.Client.name')
            ]) }}
        </a>
    @endif

    @if (($CLIENT_COUNT ?? 0) <= 0)
        <div class="card mt-2 border-left-info">
            <div class="card-body">
                <h5 class="mb-2">{{ __('messages.pages.client.index.emptyTitle') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.pages.client.index.emptyDescription') }}</p>

                @if ($Permissions::checkPermission($Permissions::ACL_CLIENT_EDIT))
                    <a href="{{ route('app.client.add') }}" class="btn btn-primary mr-2">
                        {{ __('messages.pages.client.index.emptyCtaPrimary') }}
                    </a>
                @endif

                <a href="{{ route('app.client.add', ['prefillSelf' => 1]) }}" class="btn btn-outline-primary">
                    {{ __('messages.pages.client.index.emptyCtaSecondary') }}
                </a>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card mt-2">
                    <div class="card-body px-2 py-0">
                        <livewire:table
                            :config="App\Tables\ClientsTable::class"
                            :configParams="['userId' => $userId]"
                        />
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
