@php
/*
View variables:
===============
    - $GOOGLE_CODE: string
*/

$GOOGLE_CODE = $GOOGLE_CODE ?? '';
@endphp

<script>
    function loadAnalytics() {
        var script = document.createElement('script');
        script.src = 'https://www.googletagmanager.com/gtag/js?id={{$GOOGLE_CODE}}';
        script.async = true;
        document.head.appendChild(script);

        script.onload = function () {
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{$GOOGLE_CODE}}');
        };
    }

    window.addEventListener('DOMContentLoaded', function () {
        setTimeout(loadAnalytics, 3000); // Aguarda 3 segundos para iniciar
    });
</script>
