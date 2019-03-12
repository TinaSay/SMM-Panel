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

                        <a data-toggle="collapse" href="#oneTask_{{ $task->id }}" role="button" aria-expanded="false" aria-controls="oneTask_{{ $task->id }}" class="withComments">
                            <h4><strong>{{ Bosslike::setServiceName($task->service->name) }}</strong> {{ $task->link }}
                            </h4>
                        </a>
                        <div class="card-details">
                            <span class="totalPoints" data-id="{{$task->id}}"></span> сум (<span class="point"
                                                                                                    data-id="{{$task->id}}"></span>
                            за задание)
                            {{ \Carbon\Carbon::parse($task->created_at)->format('d.m.Y')}}

                            <button type="button" data-toggle="collapse" data-target="#oneTask_{{ $task->id }}" aria-expanded="true" aria-controls="oneTask_{{ $task->id }}" class="btn btn-primary btn-block">{{ $task->points }} сум</button>
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
                        <a href="{{ $task->link }}" target="_blank">
                            <h4><strong>{{ Bosslike::setServiceName($task->service->name) }}</strong> {{ $task->link }}
                            </h4>
                        </a>
                        <div class="card-details">
                            <span class="totalPoints" data-id="{{$task->id}}"></span> сум (<span class="point"
                                                                                                    data-id="{{$task->id}}"></span>
                            за задание)
                            {{ \Carbon\Carbon::parse($task->created_at)->format('d.m.Y')}}

                            <button data-id="{{ $task->id }}" data-url="{{ $task->link }}" class="do-action btn btn-primary btn-block">{{ $task->points }} сум</button>
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
            $(document).on('click', '.do-action', function () {
                $id = $(this).attr('data-id');
                $url = $(this).attr('data-url');
                $commentId = $(this).parents('.panel-extra').find('.randComment').data('id');
                $csrf = $('meta[name="csrf-token"]').attr('content');
                var popUp = window.open($url, "thePopUp", "width=600,height=600");
                function someFunctionToCallWhenPopUpCloses() {
                    window.setTimeout(function() {
                        if (popUp.closed) {
                            $.ajax({
                                url: '/tasks/check/'+$id,
                                type: 'GET',
                                data: {_token: $csrf, comment: $commentId},
                                success: function (resp) {
                                    toastr[resp.original.status](resp.original.title, resp.original.message);
                                }
                            });
                        }
                    }, 1);
                }

                var win = window.open($url, "thePopUp", "width=500,height=500");
                var pollTimer = window.setInterval(function() {
                    if (win.closed !== false) {
                        window.clearInterval(pollTimer);
                        someFunctionToCallWhenPopUpCloses();
                    }
                }, 200);
            });
        });
    </script>
@endpush
