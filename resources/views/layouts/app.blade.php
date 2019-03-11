<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    {{-----font-awesome--}}
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    {{----editor----}}
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
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
                        <img src="{{ asset('images/picstar.png') }}" alt="picstar">
                        <span class="logo-text">picstar</span>
                        <i class="fas fa-times close-menu d-lg-none"></i>
                    </a>
                </div>

                <div class="role-switcher bordered">
                    <label class="switcher-title active">Рекламодатель</label>
                    <label class="switch">
                        <input type="checkbox">
                        <a class="slider round">
                        </a>
                    </label>
                    <label class="switcher-title">Блогер</label>
                </div>

                @include('layouts.sidebar-bosslike')

                @if(Auth::user()->role_id==1)
                    @include('layouts.sidebar-admin')
                @endif
                @include('layouts.sidebar-user')

            </aside>
            {{--dashboard content section--}}
            <div class="side-right">
                <section>
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                @include('layouts.top-bar')
                            </div>
                        </div>
                    </div>
                </section>
                <section class="pb-3">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>@yield('title')</h2>

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
<script src="{{ asset('js/app.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{--editor--}}
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
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

        (function ($) {
            $("#side-menu").mCustomScrollbar({
                autoHideScrollbar: true,
                theme: "minimal-dark"
            });
        })(jQuery);

        $('.open-menu, .close-menu').on('click', function () {
            $('#side-menu').toggleClass('menu-show-up');
        });

        $('.slider').on('click', function () {
            $('.switcher-title').toggleClass('active');
            $('.advertiser').toggleClass('d-none');
            $('.blogger').toggleClass('d-none');

        });
    });
</script>
@stack('scripts')
</body>
</html>
