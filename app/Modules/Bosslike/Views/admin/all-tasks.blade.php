@extends('layouts.app')

@section('title','Список заданий')

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
                               href="{{ route('tasks.list', ['social' => $social->id]) }}">
                                <img src="{{ asset('images/' . $social->icon) }}">
                                Все задания
                            </a>
                        </li>
                        @foreach($social->services as $service)
                            <li>
                                <a class="{{ ($service->id == $selected_service) ? 'active-cat' : '' }}"
                                   href="{{ route('tasks.list', ['social' => $social->id, 'service' => $service->id]) }}">
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

    @if ($tasks->isEmpty())
        <div class="no-orders-container">
            <h3 class="no-orders-heading">На данный момент заданий нет.</h3>
            <div class="ghost-icon"></div>
        </div>
    @else

        <div class="col-12 col-sm-12 col-md-12 table-responsive">

            <table id="tasksTable" class="table table-hover">
                <thead>
                <tr class="table-info">
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Ссылка</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tasks as $task)

                    <tr>
                        <td>{{ $task->created_at}}</td>
                        <td>{{ $task->user->login }} ({{ $task->user->billing_id }})</td>
                        <td>{{ $task->link }}</td>
                        {{--Button for delete--}}
                        <td>
                            <form action="{{ route('admin.task.delete',$task->id) }}" method="POST" class="d-inline">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        data-toggle="tooltip"
                                        title="Удалить"
                                        onclick="return confirm('Удалить безвозвратно?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        {{--End button for delete--}}
                    </tr>

                @endforeach
                </tbody>
            </table>
        </div>

    @endif

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#tasksTable').DataTable({
                "language": {
                    "search": 'Поиск',
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Страница _PAGE_ из _PAGES_",
                    "infoFiltered": " - найдено из _MAX_ записей",
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    },
                }
            });
        });
    </script>
@endpush
