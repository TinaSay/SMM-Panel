@extends('layouts.app')
@section('title','Новое задание')
@section('content')

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="intro-wrapper">
                @if ($intro)
                    <div class="intro alert alert-success alert-dismissable text-center">
                        @if (Auth::user()->role_id == \App\User::ROLE_ADMIN)
                            <div class="intro-editor">
                                <span id="intro" data-type="textarea" data-pk="2" data-url="/ajax/edit-intro" data-title="Редактировать">
                                    {!! $intro->description !!}
                                </span>
                            </div>
                        @else
                            {!! $intro->description !!}
                        @endif
                    </div>
                @else
                    @if (Auth::user()->role_id == \App\User::ROLE_ADMIN)
                        <div class="intro-editor">
                            <a href="#" id="intro" data-type="textarea" data-pk="2" data-url="/ajax/edit-intro" data-title="Редактировать">intro</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="add-new-task-form">

        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning alert-dismissable text-center">
                    <p>При создании задания, Вы указываете сумму, которую получит исполнитель <br/>
                        и удостоверьтесь, что аккаунт открыт и имеет аватарку.<br>Цена
                        для Вас рассчитывается ниже.</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('task.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <select
                            class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('social') ? ' is-invalid' : '' }}"
                            data-style="form-control"
                            name="social"
                            id="social">

                            @foreach($socials as $social)
                                <option
                                    data-content="<span><img src='<?php print asset("images/" . $social->icon); ?>'/>{{ $social->name }}</span>"
                                    {{ (old('social') == $social->id) ? 'selected' : '' }} value="{{ $social->id }}">
                                    {{ $social->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <select
                            class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('service_id') ? ' is-invalid' : '' }}"
                            data-style="form-control"
                            name="service_id"
                            id="service_id">
                        </select>
                        @if ($errors)
                            <input type="hidden" value="{{old('service_id')}}" class="old_service_id">
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('service_id') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row" id="fortelegram" style="display: none;">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissable text-center">
                        <p>
                            Прежде чем создать задание по Telegram, убедитесь что наш бот @PicStarBot <br>
                            уже добавлен в ваш канал. Если не добавлен, то обработка выполнения будет невозможна! <br>
                            Внимание!!! После добавления нашего бота в ваш канал, не забудьте назначить его <br>
                            администратором Это нужно чтобы проверять выполнения заданий.
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="link">Ссылка</label>

                        <input id="link" type="text" name="link" value="{{ old('link') }}"
                               class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}"
                               placeholder="https://" onfocus="this.placeholder = ''"
                               onblur="this.placeholder = 'https://'" required>

                        @if ($errors->has('link'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('link') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label>Скорость выполнения</label>
                        <div class="btn-group btn-group-justified form-control p-0" role="group" aria-label="Speed">
                            <button type="button" class="aactive btn btn-default btn-speed border-right" data-speed="1">
                                Медленно
                            </button>
                            <button type="button" class="btn btn-default btn-speed border-right" data-speed="3">Умеренно
                            </button>
                            <button type="button" class="btn btn-default btn-speed" data-speed="5">Быстро</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label>Приоритет задания</label>
                        <div class="btn-group btn-group-justified form-control p-0" role="group" id="priority">
                            <button type="button" class="btn btn-default border-right uzb aactive" data-value="uzb">Узб.
                            </button>
                            <button type="button" class="btn btn-default border-right sng" data-value="sng">СНГ</button>
                            <button type="button" class="btn btn-default uzbsng" data-value="uzbsng">СНГ + Узб.</button>
                        </div>
                        <input type="hidden" value="uzb" id="priority_input" name="priority">
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="points">Оплата исполнителю для Узбекистана</label>

                        <input id="points" type="number" min="30" name="points" value="{{ old('points') ?? 30 }}"
                               class="prices form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
                               placeholder="сумма" onfocus="this.placeholder = ''"
                               onblur="this.placeholder = 'сумма'">

                        @if ($errors->has('points'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('points') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="amount">Количество выполнений для Узбекистана</label>

                        <input id="amount" type="number" min="10" name="amount" value="{{ old('amount') ?? 10 }}"
                               class="prices form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}">

                        @if ($errors->has('amount'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-lg-6 col-md-6" style="display: none;" id="sngp">
                    <div class="form-group">
                        <label for="sng_points">Оплата исполнителю для СНГ</label>

                        <input id="sng_points" type="number" min="60" name="sng_points"
                               value="{{ old('sng_points') ?? 60 }}"
                               class="prices form-control"
                               placeholder="сумма">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6" style="display: none;" id="sngq">
                    <div class="form-group">
                        <label for="sng_amount">Количество выполнений для СНГ</label>

                        <input id="sng_amount" type="number" min="10" name="sng_amount"
                               value="{{ old('sng_amount') ?? 10 }}"
                               class="prices form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info alert-dismissable text-center">
                        <a href="#" class="close" data-dismiss="alert" aria-hidden="true">
                            <i class="icon-x-sm"></i></a>
                        Минимальная цена 1 выполнения для Узбекистана: <strong class="points_for_owner">
                            30&nbsp;</strong>
                        сумов
                        <br/>
                        Минимальная цена 1 выполнения для СНГ: <strong class="points_for_owner"> 60 </strong> сумов
                        <br>
                        Чем больше вы указываете сумму, тем быстрее выполняется задание.
                    </div>
                </div>
            </div>
            {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
            {{--<div class="form-group">--}}

            {{--<label for="gender">Выберите пол</label><br>--}}
            {{--<div class="custom-control custom-radio  d-inline-flex mr-3">--}}
            {{--<input type="radio" id="any-gender" name="gender"--}}
            {{--class="custom-control-input"--}}
            {{--value="3" checked>--}}
            {{--<label class="custom-control-label" for="any-gender">Любой</label>--}}
            {{--</div>--}}
            {{--<div class="custom-control custom-radio  d-inline-flex mr-3">--}}
            {{--<input type="radio" id="male-gender" name="gender"--}}
            {{--class="custom-control-input"--}}
            {{--value="1">--}}
            {{--<label class="custom-control-label" for="male-gender">Мужской</label>--}}
            {{--</div>--}}
            {{--<div class="custom-control custom-radio  d-inline-flex mr-3">--}}
            {{--<input type="radio" id="female-gender" name="gender"--}}
            {{--class="custom-control-input"--}}
            {{--value="2">--}}
            {{--<label class="custom-control-label" for="female-gender">Женский</label>--}}
            {{--</div>--}}

            {{--</div>--}}
            {{--<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut--}}
            {{--labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco--}}
            {{--laboris nisi ut aliquip ex ea commodo consequat.</p>--}}
            {{--</div>--}}
            {{--</div>--}}
            <div class="row">
                <div class="col-md-12">
                    <div class="comments-block d-none">
                        <h4>Комментарии</h4>
                        <p>Напишите комментарии, которые будут добавлять пользователи. Если вам нужны любые комментарии
                            -
                            просто оставьте эти поля пустыми.
                        </p>
                        <div class="comment-block">
                            <div class="comment-input">

                                <div class="comment-input-body">

                                    <h5>Комментарий <span class="comment-number"></span>
                                        <a href="#" class="remove-comment d-none">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </h5>
                                    <textarea name="comment_text[]"
                                              placeholder="Напишите комментарий, который должен оставить пользователь"
                                              class="form-control" id="comment-1" rows="2" maxlength="150"></textarea>
                                    @if(session()->has('error'))
                                        <h2>error</h2>
                                    @endif
                                    <input type="hidden" value="" class="counter" name="counter">

                                </div>
                            </div>

                        </div>
                        <div class="form-group comment-add mt-3">
                            <button type="button" class="btn btn-outline-primary add-comment-btn">Добавить ещё один
                            </button>
                            <span class="text-muted form-control-static">
                            <span class="comment-counter"></span> из 50 комментариев</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lilac submit-task">
                    Создать
                </button>
                <a class="btn btn-primary btn-gray" href="{{ route('tasks.my') }}">
                    Отмена
                </a>
                <span class="showPoints">
              <i class="icon-wallet"></i>
              <span class="totalPoints">0 </span> <span> сумов</span>
                <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="top" data-content="
Чтобы разорвать бесконечную циркуляцию суммы задания между пользователями, что привело бы к экспоненциальному
(постоянному, быстрому) росту цены за выполнения – указываемая цена делится на 2. Т.е., когда Вы видите
стоимость задания 30, значит заказчик поставил цену 60 сумов.">
                    <i class="fas fa-question-circle"></i>
                </button>
          </span>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}"/>
    <script type="text/javascript" src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script>

        var _socialId = $('#social').val();
        if (_socialId == 6){
            $('#fortelegram').show();
        }  else {
            $('#fortelegram').hide();
        }
        loadCategories(_socialId);
        $('#social').on('change', function () {
            var _socialId = $(this).val();
            if (_socialId == 6){
                $('#fortelegram').show();
            }  else {
                $('#fortelegram').hide();
            }
            loadCategories(_socialId);
        });


        function serviceName($name) {
            if ($name == 'Subscribe') {
                return 'Подписчики';
            } else if ($name == 'Like') {
                return 'Лайки';
            } else if ($name == 'Comment') {
                return 'Комментарии';
            } else if ($name == 'Share') {
                return 'Поделиться';
            } else if ($name == 'Watch') {
                return 'Просмотры';
            }
            return $name;
        }

        function loadCategories($socialId) {
            $.ajax({
                url: '/task/new/services/' + $socialId,
                type: 'GET',
                success: function (response) {
                    $('#service_id').empty();
                    var _old = $('.old_service_id').val();

                    $.each(response, function (k, v) {
                        $('#service_id').append('<option data-content="<span><img src=\'/images/' + v.icon + '\' />' + serviceName(v.name) + '</span>" value="' + v.id + '" data-name="' + v.name + '">' + serviceName(v.name) + '</option>');
                        if (_old) {

                            if (v.id == _old) {

                                $('#service_id option[value=' + v.id + ']').attr('selected', true);
                                if ($('#service_id :selected').attr('data-name') == 'Comment') {
                                    $('.comments-block').removeClass('d-none');
                                } else {
                                    $('.comments-block').addClass('d-none');
                                }
                            }
                        }
                    });

                    $('select#service_id').selectpicker('refresh');
                },
                error: function () {

                }
            })
        }

        $('select#social').selectpicker();
        



    </script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('/js/bootstrap3-editable/css/bootstrap-editable.css') }}">
<style>
    .editable-click, a.editable-click, a.editable-click:hover {
        border: none;
    }
    .editable-pre-wrapped {
        white-space: normal;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('/js/bootstrap3-editable/js/bootstrap-editable.min.js') }}"></script>
<script>
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.params = function (params) {
        params._token = $("meta[name=token]").attr("content");
        return params;
    };

    $(function () {
        $('#intro').editable();
        $('[data-toggle="popover"]').popover()
    });
</script>
@endpush
