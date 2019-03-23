@extends('layouts.app')
@section('title','Мои данные')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif

    <div class="row justify-content-left">
        <div class="col-12 col-sm-12 col-md-12">
            <form method="POST" enctype="multipart/form-data" action="info/save">
                @csrf
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="first_name">Имя</label>

                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}">

                            @if ($errors->has('first_name'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="last_name">Фамилия</label>

                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}">

                            @if ($errors->has('last_name'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="gender">Пол</label><br>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="male" name="gender" class="custom-control-input" value="1">
                                <label class="custom-control-label" for="male">Мужской</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="female" name="gender" class="custom-control-input" value="2">
                                <label class="custom-control-label" for="female">Женский</label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="avatar">Загрузить фото</label>
                            <img src="" alt="" class="img-thumbnail d-none" id="preview">

                            <input id="avatar" type="file"
                                   class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}" name="avatar"
                                   value="{{ old('avatar') }}">

                            @if ($errors->has('avatar'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('avatar') }}</strong>
                            </span>
                            @endif
                        </div>


                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lilac">
                                Сохранить
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection
