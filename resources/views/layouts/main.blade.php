<!DOCTYPE html>
<html lang="{{lang_code() == 15 ? 'uk' : 'en'}}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <link rel="shortcut icon" href="/favicon.ico"  type="image/x-icon">

    @if(isset($seo) && isset($seo['title']))
        <title>{{$seo['title']}}</title>
    @else
        <title>@lang('translations.site_name')</title>
    @endif
    @if(isset($seo) && isset($seo['description']))
        <meta type="description" value="{{$seo['description']}}">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:site_name" content="@lang('translations.site_name')" />
    <meta property="og:title" content="@if(isset($seo['og_title'])){{$seo['og_title']}}@else @lang('translations.og_title') @endif" />
    <meta property="og:description" content="@if(isset($seo['og_description'])){{$seo['og_description']}}@else @lang('translations.og_description') @endif" />
    @if(isset($seo['image']))
        <meta property="og:image" content="{{$seo['image']}}" />
    @endif
    <meta property="og:type" content="website" />
    @if(isset($url))
        <link rel="canonical" href="{{explode('?', $url)[0]}}"/>
        <meta property="og:url" content="{{$url}}" />
        @php $translation = lang_code() == 15 ? str_replace('/uk', '/en', $url) : str_replace('/en', '/uk', $url); @endphp
        @php $opposite = lang_code() == 15 ? 'en' : 'uk'; @endphp
        <link rel="alternate" href="{{$translation}}" hreflang="{{$opposite}}"/>
    @endif

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-JC6610SVSE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-JC6610SVSE');
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/css/style.css?var={{date('Hsi')}}">
    <link rel="stylesheet" href="/css/sg.css?var={{date('Hsi')}}">

    <!--[if IE 9]>
    <link href="/css/bootstrap-ie9.min.css" rel="stylesheet" />
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--[if lte IE 8]>
    <link href="https://cdn.jsdelivr.net/gh/coliff/bootstrap-ie8/css/bootstrap-ie8.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/g/html5shiv@3.7.3"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade

        your browser</a> to improve your experience and security.</p>
    <![endif]-->
</head>
<body>

<div class="page-wrapper">
    @include('includes.header')
    @yield('content')
    @include('includes.footer')
</div>

    <script src="/js/jquery-3.5.1.js"></script>
    <script src="/js/imagesloaded.pkgd.min.js"></script>
    <script src="/js/swiper-6.4.8.min.js"></script>
    <script src="/js/masonry.pkgd.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/jquery.ui.touch-punch.min.js"></script>

    @yield('scripts')

    <script src="/js/script.js?var={{date('Hsi')}}"></script>
</body>
</html>

