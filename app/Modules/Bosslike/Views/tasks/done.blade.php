@extends('layouts.app')
@section('title','Мои сделки')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif

    @if($tasks->isEmpty())
        <h3>Нет выполненных заданий</h3>
    @else
        <div class="row justify-content-center">
            @foreach($tasks as $task)

                <div class="col-md-12 card mb-4">
                    <div class="card-body">
                        <div class="card-details my-tasks">
                            <div class="d-flex justify-content-between">

                                <div class="img-holder"
                                     style="background-image: url({{ (!empty($task->picture)) ? $task->picture : asset('images/picstar.png') }})">
                                </div>

                                <div class="col-sm-5 col-md-4 col-lg-6">
                                    <p>{{ Bosslike::setServiceName($task->service->name) }}
                                        <a href="{{ $task->link }}" target="_blank">
                                            @if($task->service->name=='Subscribe')
                                                <span> на </span>  {{Bosslike::setTypeName($task->type) }}
                                            @else
                                                {{Bosslike::setTypeName($task->type) }}
                                            @endif
                                            {{$task->post_name}}
                                        </a></p>
                                    @if($tasksDone[$loop->index]<$task->amount)
                                        <p>Запущено {{$task->created_at->format('d.m.y')}}
                                            в {{$task->created_at->format('h.m')}}</p>
                                    @else
                                        <p>
                                            Выполнено {{$task->tasks_done[$tasksDone[$loop->iteration]]->created_at->format('d.m.y')}}
                                            в {{$task->created_at->format('h.m')}}</p>
                                    @endif
                                </div>
                                <div class="col-sm-3 col-md-4 col-lg-3">

                                    remaining and total

                                    <span class="task-stat">
                                        <span>
                                            @if($tasksDone[$loop->index]<$task->amount)
                                                <i class="fas fa-play-circle"></i>
                                            @else
                                                <i class="fas fa-check-circle" style="color:#3490dc"></i>
                                            @endif
                                            </span>
                                        <span class="done">{{$tasksDone[$loop->index]}} </span> из
                                        <span>{{$task->amount}}</span>
                                    </span>

                                    Button for edit
                                    <a href="" class="btn btn-sm btn-outline-primary edit" data-toggle="tooltip"
                                       title="Редактировать" data-id="{{$task->id}}"
                                       onclick="openEditForm({{$task->id}})"><i
                                                class="fas fa-edit"></i></a>
                                    End button for edit

                                    Button for delete
                                    <form action="{{ route('task.delete',$task->id) }}" method="POST" class="d-inline">
                                        @method('DELETE')
                                        @csrf
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-sm btn-outline-danger" data-toggle="tooltip"
                                                title="Удалить"
                                                onclick="return confirm('Удалить безвозвратно?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    End button for delete
                                </div>
                            </div>
                        </div>

                        <div class="task-slaves" data-id="{{$task->id}}">


                        </div>
                        Form for editing
                        <form class="edit-form-{{$task->id}} d-none">
                            <input type="hidden" value="{{ $task->id }}" class="task_id">

                            <div class="form-group">
                                <label for="">Ссылка</label>
                                <input type="text" disabled="disabled" value="{{ $task->link }}" class="form-control"
                                       name="link">
                            </div>

                            <div class="form-group">
                                <label for="points">Оплата исполнителю</label>

                                <input id="points" type="number" name="points" value="{{ $task->points }}"
                                       class="points form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
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
                                       class="amount form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}">

                                @if ($errors->has('amount'))
                                    <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lilac update">
                                    Сохранить
                                </button>
                                <a href="{{ route('tasks.my') }}" class="btn btn-primary btn-gray">
                                    Отмена
                                </a>
                            </div>
                        </form>
                        End editing form
                    </div>

                </div>
            @endforeach
        </div>
    @endif
@endsection
@push('functions')
    <script>
        function openEditForm(id) {
            $('.edit-form-' + id).toggleClass('d-none');
        }

    </script>
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {

            var session = $('#success-session').val();
            if (session != null) {
                window.toastr.success(session);
            }

            $('.edit').on('click', function (e) {
                e.preventDefault();
            });

            $('.update').on('click', function (e) {
                e.preventDefault();

                var _form = $(this).closest('form');
                var _task = _form.find('.task_id').val();
                var _points = _form.find('.points').val();
                var _amount = _form.find('.amount').val();

                $.ajax({
                    url: '/task/update/' + _task,
                    method: 'PUT',
                    data: {
                        _token: '{!! csrf_token() !!}',
                        'points': _points,
                        'amount': _amount,
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        _form.toggleClass('d-none');
                        total(_points, _amount, _task);
                        point(_points);

                        if (response.status == 1) {
                            window.toastr.success(response.message);
                        } else {
                            window.toastr.error(response.message);
                        }
                    },
                    error: function () {
                        window.toastr.error('Ошибка');
                    }
                });
                e.preventDefault();
            });

            function total(points, amount, id) {
                var total = points * amount;
                $('.totalPoints-' + id).text(total);
                $('.point' + id).text(points);

            }
        });
    </script>
@endpush
