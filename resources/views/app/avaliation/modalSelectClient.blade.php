@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
===============
*/

$userId = $SysUtils::getLoggedInUser()?->id ?? 0;
@endphp

@extends('layout.modal', [
    'divId' => 'avaliation-modal-select-client' . date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ __('messages.pages.avaliation.modalSelectClient.title') }}
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="register-avaliation-modal-select-client" method="POST" action="">
        @csrf
        <input type="hidden" id="client-select-error-message" value="{{ __('messages.pages.avaliation.modalSelectClient.selectClientErrorMessage') }}" />

        <livewire:table
            :config="App\Tables\AvaliationsSelectClientTable::class"
            :configParams="['userId' => $userId]"
        />

        <div class="form-actions">
            <div class="float-right">
                <button type="submit" class="btn-modal-submit btn btn-sm primary btn-user">
                    {{ __('messages.pages.avaliation.modalSelectClient.btnSelect') }}
                </button>

                <a href="javascript:;" class="btn-modal-close btn btn-sm btn-light" data-dismiss="modal">
                    {{ __('messages.buttonClose') }}
                </a>
            </div>
        </div>
    </form>

    <script>
        (function($) {
            $(document).ready(function() {

            });
        }(jQuery));
    </script>

@endsection
