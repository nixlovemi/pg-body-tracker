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
    @if ($Permissions::checkPermission($Permissions::ACL_CLIENT_EDIT))
        <a href="{{ route('app.client.add') }}" class="btn btn-primary">
            {!! $Icons::PLUS !!}
            {{ __('messages.modalAddTitle', [
                'modelName' => __('messages.models.Client.name')
            ]) }}
        </a>
    @endif

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
@endsection
