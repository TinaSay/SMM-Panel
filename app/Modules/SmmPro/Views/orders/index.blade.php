@extends('layouts.app')
@section('title','Заказы')
@section('content')
    @if($orders->isEmpty())
        <div class="no-orders-container">
            <h3 class="no-orders-heading">На данный момент у Вас ещё нет заказов.</h3>
            <div class="ghost-icon"></div>
        </div>


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
                        <td width="150">{{ $order->created_at }}</td>
                        <td width="150">
                            @if($order->status=='cancelled')
                                <a class="btn btn-sm disabled">Отменен</a>
                            @else
                                <a href="{{ route('order.cancel', $order->id) }}"
                                   class="btn btn-sm btn-outline-primary cancel">
                                    Отменить</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <div class="m-t-20">
        <a href="{{ route('catalog') }}" class="btn btn-info btn-lilac back-to-home">На главную</a>
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

            $('.cancel').on('click', function (e) {
                $(this).text('Отменен').removeClass('btn-outline-primary').addClass('disabled');

            });
        });
    </script>
@endpush
