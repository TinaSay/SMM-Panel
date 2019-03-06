@extends('layouts.app')
@section('title','Новое задание')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif

    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('task.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <select
                        class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('social') ? ' is-invalid' : '' }}"
                        data-style="form-control"
                        name="social"
                        id="social">

                        @foreach($socials as $social)
                            <option value="{{ $social->id }}">
                                {{ $social->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

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

                <div class="form-group">
                    <label for="points">Оплата исполнителю</label>

                    <input id="points" type="number" name="points" value="{{ old('points') }}"
                           class="form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
                           placeholder="кол.баллов" onfocus="this.placeholder = ''"
                           onblur="this.placeholder = 'кол.баллов'">

                    @if ($errors->has('points'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('points') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="amount">Количество выполнений</label>

                    <input id="amount" type="number" name="amount" value="{{ old('amount') }}"
                           class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}">

                    @if ($errors->has('amount'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                    @endif
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lilac">
                        Создать
                    </button>
                    <a class="btn btn-primary btn-gray" href="{{ route('tasks.my') }}">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            loadCategories(1);
            $('#social').on('change', function () {
                var _socialId = $(this).val();
                loadCategories(_socialId);
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
        })
    </script>
@endpush
