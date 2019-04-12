<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Picstar.uz | Будь лучше!</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    {{-----font-awesome--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css" rel="stylesheet">
    <link href="{{ asset('css/emojionearea.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/additional.css?ver=') . time() }}" rel="stylesheet">
    @stack('styles')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    {{--favicon--}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon//favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    {{--favicon end--}}

    @stack('functions')
</head>

<body>
<div id="app">
    @auth
    <main>
        {{--dashboard sidebar for authenticated users--}}
        <aside class="sidebar" id="side-menu">

            <div class="logo bordered">
                <a href="/">
                    <img src="{{ asset('images/landing/logo.png') }}" alt="picstar">
                    <i class="fas fa-times close-menu d-lg-none"></i>
                </a>
            </div>

            <div class="role-switcher bordered">
                <label class="switcher-title @if(Session::get('usertype') == 'advertiser' or Session::get('usertype') == null )active @endif" data-name="advertiser">Рекламодатель</label>
                <label class="switch">
                    <input type="checkbox" id="checktype" @if(Session::get('usertype')=='blogger' )checked @endif>
                    <a class="slider round">
                    </a>
                </label>
                <label class="switcher-title @if(Session::get('usertype') == 'blogger')active @endif" data-name="blogger">Исполнитель</label>
            </div>

            @include('layouts.sidebar-bosslike')

            @if(Auth::user()->role_id==1)
                @include('layouts.sidebar-admin')
            @endif
            @include('layouts.sidebar-user')

        </aside>
        {{--dashboard content section--}}
        @php($path = explode('catalog', Request()->path()))
        @php($path2 = explode('tasks', Request()->path()))
        {{--{{$path[0]}}--}}
        <div class="side-right {{ (count($path) > 1 || count($path2) > 1) ? 'has-additional-sidebar' : '' }}">
            <section class="top-menu">
                <nav class="navbar navbar-expand-lg navbar-light">

                    <button class="navbar-toggler" type="button">
                        <span class="navbar-icon"></span>
                        <span class="navbar-icon"></span>
                        <span class="navbar-icon"></span>
                    </button>

                        <span class="mobile account-bill">
                            <i class="icon-wallet"></i>
                            <span class="user_balance"></span>
                            <a class="balance-top-up" href="/deposit/">
                                Пополнить баланс
                            </a>
                        </span>


                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/tasks/my">
                                    <i class="icon-tasks"></i>
                                    Мои задания
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/tasks/all">
                                    <i class="icon-stock"></i>
                                    Биржа заданий
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/task/new">
                                    <i class="icon-add"></i>
                                    Добавить задание
                                </a>
                            </li>

                        </ul>

                        <ul class="nav navbar-nav navbar-right">

                            <li class="nav-item text-center">
                                    <span class="account-bill">
                                        <i class="icon-wallet"></i>
                                        <span class="user_balance"></span>
                                        <a class="balance-top-up" href="/deposit/">
                                            Пополнить баланс
                                        </a>
                                    </span>

                            </li>
                            <li class="nav-item text-center account-information">
                                <span class="nav-link">{{ Auth::user()->email }}</span>
                                <span class="nav-link pt-0">ID: {{ Auth::user()->billing_id }}</span>
                            </li>

                            <li class="dropdown open">
                                <a href="#" class="dropdown-toggle" aria-expanded="true">
                                    <div class="img-holder img-thumbnail border-0 ava" {{--style="background-image: url({{asset('uploads/'.Auth::user()->avatar)}})"--}} style="background-image: url({{ (!empty(Auth::user()->avatar)) ? asset('uploads/'.Auth::user()->avatar) : asset('images/avatars/'.Bosslike::randomImage()) }})">
                                    </div>

                                </a>
                                <ul class="dropdown-menu">
                                    <li><i class="fas fa-money-bill"></i><a href="/funds">Мой баланс</a></li>
                                    <li><i class="far fa-user"></i><a href="/info/edit">Мои данные</a></li>
                                    <li><i class="fas fa-cog"></i><a href="/profile">Мои настройки</a></li>
                                    <li class="divider"></li>
                                    <li><a href="/logout"><i class="far fas_active fa-fw fa-sign-out"></i>
                                            Выход</a></li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                </nav>
            </section>
            <section class="pb-3">
                <div class="container">
                    <div class="row justify-content-center mb-2">
                        <div class="col-md-10">
                            <h2 class="page-title">@yield('title')</h2>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            @if(session()->has('success'))
                                <input type="hidden" id="success-session" value="{{ session('success') }}">
                            @elseif((session()->has('fail')))
                                <input type="hidden" id="fail-session" value="{{ session('fail') }}">
                            @endif
                            @yield('content')
                        </div>
                    </div>

                        {{--@if(Session::has('toasts'))--}}
                        {{--@foreach(Session::get('toasts') as $toast)--}}
                        {{--<div class="alert alert-{{ $toast['level'] }}">--}}
                        {{--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>--}}

                        {{--{{ $toast['message'] }}--}}
                        {{--</div>--}}
                        {{--@endforeach--}}
                        {{--@endif--}}

                    </div>
                </div>
            </section>
        </div>

    </main>

    <div class="mobile-navigation">
        <ul>
            <li>
                <a href="{{ route('tasks.all') }}" class="mobile-navigation-link">
                    <i class="icon-stock"></i>
                </a>
            </li>
            <li>
                <a href="/catalog" class="mobile-navigation-link">
                    <i class="icon-shopping-bag"></i>
                </a>
            </li>
            <li>
                <a href="/task/new" class="mobile-navigation-link big">
                    <i class="icon-add"></i>
                </a>
            </li>
            <li>
                <a href="/funds" class="mobile-navigation-link">
                    <i class="icon-wallet"></i>
                </a>
            </li>
            <li>
                <a href="/profile" class="mobile-navigation-link">
                    <i class="icon-man"></i>
                </a>
            </li>
        </ul>
    </div>

    @endauth
    @yield('authorize')
</div>

@include('toast::messages-jquery')
{{--<script src="{{ asset('js/manifest.js') }}"></script>--}}
@if(Request()->path() == 'profile')
    <script src="https://code.jquery.com/jquery-1.9.1.min.js" integrity="sha256-wS9gmOZBqsqWxgIVgA8Y9WcQOa7PgSIX+rPA0VL2rbQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.js"></script>
@else
    {{--<script src="{{ asset('js/vendor.js') }}"></script>--}}
    <script src="{{ asset('js/app.js') }}"></script>
@endif
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="{{asset('js/functions.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js"></script>
<script src="{{ asset('js/emojionearea.min.js') }}"></script>
<script src="{{asset('js/main.js?v=') . time()}}"></script>

<script>
    $(document).ready(function() {

        getBalance();

        var _successSession = $('#success-session').val();
        var _failSession = $('#fail-session').val();
        if (_successSession != null) {
            toastr.success(_successSession);
        } else if (_failSession != null) {
            window.toastr.error(_failSession);
        }

        var isMobile = {
            Android: function() {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function() {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function() {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function() {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function() {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function() {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        if (!isMobile.any()) {
            (function($) {
                $("#side-menu").mCustomScrollbar({
                    autoHideScrollbar: true,
                    theme: "minimal-dark"
                });
            })(jQuery);
        }




        $('.open-menu, .close-menu').on('click', function() {
            $('#side-menu').toggleClass('menu-show-up');
        });
        $('.switcher-title').on('click', function() {
            var type = $(this).attr("data-name");
            $('.switcher-title').removeClass('active');
            $(this).addClass('active');
            $('.list-group').removeClass('show');
            if (type == 'advertiser') {
                $('#checktype').prop("checked", false);
                $('.list-group.advertiser').addClass('show');
            } else {
                $('#checktype').prop("checked", true);
                $('.list-group.blogger').addClass('show');
            }
            addToSession(type);
        });
        $('#checktype').on('click', function() {
            var smth = $(this).is(':checked');
            $('.list-group').removeClass('show');
            if (smth == true) {
                $('.switcher-title').removeClass('active');
                // $('.switcher-title.advertiser').addClass('active');
                $('.switcher-title[data-name="blogger"]').addClass('active');
                $('.list-group.blogger').addClass('show');
                addToSession("blogger");
            } else {
                $('.switcher-title').removeClass('active');
                $('.switcher-title[data-name="advertiser"]').addClass('active');
                $('.list-group.advertiser').addClass('show');
                addToSession("advertiser");
            }
        });

        function addToSession(currentUser) {
            $.ajax({
                url: '/session/' + currentUser,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                }
            })
        }

        $(".navbar-toggler").click(function() {
            $(".sidebar").toggleClass("active");
        });

        $(".sidebar .close-menu").click(function(e) {
            e.preventDefault();
            $(".sidebar").removeClass("active");
        });

        // function gotoBottom(id) {
        //     var element = document.getElementById(id);
        //     element.scrollTop = element.scrollHeight - element.clientHeight;
        // }

        // $(".sidebar .dropdown").click(function() {
        //     gotoBottom("side-menu");
        // });
    });
</script>
@stack('scripts')

    <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(53228095, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/53228095" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-138161655-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-138161655-1');
</script>
</body>
</html>