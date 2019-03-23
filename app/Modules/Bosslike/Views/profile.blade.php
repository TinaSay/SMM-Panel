@extends('layouts.app')
@section('title','Прикрепление аккаунтов')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-sm-6 col-md-8 account-buttons">
            <div class="form-group">
                <label>Одноклассники</label>
                @if($ok)

                    <div class="user-data">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $ok->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $ok->name }}</div>
                        <div class="user-desc">
                            <form action="{{ route('ok-user.delete', $ok->id)}}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-default btn-sm delete-social"
                                        onclick="return confirm('Отвязать?')">
                                    <i class="fa fa-times"></i>
                                    Отвязать аккаунт
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <a href="{{ route('insta.login', ['provider' =>'odnoklassniki']) }}"
                       class="btn btn-block btn-lg btn-odnoklassniki">
                        <i class="fab fa-odnoklassniki"></i>
                        Привязать аккаунт Одноклассники</a>
                @endif
            </div>

            <div class="form-group">
                <label>Instagram</label>
                @if($instagram)

                    <div class="user-data">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $instagram->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $instagram->client_name }}</div>
                        <div class="user-desc">
                            <form action="social/user/delete/{{ $instagram->social_id }}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-default btn-sm delete-social"
                                        onclick="return confirm('Отвязать?')">
                                    <i class="fa fa-times"></i>
                                    Отвязать аккаунт
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <a href="{{ route('insta.login', ['provider' =>'instagram']) }}"
                       class="btn btn-block btn-lg btn-instagram">
                        <i class="fab fa-instagram"></i>
                        Привязать аккаунт Instagram</a>
                @endif
            </div>

            <div class="form-group">
                <label>Facebook</label>
                @if($facebook)

                    <div class="user-data">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $facebook->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $facebook->client_name }}</div>
                        <div class="user-desc">
                            <form action="social/user/delete/{{ $facebook->social_id }}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-default btn-sm delete-social"
                                        onclick="return confirm('Отвязать?')">
                                    <i class="fa fa-times"></i>
                                    Отвязать аккаунт
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <a href="/auth/facebook"
                       class="btn btn-block btn-lg btn-facebook">
                        <i class="fab fa-facebook"></i>
                        Привязать аккаунт Facebook</a>
                @endif
            </div>

            <div class="form-group">
                <label>Twitter</label>
                @if($twitter)

                    <div class="user-data">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $twitter->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $twitter->name }}</div>
                        <div class="user-desc">
                            <form action="social/user/delete/{{ $twitter->social_id }}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-default btn-sm delete-social"
                                        onclick="return confirm('Отвязать?')">
                                    <i class="fa fa-times"></i>
                                    Отвязать аккаунт
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <a href="{{ route('twitter.login') }}"
                       class="btn btn-block btn-lg btn-twitter">
                        <i class="fab fa-twitter"></i>
                        Привязать аккаунт Twitter</a>
                @endif
            </div>

            <div class="form-group telegram_auth_but">
                <label>Telegram</label>
                <div class="user-data">
                    <script async src="https://telegram.org/js/telegram-widget.js?5" data-telegram-login="PicStarBot" data-size="large" data-userpic="false" data-radius="10" data-auth-url="http://lastsmm.com/telegram" data-request-access="write"></script>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {

            var _success = $('#success-session').val();
            var _fail = $('#fail-session').val();
            if (_success != null) {
                window.toastr.success(_success);
            } else if (_fail != null) {
                window.toastr.error(_fail);
            }

        });

    </script>
@endpush
