@extends('layouts.app')
@section('title','Все задания')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif

    @if($tasks->isEmpty())
        <h3>Нет заданий</h3>
    @else

        @foreach($tasks as $task)
            <div class="card mb-3">
                <div class="card-body">
                    <input type="hidden" value="{{ $task->id }}" class="task_id">
                    @if($task->service->name == 'Comment')

                        <div class="card-details">
                            <img src="{{ (!empty($task->picture)) ? $task->picture : asset('images/picstar.png') }}" class="col-md-1 col-1 col-xl-1 rounded-circle mr-3" alt=""
                                 width="70px">
                            <span>{{ Bosslike::setServiceName($task->service->name) }}</span>
                            <a data-toggle="collapse" href="#oneTask_{{ $task->id }}" role="button" aria-expanded="false"
                               aria-controls="oneTask_{{ $task->id }}" class="withComments">
                                 {{ $task->link }}
                            </a>
                            <button type="button" data-id="{{ $task->id }}" data-url="{{ $task->link }}" data-toggle="collapse" data-target="#oneTask_{{ $task->id }}" aria-expanded="true"
                                    aria-controls="oneTask_{{ $task->id }}" class="btn btn-primary btn-block make_action_but withComments"><i class="fa fa-star" aria-hidden="true"></i>
                                {{ $task->points }} сум</button>
                            <span class="while_checking" style="display: none"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Проверка</span>
                            <span class="needs_checking" style="display: none"><i class="fa fa-eye" aria-hidden="true"></i> Проверить</span>
                            <span class="is_ready" style="display: none"><i class="fa fa-check-circle" aria-hidden="true"></i> Выполнено</span>
                            <span class="is_initial" style="display: none"><i class="fa fa-star" aria-hidden="true"></i> {{ $task->points }} сум</span>
                        </div>

                        <div class="panel-extra collapse" id="oneTask_{{ $task->id }}" aria-expanded="true" style="">
                            <div class="panel-action">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        @if(count($task->comments) > 0)
                                            <?php $randTask = $task->comments[mt_rand(0, count($task->comments) - 1)]; ?>
                                            <div class="form-group comment-place">
                                                <label class="control-label" for="taskComment_{{ $randTask->id }}">Текст комментария <span class="help-block">(скопируйте и вставьте на странице задания)</span></label>
                                                <input readonly="readonly" type="text" name="taskComment_{{ $randTask->id }}" class="form-control randComment" data-id="{{ $randTask->id }}" value="{{ $randTask->text }}" />
                                                <button class="copy_to_clipboard"><i class="fa fa-copy"></i></button>
                                            </div>
                                        @else
                                            <div class="form-group comment-place"><label class="control-label">Текст комментария</label>
                                                <div class="alert alert-info"><strong>Произвольный текст</strong>.
                                                    Напишите осознанный комментарий к записи.<br><em>Дискредитирующие заказчика комментарии строго запрещены, нарушение может привести к блокировке профиля.</em>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-primary do-comment-close" type="button" data-toggle="collapse" data-target="#oneTask_{{ $task->id }}" aria-expanded="true" aria-controls="oneTask_{{ $task->id }}">Отмена</button>
                                    <button data-id="{{ $task->id }}" data-url="{{ $task->link }}" class="do-action btn btn-primary btn-block">Оставить комментарий</button>
                                </div>
                            </div>
                        </div>

                    @else

                        <div class="card-details">
                            <img src="{{ (!empty($task->picture)) ? $task->picture : asset('images/picstar.png') }}" class="col-md-1 col-1 col-xl-1 rounded-circle mr-3" alt=""
                                 width="70px">
                            <span>{{ Bosslike::setServiceName($task->service->name) }}</span>
                            <a href="{{ $task->link }}" target="_blank">
                                {{ $task->link }}
                            </a>

                            <button data-id="{{ $task->id }}" data-url="{{ $task->link }}" data-check="false" class="do-action btn btn-primary btn-block make_action_but"><i class="fa fa-star" aria-hidden="true"></i> {{ $task->points }} сум</button>
                            <span class="while_checking" style="display: none"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Проверка</span>
                            <span class="needs_checking" style="display: none"><i class="fa fa-eye" aria-hidden="true"></i> Проверить</span>
                            <span class="is_ready" style="display: none"><i class="fa fa-check-circle" aria-hidden="true"></i> Выполнено</span>
                            <span class="is_initial" style="display: none"><i class="fa fa-star" aria-hidden="true"></i> {{ $task->points }} сум</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.do-action', function (event) {
                var $this = $(this),
                    $id = $this.attr('data-id'),
                    $url = $this.attr('data-url'),
                    $commentId = $this.parents('.panel-extra').find('.randComment').data('id'),
                    $check = $this.attr('data-check');
                if($check === "true") {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    checkTask($id, $commentId, $this, $check);
                } else {
                    popUp = window.open($url, "thePopUp", "width=600,height=600");
                }
                $this.removeClass('do-action');
                function someFunctionToCallWhenPopUpCloses() {
                    window.setTimeout(function() {
                        if (popUp.closed) {
                            checkTask($id, $commentId, $this, $check);
                        }
                    }, 1);
                }

                if($check !== "true") {
                    var win = window.open($url, "thePopUp", "width=500,height=500");
                    var pollTimer = window.setInterval(function () {
                        if (win.closed !== false) {
                            window.clearInterval(pollTimer);
                            someFunctionToCallWhenPopUpCloses();
                        }
                    }, 200);
                }
            });

            $(document).on('click', '.copy_to_clipboard', function (e) {
                var element = $(this).prev('.randComment').val();
                copyToClipboard(element);
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
                url: '/tasks/check/'+$id,
                type: 'GET',
                data: {_token: $csrf, comment: $commentId, check: $check},
                success: function (resp) {
                    if(resp.original.status === 'error') {
                        if($this.attr('data-check') === "false") {
                            $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.needs_checking').html());
                            $this.parents('.card-body').find('.make_action_but.withComments').addClass('do-action');
                            $this.parents('.card-body').find('.make_action_but').attr('data-check', "true");
                        } else {
                            $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.is_initial').html());
                            $this.parents('.card-body').find('.make_action_but.withComments').removeClass('do-action');
                            $this.parents('.card-body').find('.make_action_but').attr('data-check', "false");
                        }
                        $this.not('.withComments').addClass('do-action');
                    } else {
                        $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.is_ready').html());
                    }
                    toastr[resp.original.status](resp.original.title, resp.original.message);
                }
            });
        }
    </script>
@endpush
