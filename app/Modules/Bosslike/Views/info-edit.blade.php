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
            <form method="POST" action="{{route('info.update',$user->id)}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="first_name">Имя</label>

                            <input id="first_name" type="text" name="first_name" value="{{$user->first_name}}"
                                   class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}">

                            @if ($errors->has('first_name'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="last_name">Фамилия</label>

                            <input id="last_name" type="text" name="last_name" value="{{$user->last_name}}"
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
                                <input type="radio" id="male" name="gender" class="custom-control-input"
                                       value="1" {{$user->gender==1?'checked':''}}>
                                <label class="custom-control-label" for="male">Мужской</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="female" name="gender" class="custom-control-input"
                                       value="2" {{$user->gender==2?'checked':''}}>
                                <label class="custom-control-label" for="female">Женский</label>
                            </div>

                        </div>
                        @if($user->avatar)
                            <div class="form-group">
                                <label for="avatar">Аватар</label>
                                <div class="col-md-3">
                                    <img src="{{ asset('/storage/uploads/'.$user->avatar) }}" alt=""
                                         class="img-thumbnail">
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="avatar">Загрузить/Изменить фото</label>

                            <input id="avatar" type="file"
                                   class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}" name="avatar"
                                   value="{{ $user->avatar == null ? old('avatar') :$user->avatar }}">

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
