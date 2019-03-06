@extends('layouts.app')
@section('title','Мои задания')
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
                    <a href="" class="btn btn-sm btn-muted edit" data-id="{{$task->id}}" onclick="openEditForm({{$task->id}})"><i
                            class="fas fa-edit"></i>Редактировать</a>

                    <form action="{{ route('task.delete',$task->id) }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-sm btn-outline-danger" data-toggle="tooltip"
                                data-original-title="Удалить" onclick="return confirm('Удалить безвозвратно?')">
                            <i class="fa fa-trash">Удалить</i>
                        </button>
                    </form>

                    <form class="d-none edit-form" data-id="{{$task->id}}">
                        <div class="form-group">
                            <label for="">Ссылка</label>
                            <input type="text" disabled="disabled" value="{{ $task->link }}" class="form-control"
                                   name="link">
                        </div>

                        <div class="form-group">
                            <label for="points">Оплата исполнителю</label>

                            <input id="points" type="number" name="points" value="{{ $task->points }}"
                                   class="form-control{{ $errors->has('points') ? ' is-invalid' : '' }}"
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
                                   class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}">

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
                </div>

            </div>
        @endforeach
    @endif
@endsection
@push('functions')
    <script>
        function openEditForm(id) {
            console.log($('.edit-form[data-id='+id+']'));
        }
    </script>
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {

            var _form = $('.edit-form');


            $('.edit').on('click', function (e) {
                e.preventDefault();
                _form.toggleClass('d-none');
            });

            _form.submit(function (e) {

                $.ajax({
                    url: '/task/update/' + $('.task_id').val(),
                    method: 'PUT',
                    data: {
                        _token: '{!! csrf_token() !!}',
                        'points': $('input[name=points]').val(),
                        'amount': $('input[name=amount]').val()
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        _form.toggleClass('d-none');
                        $('.totalPoints').text($('input[name=points]').val() * $('input[name=amount]').val());
                        point($('input[name=points]').val());

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

            function total(points, amount) {
                var total = points * amount;
                $('.totalPoints').text(total);
            }

            function point(point) {
                $('.point').text(point);
            }

        });
    </script>
@endpush
