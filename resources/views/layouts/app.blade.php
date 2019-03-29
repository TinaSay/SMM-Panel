<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Picstar.uz</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    {{-----font-awesome--}}
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>

        ul.list-group.dnone {
            display: none;
        }

        ul.list-group.show {
            display: flex !important;
        }

        .btn.btn-default.aactive {
            border: solid 1px #4c110f;
            border-right: 1px #4c110f solid !important;
        }

    </style>

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
                    <label
                        class="switcher-title @if(Session::get('usertype') == 'advertiser' or Session::get('usertype') == null )active @endif"
                        data-name="advertiser">Рекламодатель</label>
                    <label class="switch">
                        <input type="checkbox" id="checktype" @if(Session::get('usertype') == 'blogger')checked @endif>
                        <a class="slider round">
                        </a>
                    </label>
                    <label class="switcher-title @if(Session::get('usertype') == 'blogger')active @endif"
                           data-name="blogger">Исполнитель</label>
                </div>

                @include('layouts.sidebar-bosslike')

                @if(Auth::user()->role_id==1)
                    @include('layouts.sidebar-admin')
                @endif
                @include('layouts.sidebar-user')

            </aside>
            {{--dashboard content section--}}
            <div class="side-right">
                <section class="top-menu">
                    <nav class="navbar navbar-expand-lg navbar-light">

                        <button class="navbar-toggler" type="button">
                            <span class="navbar-icon"></span>
                            <span class="navbar-icon"></span>
                            <span class="navbar-icon"></span>
                        </button>


                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="/tasks/my">
                                        <i class="far fa-list-alt"></i>
                                        Мои задания
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/tasks/all">
                                        <i class="fas fa-users"></i>
                                        Биржа заданий
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/task/new">
                                        <i class="far fa-file-alt"></i>
                                        Добавить задание
                                    </a>
                                </li>

                            </ul>

                            <ul class="nav navbar-nav navbar-right">

                                <li class="nav-item text-center">
                                     <span class="badge badge-success badge-circle">
                                          <i class="fas fa-star"></i>
                                          <span class="user_balance"></span>
                                      </span>
                                    <a class="nav-link pt-1" href="/deposit/">
                                        Пополнить баланс
                                    </a>
                                </li>
                                <li class="nav-item text-center">
                                    <span class="nav-link">{{ Auth::user()->email }}</span>
                                    <span class="nav-link pt-0">ID: {{ Auth::user()->billing_id }}</span>
                                </li>

                                <li class="dropdown open">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                                       aria-expanded="true">
                                        <div
                                            class="img-holder img-thumbnail border-0 ava"
                                            {{--style="background-image: url({{asset('uploads/'.Auth::user()->avatar)}})"--}}
                                            style="background-image: url({{ (!empty(Auth::user()->avatar)) ? asset('uploads/'.Auth::user()->avatar) : asset('images/avatars/'.Bosslike::randomImage()) }})">
                                        </div>

                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><i class="fas fa-money-bill"></i><a href="/deposit">Мой баланс</a></li>
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

                            {{--@if(Session::has('toasts'))--}}
                            {{--@foreach(Session::get('toasts') as $toast)--}}
                            {{--<div class="alert alert-{{ $toast['level'] }}">--}}
                            {{--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>--}}

                            {{--{{ $toast['message'] }}--}}
                            {{--</div>--}}
                            {{--@endforeach--}}
                            {{--@endif--}}
                            <div class="col-md-10">
                                @if(session()->has('success'))
                                    <input type="hidden" id="success-session" value="{{ session('success') }}">
                                @elseif((session()->has('fail')))
                                    <input type="hidden" id="fail-session" value="{{ session('fail') }}">
                                @endif
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </main>

    @endauth
    @yield('authorize')
</div>

@include('toast::messages-jquery')
<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ asset('js/vendor.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="{{asset('js/functions.js')}}"></script>

<script>
    $(document).ready(function () {

        $('#description').summernote({
            popover: false,
            height: 200,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol']],
            ]
        });

        getBalance();

        var _successSession = $('#success-session').val();
        var _failSession = $('#fail-session').val();
        if (_successSession != null) {
            window.toastr.success(_successSession);
        } else if (_failSession != null) {
            window.toastr.error(_failSession);
        }


        (function ($) {
            $("#side-menu").mCustomScrollbar({
                autoHideScrollbar: true,
                theme: "minimal-dark"
            });
        })(jQuery);

        $('.open-menu, .close-menu').on('click', function () {
            $('#side-menu').toggleClass('menu-show-up');
        });
        $('.switcher-title').on('click', function () {
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
        $('#checktype').on('click', function () {
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
                success: function (response) {
                    console.log(response);
                }
            })
        }

        $(".navbar-toggler").click(function () {
            $(".sidebar").toggleClass("active");
        });

        $(".sidebar .close-menu").click(function (e) {
            e.preventDefault();
            $(".sidebar").removeClass("active");
        });
    });
</script>
@stack('scripts')
</body>
</html>
