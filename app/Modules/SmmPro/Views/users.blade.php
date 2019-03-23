@extends('layouts.app')
@section('title','Пользователи')
@section('content')
    @if($users->isEmpty())
        <h3>Нет пользователей</h3>
    @else
        <div class="table-responsive">
            <table id="usersTable" class="table color-table info-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Логин</th>
                    <th>E-mail</th>
                    <th>Роль</th>
                    <th>Дата регистрации</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->login }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ Smmpro::getRole($user->role_id) }}</td>
                        <td>{{ $user->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <a href="{{ route('task.create') }}" class="btn btn-info btn-lilac">На главную</a>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
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
