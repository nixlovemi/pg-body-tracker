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
