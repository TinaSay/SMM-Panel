@extends('layouts.app')
@section('title','Все задания')
@section('content')
    @if($tasks->isEmpty())
        <h3>Нет заданий</h3>
    @else
        <div class="row justify-content-center">

            @foreach($tasks as $task)
                <div class="col-md-12 card mb-4" data-id="{{$task->id}}">
                    <div class="card-body" data-social="{{ $task->service->social->name }}">
                        <input type="hidden" value="{{ $task->id }}" class="task_id">
                        @if($task->service->name == 'Comment')

                            <div class="card-details">
                                <div class="d-flex justify-content-between">
                                    <i class="left-icon far fa-comment"></i>
                                    <div class="img-holder"
                                         style="margin-left:-20px; background-image: url({{ (!empty($task->picture)) ? $task->picture : asset('images/picstar.png') }})">
                                    </div>
                                    <span class="soc-badge">
                                        @if($task->service->social->name =='Facebook')
                                            <i class="fab fa-facebook"></i>
                                        @elseif($task->service->social->name =='Instagram')
                                            <i class="fab fa-instagram"></i>
                                        @else
                                            <i class="fas fa-info-circle"></i>
                                        @endif
                                    </span>
                                    <div class="col-sm-5 col-md-4 col-lg-6">
                                        <p>{{ Bosslike::setServiceName($task->service->name) }}
                                            <a data-toggle="collapse" href="#oneTask_{{ $task->id }}" role="button"
                                               aria-expanded="false"
                                               aria-controls="oneTask_{{ $task->id }}" class="withComments">
                                                {{ Bosslike::setTypeName($task->type) }}
                                            </a></p>
                                    </div>

                                    <div class="col-sm-3 col-md-4 col-lg-3">
                                        <button type="button" data-id="{{ $task->id }}"
                                                data-toggle="collapse" data-check="false"
                                                data-target="#oneTask_{{ $task->id }}"
                                                aria-expanded="true"
                                                aria-controls="oneTask_{{ $task->id }}"
                                                class="btn btn-lilac btn-lilac-sm make_action_but withComments link_but"><i
                                                class="far fa-star" aria-hidden="true"></i>
                                            {{ $task->points }} сум
                                        </button>
                                        <span class="while_checking" style="display: none"><i
                                                class="fa fa-spinner fa-spin"
                                                aria-hidden="true"></i> Проверка</span>
                                        <span class="needs_checking" style="display: none"><i class="fa fa-eye"
                                                                                              aria-hidden="true"></i> Проверить</span>
                                        <span class="is_ready" style="display: none"><i class="fa fa-check-circle"
                                                                                        aria-hidden="true"></i> Выполнено</span>
                                        <span class="is_initial" style="display: none"><i class="fa fa-star"
                                                                                          aria-hidden="true"></i> {{ $task->points }} сум</span>
                                    </div>
                                    <a href="" data-toggle="tooltip" title="Скрыть" onclick="hide({{ $task->id }})"><i
                                            class="far fa-times-circle close-icon"></i></a>
                                </div>
                            </div>

                            <div class="panel-extra collapse" id="oneTask_{{ $task->id }}" aria-expanded="true"
                                 style="">
                                <div class="panel-action">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            @if(count($task->comments) > 0)
                                                <?php $randTask = $task->comments[mt_rand(0, count($task->comments) - 1)]; ?>
                                                <div class="form-group comment-place">
                                                    <label class="control-label" for="taskComment_{{ $randTask->id }}">Текст
                                                        комментария <span class="help-block">(скопируйте и вставьте на странице задания)</span></label>
                                                    <input readonly="readonly" type="text"
                                                           name="taskComment_{{ $randTask->id }}"
                                                           class="form-control randComment"
                                                           data-id="{{ $randTask->id }}" value="{{ $randTask->text }}"/>
                                                    <button class="copy_to_clipboard"><i class="fa fa-copy"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="form-group comment-place"><label class="control-label">Текст
                                                        комментария</label>
                                                    <div class="alert alert-info"><strong>Произвольный текст</strong>.
                                                        Напишите осознанный комментарий к записи.<br><em>Дискредитирующие
                                                            заказчика комментарии строго запрещены, нарушение может
                                                            привести к блокировке профиля.</em>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button class="btn btn-primary do-comment-close" type="button"
                                                data-toggle="collapse" data-target="#oneTask_{{ $task->id }}"
                                                aria-expanded="true" aria-controls="oneTask_{{ $task->id }}">Отмена
                                        </button>
                                        <button data-id="{{ $task->id }}"
                                                data-check="false" class="do-action btn btn-primary link_but">Оставить
                                            комментарий
                                        </button>
                                    </div>
                                </div>
                            </div>

                        @else

                            <div class="card-details">
                                <div class="d-flex justify-content-between">
                                    @if($task->service->name == 'Like')
                                        <i class="left-icon far fa-heart"></i>
                                    @elseif($task->service->name == 'Subscribe')
                                        <i class="left-icon far fa-user"></i>
                                    @elseif($task->service->name == 'Share')
                                        <i class="left-icon far fa-share-square"></i>
                                    @else
                                        <i class="left-icon fas fa-user-tie"></i>
                                    @endif
                                    <div class="img-holder"
                                         style="margin-left:-20px; background-image: url({{ (!empty($task->picture)) ? $task->picture : asset('images/picstar.png') }})">
                                    </div>
                                    <span class="soc-badge">
                                        @if($task->service->social->name =='Facebook')
                                            <i class="fab fa-facebook"></i>
                                        @elseif($task->service->social->name =='Instagram')
                                            <i class="fab fa-instagram"></i>
                                        @else
                                            <i class="fas fa-info-circle"></i>
                                        @endif
                                        </span>
                                    <div class="col-sm-5 col-md-4 col-lg-6">
                                        <p>{{ Bosslike::setServiceName($task->service->name) }}
                                            <a data-id="{{ $task->id }}"
                                               target="_blank" class="do-action link_but" data-check="false">

                                                @if($task->service->name=='Subscribe')
                                                    <span> на </span>  {{Bosslike::setTypeName($task->type) }}
                                                @else
                                                    {{Bosslike::setTypeName($task->type) }}
                                                @endif
                                                {{$task->post_name}}
                                                {{--{{ $task->type=='page' ? $task->post_name :'' }}--}}
                                            </a></p>
                                    </div>
                                    <div class="col-sm-3 col-md-4 col-lg-3">
                                        <button data-id="{{ $task->id }}"
                                                data-check="false"
                                                class="do-action btn btn-lilac btn-lilac-sm make_action_but link_but"><i
                                                class="far fa-star" aria-hidden="true"></i> {{ $task->points }} сум
                                        </button>
                                        <span class="while_checking" style="display: none"><i
                                                class="fa fa-spinner fa-spin"
                                                aria-hidden="true"></i> Проверка</span>
                                        <span class="needs_checking" style="display: none"><i class="fa fa-eye"
                                                                                              aria-hidden="true"></i> Проверить</span>
                                        <span class="is_ready" style="display: none"><i class="fa fa-check-circle"
                                                                                        aria-hidden="true"></i> Выполнено</span>
                                        <span class="is_initial" style="display: none"><i class="far fa-star"
                                                                                          aria-hidden="true"></i> {{ $task->points }} сум</span>
                                    </div>
                                    <a href="#" data-toggle="tooltip" title="Скрыть" onclick="hide({{ $task->id }})"><i
                                            class="far fa-times-circle close-icon"></i></a>

                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
