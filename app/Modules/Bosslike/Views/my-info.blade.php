@extends('layouts.app')
@section('title','Мои данные')
@section('content')
    <div class="row justify-content-left">
        <div class="col-12 col-sm-12 col-md-12">
            <form method="POST" enctype="multipart/form-data" action="info/save" class="profile-form">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input id="imageUpload" type="file" accept=".png, .jpg, .jpeg"
                                           class="form-control{{ $errors->has('avatar') ? ' is-invalid' : '' }}"
                                           name="avatar"
                                           value="{{asset('images/ava.png')}}">
                                    <label for="imageUpload">
                                        <i class="fas fa-pencil-alt"></i>
                                    </label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="imagePreview"
                                         style="background-image:{{asset('images/ava.png')}}">
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

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="male" name="gender"
                                       class="custom-control-input form-control{{ $errors->has('gender') ? ' is-invalid' : '' }}"
                                       value="1">
                                <label class="custom-control-label" for="male"><i
                                        class="fas fa-mars"></i>Мужской</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="female" name="gender"
                                       class="custom-control-input form-control{{ $errors->has('gender') ? ' is-invalid' : '' }}"
                                       value="2">
                                <label class="custom-control-label" for="female"><i
                                        class="fas fa-venus"></i>Женский</label>
                            </div>
                            @if ($errors->has('gender'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('gender') }}</strong>
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
