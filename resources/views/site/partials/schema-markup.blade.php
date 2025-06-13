@inject('LayoutSitePresenter', 'App\Presenters\LayoutSitePresenter')

@php
$routeName = Route::currentRouteName();
@endphp

@if (in_array($routeName, ['site.home']))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{env('APP_NAME')}}",
        "url": "{{env('APP_URL')}}",
        "logo": "{{env('APP_URL')}}/images/site-logo-top-white.png",
        "sameAs": [
            "https://www.instagram.com/pgbodytracker",
            "https://www.facebook.com/people/PG-Body-Tracker/61577056899425/"
        ]
    }
    </script>

    @php
    /*
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "123"
        }
    */
    @endphp
@endif

@if (in_array($routeName, ['site.faq']))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            @foreach ($LayoutSitePresenter::getFaq() as $faq)
            {
                "@type": "Question",
                "name": "{{ addslashes($faq['question']) }}",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "{{ addslashes($faq['answer']) }}"
                }
            }@if (!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
@endif
