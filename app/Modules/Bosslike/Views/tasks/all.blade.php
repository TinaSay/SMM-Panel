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
                <div class="card-body" data-id="{{ $task->id }}" data-url="{{ $task->link }}">
                    <input type="hidden" value="{{ $task->id }}" class="task_id">
                    <a href="{{ $task->link }}" target="_blank">
                        <h4><strong>{{ Bosslike::setServiceName($task->service->name) }}</strong> {{ $task->link }}
                        </h4>
                    </a>
                    <div class="card-details">
                        <span class="totalPoints" data-id="{{$task->id}}"></span> баллов (<span class="point"
                                                                                                data-id="{{$task->id}}"></span>
                        за задание)
                        {{$task->created_at}}
                    </div>

                    {{--Form for editing--}}
                    <form class="edit-form-{{$task->id}} d-none" onsubmit="updateData({{$task->id}})">
                        <div class="form-group">
                            <label for="">Ссылка</label>
                            <input type="text" disabled="disabled" value="{{ $task->link }}" class="form-control"
                                   name="link">
                        </div>

                        <div class="form-group">
                            <label for="points">Оплата исполнителю</label>

                            <input id="points" type="number" name="points" value="{{ $task->points }}"
                                   class="points{{$task->id}} form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
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

                            <input id="amount" type="number" name="amount" value="{{ $task->amount }}"
                                   class="amount{{$task->id}} form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}">

                            @if ($errors->has('amount'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lilac">
                                Сохранить
                            </button>
                            <a href="{{ route('tasks.my') }}" class="btn btn-primary btn-gray">
                                Отмена
                            </a>
                        </div>
                    </form>
                    {{--End editing form--}}
                </div>

            </div>
        @endforeach
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $(document).on('click', '.card-body', function () {
                $id = $(this).attr('data-id');
                $url = $(this).attr('data-url');
                $csrf = $('meta[name="csrf-token"]').attr('content');
                var popUp = window.open($url, "thePopUp", "width=600,height=600");
                function someFunctionToCallWhenPopUpCloses() {
                    window.setTimeout(function() {
                        if (popUp.closed) {
                            $.ajax({
                                url: '/tasks/check/'+$id,
                                type: 'GET',
                                data: {_token: $csrf},
                                success: function (resp) {
                                    toastr[resp.original.status](resp.original.title, resp.original.message);
                                }
                            });
                        }
                    }, 1);
                }

                var win = window.open($url, "thePopUp", "width=500,height=500");
                var pollTimer = window.setInterval(function() {
                    if (win.closed !== false) { // !== is required for compatibility with Opera
                        window.clearInterval(pollTimer);
                        someFunctionToCallWhenPopUpCloses();
                    }
                }, 200);
            });
        });
    </script>
@endpush
