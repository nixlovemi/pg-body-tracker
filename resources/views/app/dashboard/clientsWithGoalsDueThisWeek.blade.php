@inject('SysUtils', 'App\Helpers\SysUtils')

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
    <h4>{{ $PAGE_TITLE }}</h4>

    <div class="row">
        <div class="col-12">
            <div class="card mt-2">
                <div class="card-body px-2 py-0">
                    <livewire:table
                        :config="App\Tables\ClientsWithGoalsDueThisWeekTable::class"
                        :configParams="['userId' => $userId]"
                    />
                </div>
            </div>
        </div>
    </div>
@endsection
