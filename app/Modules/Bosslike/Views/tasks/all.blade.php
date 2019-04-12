@extends('layouts.app')
@section('title','Все задания')
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
                               href="{{ route('tasks.all', ['social' => $social->id]) }}">
                                <img src="{{ asset('images/' . $social->icon) }}">
                                Все задания
                            </a>
                        </li>
                        @foreach($social->services as $service)
                            <li>
                                <a class="{{ ($service->id == $selected_service) ? 'active-cat' : '' }}"
                                   href="{{ route('tasks.all', ['social' => $social->id, 'service' => $service->id]) }}">
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
            <h3 class="no-orders-heading">На данный момент нет подходящих заданий.</h3>
            <div class="ghost-icon"></div>
        </div>
    @else

        <div class="row justify-content-center task-list">
            <div class="alert-wrapper">
                <div class="alert alert-info alert-dismissable text-center">
                    <ul>
                        <li>
                            Аккаунты должны быть открытыми и иметь аватарку, если вы заметили закрытый профиль, пожалуйста обратитесь к нам.
                        </li>
                        <li>
                            После подписки на профиль, исполнитель не должен отписываться от него в течении 30 дней.
                        </li>
                        <li>
                            Если вы производили какие-то действия вне сервиса Picstar, то советуем вам перевыполнить их для подтверждения выполнения.
                        </li>
                    </ul>
                </div>
            </div>
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
                                        @elseif($task->service->social->name =='Youtube')
                                            <i class="fab fa-youtube"></i>
                                        @else
                                            <i class="fas fa-info-circle"></i>
                                        @endif
                                    </span>
                                    <div class="col-sm-5 col-md-4 col-lg-6">
                                        <p>{{ Bosslike::setServiceName($task->service->name) }}
                                            <a data-toggle="collapse" href="#oneTask_{{ $task->id }}" role="button"
                                               aria-expanded="false"
                                               aria-controls="oneTask_{{ $task->id }}" class="withComments">
                                                @if($task->service->social->name =='Instagram')
                                                    <span> пост </span>
                                                @else
                                                    {{ Bosslike::setTypeName($task->type) }}
                                                @endif
                                            </a></p>
                                    </div>

                                    <div class="col-sm-3 col-md-4 col-lg-3">
                                        <button type="button" data-id="{{ $task->id }}"
                                                data-toggle="collapse" data-check="false"
                                                data-target="#oneTask_{{ $task->id }}"
                                                aria-expanded="true"
                                                aria-controls="oneTask_{{ $task->id }}"
                                                class="btn btn-lilac btn-lilac-sm make_action_but withComments link_but">
                                            <i
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

                                    <div class="dropleft">
                                        <button class="btn btn-secondary" type="button" id="dropdownMenuButton_{{ $task->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_{{ $task->id }}">
                                            <a href="" class="dropdown-item" data-toggle="tooltip" title="Скрыть" onclick="hide({{ $task->id }})"><i
                                                        class="fas fa-times close-icon"></i> Скрыть</a>
                                            <a class="dropdown-item task-callback-but" data-id="{{ $task->id }}" data-toggle="modal" data-target="#complainModal" href="#"><i class="fas fa-exclamation"></i> Пожаловаться</a>
                                        </div>
                                    </div>
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
                                                    <div readonly="readonly"
                                                         id="taskComment_{{ $randTask->id }}"
                                                         class="form-control randComment"
                                                         data-id="{{ $randTask->id }}">{!! $randTask->text !!}</div>
                                                    <button class="copy_to_clipboard">Скопировать
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
                                        @elseif($task->service->social->name =='Youtube')
                                            <i class="fab fa-youtube"></i>
                                        @else
                                            <i class="fas fa-info-circle"></i>
                                        @endif
                                        </span>
                                    <div class="col-sm-5 col-md-4 col-lg-6">
                                        <p>{{ Bosslike::setServiceName($task->service->name) }}
                                            <a data-id="{{ $task->id }}"
                                               href="#oneTask_{{ $task->id }}"
                                               aria-controls="oneTask_{{ $task->id }}"
                                               class="do-action link_but"
                                               data-check="false">

                                                @if($task->service->name=='Subscribe')
                                                    @if($task->service->social->name =='Youtube')
                                                        <span> канал </span>
                                                    @elseif($task->service->social->name =='Telegram')
                                                        <span> канал </span>
                                                    @elseif($task->service->social->name =='Instagram')
                                                        <span> пользователя</span>
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
                                                {{$task->post_name}}
                                            </a></p>
                                    </div>
                                    <div class="col-sm-3 col-md-4 col-lg-3">
                                        @if($task->service->name == 'Watch')
                                            <button data-id="{{ $task->id }}"
                                                    data-check="false"
                                                    data-method="watch"
                                                    class="do-action btn btn-lilac btn-lilac-sm make_action_but link_but">
                                                <i
                                                    class="far fa-star" aria-hidden="true"></i> {{ $task->points }} сум
                                            </button>
                                        @else
                                            <button data-id="{{ $task->id }}"
                                                    data-check="false"
                                                    class="do-action btn btn-lilac btn-lilac-sm make_action_but link_but">
                                                <i
                                                    class="far fa-star" aria-hidden="true"></i> {{ $task->points }} сум
                                            </button>
                                        @endif
                                        <span class="while_checking" style="display: none"><i
                                                class="fa fa-spinner fa-spin"
                                                aria-hidden="true"></i> Проверка</span>
                                        <span class="needs_checking" style="display: none"><i class="fa fa-eye"
                                                                                              aria-hidden="true"></i> Проверить</span>
                                        <span class="is_ready" style="display: none"><i class="fa fa-check-circle"
                                                                                        aria-hidden="true"
                                                                                        onload="getBalance()"></i> Выполнено</span>
                                        <span class="is_initial" style="display: none"><i class="far fa-star"
                                                                                          aria-hidden="true"></i> {{ $task->points }} сум</span>
                                    </div>

                                    <div class="dropleft">
                                        <button class="btn btn-secondary" type="button" id="dropdownMenuButton_{{ $task->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_{{ $task->id }}">
                                            <a href="" class="dropdown-item" data-toggle="tooltip" title="Скрыть" onclick="hide({{ $task->id }})"><i
                                                        class="fas fa-times close-icon"></i> Скрыть</a>
                                            <a class="dropdown-item task-callback-but" data-id="{{ $task->id }}" data-toggle="modal" data-target="#complainModal" href="#"><i class="fas fa-exclamation"></i> Пожаловаться</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            <button class="refresh-task-list"><i class="fas fa-sync-alt"></i>Ещё задания</button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="complainModal" tabindex="-1" role="dialog" aria-labelledby="complainModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="complainModalTitle">Пожаловаться на задание</h5>
                        <p>Пожалуйста, сообщите причину, по которой задание должно быть заблокировано</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{--{{ route('complain.store') }}--}}
                        <form id="complainForm" action="/complain/store?debug=00017" method="POST">
                            <div class="complain-reason-radios">
                                <div class="form-group custom-radio">
                                    <label>
                                        <input type="radio" name="type" value="Задание недоступно" />
                                        <span>Задание недоступно</span></label>
                                </div>
                                <div class="form-group custom-radio">
                                    <label>
                                        <input type="radio" name="type" value="Мошенничество" />
                                        <span>Мошенничество</span></label>
                                </div>
                                <div class="form-group custom-radio">
                                    <label>
                                        <input type="radio" name="type" value="Рассылка спама" />
                                        <span>Рассылка спама</span></label>
                                </div>
                                <div class="form-group custom-radio">
                                    <label>
                                        <input type="radio" name="type" value="Оскорбительное поведение" />
                                        <span>Оскорбительное поведение</span></label>
                                </div>
                                <div class="form-group form-text">
                                    <label>Комментарий
                                    <textarea placeholder="Введите комментарий..." name="comment"></textarea>
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                <button type="submit" class="btn btn-primary">Отправить</button>
                            </div>
                            <input type="hidden" name="task_id" value="" id="complainTaskId" />
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
