<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
    <head>
        <title>{{ $PAGE_TITLE ?? '' }} | {{ env('SITE_DISPLAY_NAME') }}</title>

        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />

        @livewireStyles
        @yield('CORE_HEADER_CUSTOM_CSS')
        <link rel='stylesheet' href='{{ url('/') }}/base-reset.css' type='text/css' media='all' />
        <link rel='stylesheet' href='{{ url('/') }}/template/components/bootstrap/css/bootstrap.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='{{ url('/') }}/template/components/font-awesome-5/css/all.min.css' type='text/css' media='all' />
        <link rel='stylesheet' href='{{ url('/') }}/template/components/sweetalert2-11.14.0/sweetalert2.min.css' type='text/css' media='all' />
        <link rel="stylesheet" href="{{ url('/') }}/template/components/jquery-ui-1.13.2/jquery-ui.min.css" type='text/css' media='all' />
        <link rel="stylesheet" href="{{ url('/') }}/template/components/jquery-ui-1.13.2/jquery-ui.theme.css" type='text/css' media='all' />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- ========== -->

        <!-- Custom fonts for this template-->
        <link href="{{ url('/') }}/template/start-bootstrap/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
        <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet" />

        <!-- Custom styles for this template-->
        <link href="{{ url('/') }}/template/start-bootstrap/css/sb-admin-2.min.css" rel="stylesheet" />
        <link href="{{ url('/') }}/template/start-bootstrap/css/custom.css" rel="stylesheet" />

        <!-- BASE CSS -->
        <link rel='stylesheet' href='{{ url('/') }}/base.css' type='text/css' media='all' />
    </head>

    <body id="page-top">

        @yield('CORE_BODY_CONTENT')

        <!-- Bootstrap core JavaScript-->
        <script src="{{ url('/') }}/template/start-bootstrap/vendor/jquery/jquery.min.js"></script>
        <script src="{{ url('/') }}/template/start-bootstrap/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="{{ url('/') }}/template/start-bootstrap/vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="{{ url('/') }}/template/components/sweetalert2-11.14.0/sweetalert2.all.min.js"></script>
        <script src="{{ url('/') }}/template/components/jquery-loading-overlay-2.1.7/dist/loadingoverlay.min.js"></script>
        <script src="{{ url('/') }}/template/components/jquery-ui-1.13.2/jquery-ui.min.js"></script>
        <script src="{{ url('/') }}/template/start-bootstrap/vendor/chart.js/Chart.min.js"></script>
        <script src="{{ url('/') }}/template/start-bootstrap/js/demo/chart-area-demo.js"></script>
        <script src="{{ url('/') }}/template/start-bootstrap/js/demo/chart-pie-demo.js"></script>
        <script src="{{ url('/') }}/template/start-bootstrap/js/sb-admin-2.min.js"></script>

        @livewireScripts
        @yield('CORE_FOOTER_CUSTOM_JS')
        <script src="{{ url('/') }}/template/start-bootstrap/js/custom.js"></script>

        <!-- BASE JS -->
        <script src="{{ url('/') }}/base.js"></script>
    </body>
</html>
