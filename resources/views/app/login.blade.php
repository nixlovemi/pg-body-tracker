@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('CORE_HEADER_CUSTOM_CSS')
    <style>
        body {
            background-color: #eaeaea !important;
        }
    </style>
@endsection

@section('CORE_BODY_CONTENT')
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-9 col-lg-6">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-12">
                                <div class="p-5">
                                    <div class="text-center text-gray-900">
                                        <h1 class="h2">Login</h1>
                                        <h3 class="h4 mb-4">Áudio e Vídeo Esperança</h3>
                                    </div>
                                    @include('layout.partials.alert-return-messages')
                                    <form action="" class="user" method="POST">
                                        @csrf

                                        <div class="form-group">
                                            <input type="password" id="f-password" name="f-password" class="form-control form-control-user" placeholder="Senha" />
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Entrar
                                        </button>
                                    </form>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('CORE_FOOTER_CUSTOM_JS')
@endsection
