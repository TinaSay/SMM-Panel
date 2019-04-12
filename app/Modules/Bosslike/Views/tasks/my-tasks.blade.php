@extends('layouts.app')
@section('title','Мои задания')
@section('content')

    <div class="additional-sidebar"><span class="additional-sidebar-title">Выберите социальную сеть</span>
        <ul class="additional-sidebar-select">
            @foreach($socials as $social)
                <li>
                    <a class="additional-sidebar-select-button {{ ($social->id == $selected_social) ? 'selected' : '' }}">
                        <img src="{{ asset('images/' . $social->icon) }}" alt="{{ $social->name }}">
                        {{ $social->name }}
                    </a>
                    <ul class="additional-sidebar-submenu {{ ($social->id == $selected_social) ? 'active-cat-cover' : '' }}">
                        <li>
                            <a class="{{ ($social->id == $selected_social && $all) ? 'active-cat' : '' }}"
                               href="{{ route('tasks.my', ['social' => $social->id]) }}">
                                <img src="{{ asset('images/' . $social->icon) }}">
                                Все задания
                            </a>
                        </li>
                        @foreach($social->services as $service)
                            <li>
                                <a class="{{ ($service->id == $selected_service) ? 'active-cat' : '' }}"
                                   href="{{ route('tasks.my', ['social' => $social->id, 'service' => $service->id]) }}">
                                    <img src="{{ asset('images/' . $service->icon) }}">
                                    {{ Bosslike::setServiceName($service->name, 'category') }}
                                </a>
                            </li>
                        @endforeach
                        <button class="hide">Скрыть</button>
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>

    @if($tasks->isEmpty())
        <div class="no-orders-container">
            <h3 class="no-orders-heading">На данный момент у Вас ещё нет заданий.</h3>
            <div class="ghost-icon"></div>
        </div>
    @else
        <div class="row justify-content-center">
            @foreach($tasks as $task)
                <div class="col-md-12 card mb-4">
                    <div class="card-body">
                        <div class="card-details my-tasks">
                            <div class="d-flex justify-content-between">
                                @if($task->service->name == 'Like')
                                    <i class="left-icon far fa-heart"></i>
                                @elseif($task->service->name == 'Subscribe')
                                    <i class="left-icon far fa-user"></i>
                                @elseif($task->service->name == 'Share')
                                    <i class="left-icon fas fa-info-circle"></i>
                                @else
                                    <i class="left-icon fas fa-user-tie"></i>
                                @endif
                                <div class="img-holder"
                                     style="background-image: url({{ (!empty($task->picture)) ? $task->picture : asset('images/picstar.png') }})">
                                </div>
                                <span class="soc-badge">
                                    @if($task->service->social->name =='Facebook')
                                        <i class="fab fa-facebook"></i>
                                    @elseif($task->service->social->name =='Instagram')
                                        <i class="fab fa-instagram"></i>
                                    @elseif($task->service->social->name =='Youtube')
                                        <i class="fab fa-youtube"></i>
                                    @else
                                        <i class="fas fa-info-circle"></i>
                                    @endif
                                    </span>
                                <div class="col-sm-5 col-md-4 col-lg-6">
                                    <p>{{ Bosslike::setServiceName($task->service->name) }}
                                        <a href="{{ $task->link }}" target="_blank">
                                            @if($task->service->name=='Subscribe')
                                                @if($task->service->social->name =='Instagram')
                                                    <span> пользователя </span>
                                                @else
                                                    {{Bosslike::setTypeName($task->type) }}
                                                @endif
                                            @elseif($task->service->name=='Comment')
                                                @if($task->service->social->name =='Instagram')
                                                    <span> пост </span>
                                                @else
                                                    {{Bosslike::setTypeName($task->type) }}
                                                @endif
                                            @elseif($task->service->name=='Like')
                                                @if($task->service->social->name =='Instagram')
                                                    <span> пост </span>
                                                @else
                                                    {{Bosslike::setTypeName($task->type) }}
                                                @endif
                                            @else
                                                {{Bosslike::setTypeName($task->type) }}
                                            @endif

                                        </a>&nbsp;
                                        @php($priority = ['sng' => 'СНГ', 'uzbsng' => 'УЗБ + СНГ' ])
                                        {{$task->post_name}}
                                        @if($task->priority == 'sng' || $task->priority == 'uzbsng')
                                            ({{$priority[$task->priority]}})
                                        @else
                                            (УЗБ)
                                        @endif
                                    </p>
                                    @if($tasksDone[$loop->index]<$task->amount)
                                        <p>Запущено {{$task->created_at->format('d.m.y')}}
                                            в {{$task->created_at->format('G.m')}}</p>
                                    @else
                                        <p>
                                            Выполнено {{$task->tasks_done[$tasksDone[$loop->iteration]]->created_at->format('d.m.y')}}
                                            в {{$task->created_at->format('G.m')}}</p>
                                    @endif
                                </div>
                                <div class="col-sm-3 col-md-4 col-lg-4">

                                    {{--remaining and total--}}

                                    <span class="task-stat">
                                        @if($task->priority == 'sng')
                                            <span>
                                                @if($task->bl->count_complete<$task->bl->count)
                                                    <i class="fas fa-play-circle"></i>
                                                @else
                                                    <i class="fas fa-check-circle" style="color:#3490dc"></i>
                                                @endif
                                            </span>
                                            <span class="done">{{$task->bl->count_complete}} </span> из
                                            <span>{{$task->bl->count}}</span>
                                        @elseif($task->priority == 'uzbsng')
                                            <p>
                                                <span>
                                                    @if($tasksDone[$loop->index]<$task->amount)
                                                        <i class="fas fa-play-circle"></i>
                                                    @else
                                                        <i class="fas fa-check-circle" style="color:#3490dc"></i>
                                                    @endif
                                                </span>&nbsp;
                                                <span class="done">{{$tasksDone[$loop->index]}} </span> &nbsp;из&nbsp;
                                                <span>{{$task->amount}}</span>
                                            </p>
                                            <p>
                                                <span>
                                                    @if($task->bl->count_complete<$task->bl->count)
                                                        <i class="fas fa-play-circle"></i>
                                                    @else
                                                        <i class="fas fa-check-circle" style="color:#3490dc"></i>
                                                    @endif
                                                </span>&nbsp;
                                                <span class="done">{{$task->bl->count_complete}} </span> &nbsp;из&nbsp;
                                                <span>{{$task->bl->count}}</span>
                                            </p>
                                        @else
                                            <span>
                                                @if($tasksDone[$loop->index]<$task->amount)
                                                    <i class="fas fa-play-circle"></i>
                                                @else
                                                    <i class="fas fa-check-circle" style="color:#3490dc"></i>
                                                @endif
                                            </span>
                                            <span class="done">{{$tasksDone[$loop->index]}} </span> из
                                            <span class="totalAmount">{{$task->amount}}</span>
                                        @endif

                                    </span>
                                    @if($tasksDone[$loop->index]<$task->amount)
                                        {{--Button for edit--}}
                                        <a href="" class="btn btn-sm btn-outline-primary edit" data-toggle="tooltip"
                                           title="Редактировать" data-id="{{$task->id}}"
                                           onclick="openEditForm({{$task->id}})"><i
                                                class="fas fa-edit"></i></a>
                                        {{--End button for edit--}}
                                    @endif

                                    {{--Button for delete--}}
                                    <form action="{{ route('task.delete',$task->id) }}" method="POST" class="d-inline">
                                        {{--@method('DELETE')
                                        @csrf--}}
                                        {{ csrf_field() }}
                                        {{--{{ method_field('DELETE') }}--}}
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-toggle="tooltip"
                                                title="Удалить"
                                                onclick="return confirm('Удалить безвозвратно?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    {{--End button for delete--}}
                                </div>
                            </div>
                        </div>

                        {{--Form for editing--}}
                        <form class="edit-form-{{$task->id}} d-none">
                            <input type="hidden" value="{{ $task->id }}" class="task_id">

                            <div class="form-group">
                                <label for="">Ссылка</label>
                                <input type="text" disabled="disabled" value="{{ $task->link }}" class="form-control"
                                       name="link">
                            </div>

                            <div class="form-group">
                                <label for="points">Оплата исполнителю для Узбекистана</label>

                                <input id="points" type="number" name="points" value="{{ $task->points }}" min="30"
                                       class="points form-control"
                                       placeholder="кол.баллов" onfocus="this.placeholder = ''"
                                       onblur="this.placeholder = 'кол.баллов'">

                                <span class="invalid-feedback d-none" role="alert">
                                        <strong>Сумма должна быть не меньше 30</strong>
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="amount">Количество выполнений для Узбекистана</label>

                                <input id="amount" type="number" name="amount" min="10" value="{{ $task->amount }}"
                                       class="amount form-control">

                                <span class="invalid-feedback d-none" role="alert">
                                        <strong>Количество должно быть не меньше 10</strong>
                                </span>
                            </div>
                            @if($task->sng_points)
                                <div class="form-group">
                                    <label for="sng_points">Оплата исполнителю для СНГ</label>

                                    <input id="sng_points" type="number" name="sng_points" min="60"
                                           value="{{ $task->sng_points }}"
                                           class="sng_points form-control{{ $errors->has('sng_points') ? ' is-invalid' : '' }}">

                                    @if ($errors->has('sng_points'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('sng_points') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @if($task->sng_amounts)
                                <div class="form-group">
                                    <label for="sng_amounts">Количество выполнений для СНГ</label>

                                    <input id="sng_amounts" type="number" name="sng_amounts" min="10"
                                           value="{{ $task->sng_amounts }}"
                                           class="sng_amounts form-control{{ $errors->has('sng_amounts') ? ' is-invalid' : '' }}">

                                    @if ($errors->has('sng_amounts'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('sng_amounts') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            @endif

                            @if($task->service->name=='Comment')
                                <p>Комментарии, которые будут добавлять пользователи. Если вам нужны любые комментарии -
                                    просто оставьте эти поля пустыми.
                                </p>
                                @foreach($task->comments as $comment)

                                    <div class="form-group">
                                        <div class="comment-block">
                                            <div class="comment-input">

                                                <div class="comment-input-body">

                                                    <h5>Комментарий <span
                                                            class="comment-number">{{$loop->iteration}}</span>
                                                        <a href="#" class="remove-comment d-none">
                                                            <i class="fas fa-trash"></i>
                                                        </a></h5>
                                                    <textarea name="comment_text[]"
                                                              class="form-control comment_text" rows="2"
                                                              maxlength="150">{{$comment->text}}</textarea>
                                                    <input type="hidden" value="" class="counter" name="counter">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach

                            @endif


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lilac update">
                                    Сохранить
                                </button>
                                <button class="btn btn-primary btn-gray btn-cancel">
                                    Отмена
                                </button>
                                <span class="showPoints">
                                  {{--<i class="far icon-wallet"></i>--}}
                                    {{--<span class="totalPoints">0 </span> <span> сумов</span>--}}
                                </span>
                            </div>
                        </form>
                        {{--End editing form--}}
                    </div>

                </div>
            @endforeach
        </div>
        <div class="">
            {{ $links }}
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

            $('.edit').click(function (e) {
                e.preventDefault();
                var _form = $('.edit-form-' + $(this).attr('data-id'));
                var _points = _form.find('#points').val();

                _form.find('#points').on('change keyup paste', function () {
                    console.log('true');
                });

            });

            $('.btn-cancel').on('click', function (e) {
                e.preventDefault();
                $(this).closest('form').addClass('d-none');
            });

            $('.update').on('click', function (e) {
                e.preventDefault();

                var _form = $(this).closest('form');
                var _task = _form.find('.task_id').val();
                var _points = _form.find('.points').val();
                var _amount = _form.find('.amount').val();
                var sng_points = _form.find('.sng_points').val();
                var sng_amounts = _form.find('.sng_amounts').val();
                var _comments = _form.find('[name="comment_text[]"]');
                var _commentsArray = [];

                $.each(_comments, function (k, v) {
                    _commentsArray.push($(this).val());
                });

                $.ajax({
                    url: '/task/update/' + _task,
                    method: 'PUT',
                    data: {
                        _token: '{!! csrf_token() !!}',
                        'points': _points,
                        'amount': _amount,
                        'sng_points': sng_points,
                        'sng_amounts': sng_amounts,
                        'comment_text': _commentsArray,
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        _form.toggleClass('d-none');
                        total(_points, _amount, _task);

                        if (response.status == 1) {
                            window.toastr.success(response.message);
                            getBalance();
                            _form.closest('.card-body').find('.totalAmount').text(_amount);
                            // $('#userTopBalance').load(location.href + ' #userTopBalance' + ">*", "");
                        } else {
                            window.toastr.error(response.message);
                        }
                    },
                    error: function () {
                        _form.find('input[type="number"]').addClass('is-invalid');
                        _form.find('.invalid-feedback').removeClass('d-none')
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
