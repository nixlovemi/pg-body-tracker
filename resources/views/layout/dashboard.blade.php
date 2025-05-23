@inject('mSysUtils', 'App\Helpers\SysUtils')
@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $DASH_PAGE_TITLE: string
*/

$DASH_PAGE_TITLE = $DASH_PAGE_TITLE ?? '';
$USER = $mSysUtils::getLoggedInUser();
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('CORE_HEADER_CUSTOM_CSS')
    <style>
        .sidebar-dark #sidebarToggle::after {
            color: rgba(255,255,255,.5);
            position: relative;
            font-size: 1.7em;
            top: -12px;
            left: -8px;
        }
    </style>
@endsection

@section('CORE_BODY_CONTENT')
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar (bg-gradient-primary) -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('app.dashboard.index') }}">
                <div class="sidebar-brand-icon">
                    <img class="img-fluid w-100" style="max-width:60px;" src="/images/logo-icon.png" alt="Logo" />
                </div>
                <div class="sidebar-brand-text">
                    <img class="img-fluid w-100" src="/images/logo.png" alt="Logo" />
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            @include('layout.partials.dash-nav-item')

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        {!! $Icons::BARS !!}
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                            >
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ $USER->first_name ?? __('messages.userNameDash') }}</span>
                                <img class="img-profile rounded-circle" src="{{ $USER->getPictureBase64() }}" />
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('app.user.profile') }}">
                                    {!! $Icons::USER_GREY !!}
                                    {{ __('messages.profile') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('app.user.changePsw') }}">
                                    {!! $Icons::KEY_GREY !!}
                                    {{ __('messages.pages.changePsw.title') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('app.login') }}">
                                    {!! $Icons::SIGN_OUT_GREY !!}
                                    {{ __('messages.logout') }}
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    @if (!empty($DASH_PAGE_TITLE))
                        <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">
                                {{ $DASH_PAGE_TITLE }}
                            </h1>
                        </div>
                    @endif

                    <!-- Content Row -->
                    @include('layout.partials.alert-return-messages')
                    @yield('DASH_BODY_CONTENT')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="mt-5 sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; {{ env('APP_NAME') }} {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        {!! $Icons::ANGLE_UP !!}
    </a>
@endsection
