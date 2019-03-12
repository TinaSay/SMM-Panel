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
                <p><i class="fas fa-exclamation-circle"></i> Внимание, обновление логики формы</p>
                <p>Теперь при создании задания, Вы указываете количество баллов, которое получит исполнитель.<br>Цена
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
                    @if ($errors->has('service_id'))
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
                        <button type="button" class="btn btn-default border-right slow">Медленно</button>
                        <button type="button" class="btn btn-default border-right middle">Умеренно</button>
                        <button type="button" class="btn btn-default fast">Быстро</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="points">Оплата исполнителю</label>

                    <input id="points" type="number" min="0" name="points" value="{{ old('points') }}"
                           class="form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
                           placeholder="кол.баллов" onfocus="this.placeholder = ''"
                           onblur="this.placeholder = 'кол.баллов'">

                    @if ($errors->has('points'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('points') }}</strong>
                            </span>
                    @endif
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="form-group">
                    <label for="amount">Количество выполнений</label>

                    <input id="amount" type="number" min="0" name="amount" value="{{ old('amount') }}"
                           class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}">

                    @if ($errors->has('amount'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info alert-dismissable text-center">
                    <a href="#" class="close" data-dismiss="alert" aria-hidden="true">
                        <i class="icon-x-sm"></i></a>
                    Цена 1 выполнения для вас: <strong class="points_for_owner">0&nbsp;баллов</strong>
                    <br>
                    <em>Цена = Оплата исполнителю * 2 <a target="_blank" href="">подробнее</a></em>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lilac">
                Создать
            </button>
            <a class="btn btn-primary btn-gray" href="{{ route('tasks.my') }}">
                Отмена
            </a>
            <span class="totalPoints">0 </span> <span> баллов</span>
        </div>
    </form>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            var _socialId = $('#social').val();
            loadCategories(_socialId);
            $('#social').on('change', function () {
                var _socialId = $(this).val();
                loadCategories(_socialId);
            });

            $('#points').on('change paste keyup', function () {
                countPrice();
                totalPoints();
            });

            $('#amount').on('change paste keyup', function () {
                totalPoints();
            });

            function loadCategories($socialId) {
                $.ajax({
                    url: '/task/new/services/' + $socialId,
                    type: 'GET',
                    success: function (response) {
                        $('#service_id').empty();
                        $.each(response, function (k, v) {
                            $('#service_id').append('<option value="' + v.id + '">' + v.name + '</option>');
                        });
                    },
                    error: function () {
                        console.log('error');
                    }
                })
            }

            function countPrice() {
                var _price = $('#points').val() * 2;
                $('.points_for_owner').text(_price);
            }

            function totalPoints() {
                var _points = $('.points_for_owner').text();
                var _amount = $('#amount').val();
                var _totalPoints = _points * _amount;
                $('.totalPoints').text(_totalPoints);
            }

            function loadSpeed($speed) {
                var _social = $('#social option:selected').text();
                var _service = $('#service_id option:selected').text();

                $.ajax({
                    url: '/task/speed/' + _social + '/' + _service,
                    type: 'GET',
                    success: function (response) {
                        if ($speed == 'slow') {
                            $('#points').val(response.slow);
                        } else if ($speed == 'middle') {
                            $('#points').val(response.middle);
                        } else if ($speed == 'fast') {
                            $('#points').val(response.fast);
                        }
                        countPrice();
                        totalPoints();
                    },
                    error: function () {
                        console.log('error');
                    }
                })
            }

            $('.slow').on('click', function () {
                loadSpeed('slow');
                $(this).addClass('active');
                $('.middle').removeClass('active');
                $('.fast').removeClass('active');
            });
            $('.middle').on('click', function () {
                loadSpeed('middle');
                $(this).toggleClass('active');
                $('.slow').removeClass('active');
                $('.fast').removeClass('active');
            });
            $('.fast').on('click', function () {
                loadSpeed('fast');
                $(this).toggleClass('active');
                $('.slow').removeClass('active');
                $('.middle').removeClass('active');
            })

        })
    </script>
@endpush
