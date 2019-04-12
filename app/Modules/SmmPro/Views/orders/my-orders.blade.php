@extends('layouts.app')
@section('title','Мои заказы')
@section('content')
@if($orders->isEmpty())
<div class="no-orders-container">
    <h3 class="no-orders-heading">На данный момент у Вас ещё нет заказов.</h3>
    <div class="ghost-icon"></div>
</div>
@else
@if(session()->has('success'))
<div class="alert alert-info alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">×</span>
        <span class="sr-only">Close</span>
    </button>
    <ul class="list-unstyled">
        <li>{{ session('success') }}</li>
    </ul>
</div>
@endif

<div class="table-responsive">
    <table class="table color-table info-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Сервис</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Ссылка</th>
                <th>Статус</th>
                <th>Дата заказа</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            {{\App\Modules\SmmPro\Models\Order::checkOrderStatus($order)}}
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->service->name }}</td>
                <td>{{ $order->charge }}</td>
                <td>{{ $order->quantity }}</td>
                <td>{{ $order->link }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="container">{{ $orders->render()}} </div>
</div>
@endif
<div class="m-t-20">
    <a href="{{ route('catalog') }}" class="btn btn-info btn-lilac">На главную</a>
</div>
@endsection