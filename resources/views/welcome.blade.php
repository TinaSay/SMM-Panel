<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body id="landing">
<main class="landing">
    <aside class="landing-sidebar">
        <img src="{{asset('images/landing/logo.png')}}" class="landing-logo" alt="">
        <p>На нашем сервисе можно бесплатно накрутить лайки, подписчиков, фолловеров, раскрутить группу
        </p>
        <a href="{{ route('oauth.login') }}" class="cta-button">Вход</a>
        <a href="{{ route('oauth.login') }}" class="cta-button">Регистрация</a>
        <img src="{{asset('images/landing/plane.png')}}" alt="" class="plane">
    </aside>
    <section class="landing-side-right" style="background-image: url({{asset('images/landing/phone.png')}})">
        {{--<img src="{{asset('images/landing/phone.png')}}" class="phone-img" alt="">--}}
    </section>
</main>
</body>
</html>
