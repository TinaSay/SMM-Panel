@extends('layouts.app')
@section('title','Заказы')
@section('content')
    @if($orders->isEmpty())
        <h3>Нет заказов</h3>
    @else
        @if(session()->has('success'))
            <input type="hidden" id="success-session" value="{{ session('success') }}">
        @elseif((session()->has('fail')))
            <input type="hidden" id="fail-session" value="{{ session('fail') }}">
        @endif
        <div class="table-responsive">
            <table id="ordersTable" class="table color-table info-table">
                <thead>
                <tr>
                    <th style="background-image:none;">#</th>
                    <th style="background-image:none;">Сервис</th>
                    <th style="background-image:none;">Пользователь</th>
                    <th style="background-image:none;">Цена</th>
                    <th style="background-image:none;">Количество</th>
                    <th style="background-image:none;">Ссылка</th>
                    <th style="background-image:none;">Статус</th>
                    <th style="background-image:none;">Дата заказа</th>
                    <th style="background-image:none;">Действие</th>
                </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td height="100" style="overflow: hidden; display:block;">{{ $order->service->name }}</td>
                        <td>{{ $order->user->login }}</td>
                        <td>{{ $order->charge }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td width="200" style="word-break: break-all; display: block;">{{ $order->link }}</td>
                        <td>{{ $order->status }}</td>
                        <td width="150" style="display:block;">{{ $order->created_at }}</td>
                        <td width="150">
                            <a href="{{ route('order.edit', $order->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               data-toggle="tooltip" data-original-title="Редактировать">
                                <i class="fa fa-edit"></i></a>
                            <form action="{{ route('order.destroy', $order->id) }}" method="POST"
                                  style="display:inline-block">
                                @method('DELETE')
                                @csrf
                                <button class="btn btn-sm btn-outline-danger" data-toggle="tooltip"
                                        data-original-title="Удалить" onclick="return confirm('Удалить безвозвратно?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <div class="m-t-20">
        <a href="{{ route('catalog') }}" class="btn btn-info btn-lilac">На главную</a>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#ordersTable').DataTable({
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
        var _successSession = $('#success-session').val();
        var _failSession = $('#fail-session').val();
        if (_successSession != null) {
            window.toastr.success(_successSession);
        } else if (_failSession != null) {
            window.toastr.error(_failSession);
        }
    </script>
@endpush
