@extends('layouts.app')
@section('title','Новое задание')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissable text-center">
                <p>При создании задания, Вы указываете сумму, которую получит исполнитель.<br>Цена
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
                            <option {{ (old('social') == $social->id) ? 'selected' : '' }} value="{{ $social->id }}">
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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="link">Ссылка</label>

                    <input id="link" type="text" name="link" value="{{ old('link') }}"
                           class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}"
                           placeholder="http://" onfocus="this.placeholder = ''"
                           onblur="this.placeholder = 'http://'">

                    @if ($errors->has('link'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('link') }}</strong>
                            </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
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
            <div class="col-md-6 col-sm-6 col-xs-12">
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
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="points">Оплата исполнителю для Узбекистана</label>

                    <input id="points" type="number" min="10" name="points" value="{{ old('points') ?? 10 }}"
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
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="amount">Количество выполнений для Узбекистана</label>

                    <input id="amount" type="number" min="1" name="amount" value="{{ old('amount') ?? 10 }}"
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

            <div class="col-md-6 col-sm-6 col-xs-12" style="display: none;" id="sngp">
                <div class="form-group">
                    <label for="sng_points">Оплата исполнителю для СНГ</label>

                    <input id="sng_points" type="number" min="30" name="sng_points"
                           value="{{ old('sng_points') ?? 30 }}"
                           class="prices form-control"
                           placeholder="сумма">
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12" style="display: none;" id="sngq">
                <div class="form-group">
                    <label for="sng_amount">Количество выполнений для СНГ</label>

                    <input id="sng_amount" type="number" min="1" name="sng_amount" value="{{ old('sng_amount') ?? 1 }}"
                           class="prices form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info alert-dismissable text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-hidden="true">
                        <i class="icon-x-sm"></i></a>
                    Минимальная цена 1 выполнения для Узбекистана: <strong class="points_for_owner"> 10&nbsp;</strong>
                    сумов
                    </br>
                    Минимальная цена 1 выполнения для СНГ: <strong class="points_for_owner"> 30 </strong> сумов
                    <br>
                    {{--<em>Цена = Оплата исполнителю * 2 <a target="_blank" href="">подробнее</a></em>--}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="comments-block d-none">
                    <h4>Комментарии</h4>
                    <p>Напишите комментарии, которые будут добавлять пользователи. Если вам нужны любые комментарии -
                        просто оставьте эти поля пустыми.
                    </p>
                    <div class="comment-block">
                        <div class="comment-input">

                            <div class="comment-input-body">

                                <h5>Комментарий <span class="comment-number"></span>
                                    <a href="#" class="remove-comment d-none">
                                        <i class="fas fa-trash"></i>
                                    </a></h5>
                                <textarea name=""
                                          placeholder="Напишите комментарий, который должен оставить пользователь"
                                          class="form-control" rows="2" maxlength="150"></textarea>
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
              <i class="far fa-star"></i>
              <span class="totalPoints">0 </span> <span> сумов</span>
          </span>
        </div>
    </form>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            var session = $('#success-session').val();
            if (session != null) {
                window.toastr.success(session);
            }

            var $speed = 1;
            totalPoints();


            $('.comment-number').text(1);
            $('.comment-counter').text(1);

            $('.add-comment-btn').on('click', function () {
                $('.comment-input').last().clone().addClass('next').appendTo($('.comment-block')).find('textarea').val('');
                $('.next').find('.remove-comment').removeClass('d-none');
                $('.comment-number, .comment-counter').text(function (index) {
                    return (index + 1);
                });
            });

            $(document).on('click', '.remove-comment i', function (e) {
                e.preventDefault();
                $(this).parent().parent().parent().parent().remove();
            });

            $('#service_id').on('change', function () {
                $selectedService = $(this).find(':selected').attr('data-name');

                if ($selectedService == 'Comment') {
                    $('.comments-block').removeClass('d-none');
                } else {
                    $('.comments-block').addClass('d-none');
                }
            });

            $('.btn-speed').on('click', function () {
                $speed = parseInt($(this).attr('data-speed'));
                $('.btn-speed').removeClass('aactive');
                $(this).addClass('aactive');

                $value = $('#priority_input').val();
                $points = $('#points').val();
                if ($value == 'sng') {
                    $('#points').val(30 * $speed);
                } else {
                    $('#points').val(10 * $speed);
                }

                $points = $('#sng_points').val();
                $('#sng_points').val(30 * $speed);
                totalPoints();
            });
            $('#priority .btn').on('click', function () {
                $value = $(this).attr('data-value');
                $('#priority .btn').removeClass('aactive');
                $(this).addClass('aactive');
                $('#priority_input').val($value);
                if ($value == 'uzbsng') {
                    $('#sngp').show();
                    $('#sngq').show();
                    $('label[for="points"]').html('Оплата исполнителю для Узбекистана');
                    $('label[for="amount"]').html('Количество выполнений для Узбекистана');
                } else if ($value == 'sng') {
                    $('#sngp').hide();
                    $('#sngq').hide();
                    $('label[for="points"]').html('Оплата исполнителю для СНГ.');
                    $('label[for="amount"]').html('Количество выполнений для СНГ.');
                } else {
                    $('label[for="points"]').html('Оплата исполнителю для Узбекистана');
                    $('label[for="amount"]').html('Количество выполнений для Узбекистана');
                }
                totalPoints();
            });


            $('.prices').on('input change paste keyup', function () {
                $id = $(this).attr('id');
                if ($id == 'points') {
                    $('.btn-speed').removeClass('aactive');
                    if ($(this).val() < 10) {
                        $('.btn-speed[data-speed="1"]').addClass('aactive');
                    } else if ($(this).val() < 30) {
                        $('.btn-speed[data-speed="3"]').addClass('aactive');
                    } else {
                        $('.btn-speed[data-speed="5"]').addClass('aactive');
                    }
                } else if ($id == 'sng_points') {
                    $('.btn-speed').removeClass('aactive');
                    if ($(this).val() < 30) {
                        $('.btn-speed[data-speed="1"]').addClass('aactive');
                    } else if ($(this).val() < 60) {
                        $('.btn-speed[data-speed="3"]').addClass('aactive');
                    } else {
                        $('.btn-speed[data-speed="5"]').addClass('aactive');
                    }
                }
                totalPoints();
            });

            function totalPoints() {
                $value = $('#priority_input').val();

                var _points = $('#points').val();
                var _amount = $('#amount').val();
                var _totalPoints = _points * _amount;
                if ($value === 'uzbsng') {
                    var sng_points = $('#sng_points').val();
                    var sng_amount = $('#sng_amount').val();
                    var sng_totalPoints = sng_points * sng_amount;
                    _totalPoints = _totalPoints + sng_totalPoints;
                }
                $('.totalPoints').text(_totalPoints);
            }


            var _socialId = $('#social').val();
            loadCategories(_socialId);
            $('#social').on('change', function () {
                var _socialId = $(this).val();
                loadCategories(_socialId);
            });


            function serviceName($name) {
                if ($name == 'Subscribe') {
                    return 'Подписаться';
                } else if ($name == 'Like') {
                    return 'Лайкнуть';
                } else if ($name == 'Comment') {
                    return 'Оставить комментарий';
                } else if ($name == 'Share') {
                    return 'Поделиться';
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
                            $('#service_id').append('<option value="' + v.id + '" data-name="' + v.name + '">' + serviceName(v.name) + '</option>');
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
                    },
                    error: function () {

                    }
                })
            }


        })
    </script>
@endpush
