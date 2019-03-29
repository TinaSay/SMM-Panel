<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Picstar.uz</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{--favicon--}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon//favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    {{--favicon end--}}
</head>
<body id="landing">
<main class="landing">
    <aside class="landing-sidebar">
        <img src="{{asset('images/landing/logo.png')}}" class="landing-logo" alt="">
        <p>На нашем сервисе можно накрутить лайки, подписчиков, раскрутить группу
        </p>
        <a href="{{ route('oauth.login') }}" class="cta-button">Вход</a>
        <a href="{{ route('oauth.login') }}" class="cta-button">Регистрация</a>
        <img src="{{asset('images/landing/plane.png')}}" alt="" class="plane">
    </aside>
    <section class="landing-side-right" style="background-image: url({{asset('images/landing/phone.png')}})">
    </section>
</main>
</body>
</html>