@push('functions')
    <script>
        function hide(task) {
            var $csrf = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '/task/hide/' + task,
                type: 'GET',
                data: {_token: $csrf},
                success: function (resp) {
                    $(".card[data-id=" + task + "]").addClass("d-none");
                    toastr.success('Задание скрыто из вашей ленты');
                }
            });
        }

        function checkConnectedProfile(id) {
            var $csrf = $('meta[name="csrf-token"]').attr('content');
            return $.ajax({
                url: '/profile/check/' + id,
                type: 'GET',
                data: {_token: $csrf}
                // success: function (resp) {
                //     return resp;
                // }
            });
        }
    </script>
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.do-action', function (event) {

                var $this = $(this),
                    $id = $this.attr('data-id'),
                    $url = '/task/show/' + $id,
                    $commentId = $this.parents('.card-body').find('.randComment').data('id'),
                    $check = $this.attr('data-check'),
                    windowParams = 1000;

                var profileCheck = checkConnectedProfile($id);
                profileCheck.then(function (profData) {
                    if(profData.status) {
                        if ($check === "true") {
                            event.preventDefault();
                            event.stopImmediatePropagation();
                            checkTask($id, $commentId, $this, $check);
                        } else {
                            popUp = window.open($url, "thePopUp", "width=" + windowParams + ",height=" + windowParams);
                        }
                        $this.parents('.card-body').find('.do-action').removeClass('do-action');

                        function someFunctionToCallWhenPopUpCloses() {
                            window.setTimeout(function () {
                                if (popUp.closed) {
                                    checkTask($id, $commentId, $this, $check);
                                }
                            }, 1);
                        }

                        if ($check !== "true") {
                            var win = window.open($url, "thePopUp", "width=" + windowParams + ",height=" + windowParams);
                            var pollTimer = window.setInterval(function () {
                                if (win.closed !== false) {
                                    window.clearInterval(pollTimer);
                                    someFunctionToCallWhenPopUpCloses();
                                }
                            }, 200);
                        }
                    } else {
{{--                        {{ Session::put('success', 'Подключите аккаунт ' + profData.social + ') }};--}}
//                         toastr.warning('Подключите аккаунт ' + profData.social + ', чтобы продолжить', 'Нет аккаунта ' + profData.social);
                        window.location.href = '/profile';
                    }
                });
            });

            $(document).on('click', '.copy_to_clipboard', function (e) {
                var element = $(this).prev('.randComment').val();
                copyToClipboard(element);
            });

            $('.close-icon').on('click', function (e) {
                e.preventDefault();
            })
        });

        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(element).select();
            document.execCommand("copy");
            $temp.remove();
        }

        function checkTask($id, $commentId, $this, $check) {
            var $csrf = $('meta[name="csrf-token"]').attr('content');
            $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.while_checking').html());
            $.ajax({
                url: '/tasks/check/' + $id,
                type: 'GET',
                data: {_token: $csrf, comment: $commentId, check: $check},
                success: function (resp) {
                    if (resp.original.status !== 'success') {
                        if ($this.parents('.card-body').find('.make_action_but').attr('data-check') === "false") {
                            $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.needs_checking').html());
                            $this.parents('.card-body').find('.make_action_but.withComments').addClass('do-action');
                            $this.parents('.card-body').find('.make_action_but').attr('data-check', "true");
                        } else {
                            $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.is_initial').html());
                            $this.parents('.card-body').find('.make_action_but.withComments').removeClass('do-action');
                            $this.parents('.card-body').find('.make_action_but').attr('data-check', "false");
                        }
                        $this.parents('.card-body').find('.link_but').not('.withComments').addClass('do-action');
                    } else {
                        $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.is_ready').html());
                        // $this.parents('.card-body').parent().slideUp(700);
                    }
                    toastr[resp.original.status](resp.original.title, resp.original.message);
                }
            });
        }
    </script>
@endpush
