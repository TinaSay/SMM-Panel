@extends('layouts.app')
@section('title','Прикрепление аккаунтов')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-sm-6 col-md-8 account-buttons">
            {{--<div class="form-group">
                <label>Одноклассники</label>
                @if($ok)

                    <div class="user-data">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $ok->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $ok->name }}</div>
                        <div class="user-social-info">
                            <span>555621515 Подписчиков</span>
                            <span>899025 Подписок</span>
                        </div>
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
            </div>--}}

            {{--<div class="form-group">--}}
                {{--<label>Facebook</label>--}}
                {{--@if($facebook)--}}

                    {{--<div class="user-data" id="facebookAcc">--}}
                        {{--<div class="user-avatar float-left mr-3">--}}
                            {{--<img src="{{ $facebook->avatar }}" alt="" class="img-thumbnail">--}}
                        {{--</div>--}}
                        {{--<div class="user-name">{{ $facebook->client_name }}</div>--}}
                        {{--<div class="user-social-info">--}}
                            {{--<span><span class="user-social-info-number">{{ $facebook->user_info_1 }}</span> отметок "нравится"</span>--}}
                            {{--<span><span class="user-social-info-number">{{ $facebook->user_info_2 }}</span> публикаций</span>--}}
                            {{--<span class="update-user-social-info" data-social="{{ $facebook->id }}" title="Обновить статистику"><i class="fas fa-sync"></i></span>--}}
                        {{--</div>--}}
                        {{--<div class="user-desc">--}}
                            {{--<form action="social/user/delete/{{ $facebook->social_id }}" method="POST">--}}
                                {{--@method('DELETE')--}}
                                {{--@csrf--}}
                                {{--<button class="btn btn-default btn-sm delete-social"--}}
                                        {{--onclick="return confirm('Отвязать?')">--}}
                                    {{--<i class="fa fa-times"></i>--}}
                                    {{--Отвязать аккаунт--}}
                                {{--</button>--}}
                            {{--</form>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--@else--}}
                    {{--<a href="/auth/facebook"--}}
                       {{--class="btn btn-block btn-lg btn-facebook">--}}
                        {{--<i class="fab fa-facebook"></i>--}}
                        {{--Привязать аккаунт Facebook</a>--}}
                {{--@endif--}}
            {{--</div>--}}

            <div class="form-group">
                <label>Instagram</label>
                @if($instagram)

                    <div class="user-data" id="instagramAcc">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $instagram->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $instagram->client_name }}</div>
                        <div class="user-social-info">
                            <span><span class="user-social-info-number">{{ $instagram->user_info_1 }}</span> публикаций</span>
                            <span><span class="user-social-info-number">{{ $instagram->user_info_3 }}</span> подписчиков</span>
                            <span><span class="user-social-info-number">{{ $instagram->user_info_2 }}</span> подписок</span>
                            <span class="update-user-social-info" data-social="{{ $instagram->id }}" title="Обновить статистику"><i class="fas fa-sync"></i></span>
                        </div>
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
                <label>Youtube</label>
                @if($youtube)

                    <div class="user-data" id="youtubeAcc">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $youtube->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $youtube->client_name }}</div>
                        <div class="user-social-info">
                            <span><span class="user-social-info-number">{{ $youtube->user_info_1 }}</span> просмотров</span>
                            <span><span class="user-social-info-number">{{ $youtube->user_info_2 }}</span> подписчиков</span>
                            <span><span class="user-social-info-number">{{ $youtube->user_info_3 }}</span> видео</span>
                            <span class="update-user-social-info" data-social="{{ $youtube->id }}" title="Обновить статистику"><i class="fas fa-sync"></i></span>
                        </div>
                        <div class="user-desc">
                            <form action="social/user/delete/{{ $youtube->social_id }}" method="POST">
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
                    <a href="/youtube/login"
                       class="btn btn-block btn-lg btn-youtube">
                        <i class="fab fa-youtube"></i>
                        Привязать аккаунт Youtube</a>
                @endif
            </div>


            {{--<div class="form-group">--}}
                {{--<label>Twitter</label>--}}
                {{--@if($twitter)--}}

                    {{--<div class="user-data">--}}
                        {{--<div class="user-avatar float-left mr-3">--}}
                            {{--<img src="{{ $twitter->avatar }}" alt="" class="img-thumbnail">--}}
                        {{--</div>--}}
                        {{--<div class="user-name">{{ $twitter->name }}</div>--}}
                        {{--<div class="user-social-info">--}}
                            {{--<span><span class="user-social-info-number">23000</span> Подписчиков</span>--}}
                            {{--<span>50000 Подписок</span>--}}
                        {{--</div>--}}
                        {{--<div class="user-desc">--}}
                            {{--<form action="social/user/delete/{{ $twitter->social_id }}" method="POST">--}}
                                {{--@method('DELETE')--}}
                                {{--@csrf--}}
                                {{--<button class="btn btn-default btn-sm delete-social"--}}
                                        {{--onclick="return confirm('Отвязать?')">--}}
                                    {{--<i class="fa fa-times"></i>--}}
                                    {{--Отвязать аккаунт--}}
                                {{--</button>--}}
                            {{--</form>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--@else--}}
                    {{--<a href="{{ route('twitter.login') }}"--}}
                       {{--class="btn btn-block btn-lg btn-twitter">--}}
                        {{--<i class="fab fa-twitter"></i>--}}
                        {{--Привязать аккаунт Twitter</a>--}}
                {{--@endif--}}
            {{--</div>--}}

            <div class="form-group">
                <label>Telegram</label>
                @if($tg)

                    <div class="user-data" id="telegramAcc">
                        <div class="user-avatar float-left mr-3">
                            <img src="{{ $tg->avatar }}" alt="" class="img-thumbnail">
                        </div>
                        <div class="user-name">{{ $tg->client_name }}</div>
                        <div class="user-social-info">
                            <span><span class="user-social-info-number">{{ $tg->user_info_1 }}</span> Имя</span>
                            <span><span class="user-social-info-number">{{ date('d.m.Y', $tg->user_info_2) }}</span> Дата подключения</span>
                        </div>
                        <div class="user-desc">
                            <form action="social/user/delete/{{ $tg->social_id }}" method="POST">
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
                <div class="user-data">
                    <script async src="https://telegram.org/js/telegram-widget.js?5" data-telegram-login="PicStarBot" data-size="large" data-userpic="false" data-radius="10" data-auth-url="https://picstar.uz/telegram" data-request-access="write"></script>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
