@extends('layouts.app')
@section('title','Мои данные')
@section('content')
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
                            <label for="gender">Пол</label><br>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="male" name="gender" class="custom-control-input"
                                       value="1" {{$user->gender==1?'checked':''}}>
                                <label class="custom-control-label" for="male"><i
                                        class="fas fa-mars"></i>Мужской</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="female" name="gender" class="custom-control-input"
                                       value="2" {{$user->gender==2?'checked':''}}>
                                <label class="custom-control-label" for="female"><i
                                        class="fas fa-venus"></i>Женский</label>
                            </div>

                        </div>
                        {{--@if($user->avatar)
                            <div class="form-group">
                                <label for="avatar">Аватар</label>
                                <div class="col-md-3">
                                    <img src="{{ asset('uploads/'.$user->avatar) }}" alt=""
                                         class="img-thumbnail">
                                </div>
                            </div>
                        @endif--}}
                        <div class="form-group">
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input id="imageUpload" type="file" accept=".png, .jpg, .jpeg"
                                           class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}"
                                           name="avatar"
                                           value="{{ $user->avatar == null ? asset('images/ava.png') :asset('uploads/'.$user->avatar)}}">
                                    <label for="imageUpload">
                                        <i class="fas fa-pencil-alt"></i>
                                    </label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="imagePreview"
                                         style="background-image:{{ $user->avatar==null ? asset('images/ava.png') : asset('uploads/'.$user->avatar) }}">
                                    </div>
                                </div>

                                @if ($errors->has('avatar'))
                                    <span class="invalid-feedback" role="alert">
                                     <strong>{{ $errors->first('avatar') }}</strong>
                                    </span>
                                @endif
                            </div>
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
@push('scripts')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#imageUpload").change(function () {
            readURL(this);
        });

    </script>
@endpush
