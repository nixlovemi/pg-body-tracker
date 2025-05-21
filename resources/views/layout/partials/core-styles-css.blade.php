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

<!-- RAF PHOTO -->
<style>
    .raf-photo-url {
        width: 100%;
        aspect-ratio: 3 / 4; /* 480 / 640 = 3 / 4 */
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        display: flex;
        justify-content: center;
        align-items: center;
        object-fit: cover;
    }
    .raf-photo-url img {
        cursor: pointer;
    }
    .raf-file-input {
        position: absolute;
        opacity: 0;
        z-index: -1;
        text-indent: -9999px;
    }
    .raf-photo-url:hover .raf-remove-btn {
        opacity: 1;
        pointer-events: auto;
    }
    .raf-remove-btn {
        position: absolute;
        top: 16px;
        right: 8px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        font-weight: bold;
        font-size: 16px;
        line-height: 22px;
        text-align: center;
        cursor: pointer;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-in-out;
        z-index: 10;
        padding: 0;
    }
</style>

<!-- BASE CSS -->
<link rel='stylesheet' href='{{ url('/') }}/base.css' type='text/css' media='all' />
