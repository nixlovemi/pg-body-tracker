@inject('Permissions', 'App\Helpers\Permissions')
@inject('SysUtils', 'App\Helpers\SysUtils')
@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $AVALIATION: App\Models\Avaliations
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <div class="text-left mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-light">
            {{ __('messages.buttonBack') }}
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <x-avaliation-report
                :avaliationId="$AVALIATION->id"
            />
        </div>
    </div>
@endsection
