@extends('layouts.app')
@section('title','Оформление заказа')
@section('content')
    {!! Form::open(['route' => 'post.checkout']) !!}
    <div class="row mb-4">
        <div class="col-12">
            <h4>Содержимое корзины</h4>
        </div>

        <div class="col-12">
            <div class="separator"></div>
        </div>
    </div>

    @foreach (Cart::getContents() as $item)
        <div class="row">
            <div class="col-6">
                {{ $item['name'] }} x <strong>{{ $item['qty'] }}</strong>
            </div>
            <div class="col-3 text-right">
                <strong>{{ number_format($item['price'] * $item['qty'], 0, ',', ' ') }} сум</strong>
            </div>
        </div>
    @endforeach

    <div class="row">
        <div class="col-12">
            <div class="separator"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-3">
            <strong>Итого:</strong>
        </div>
        <div class="col-6 text-right">
            <strong>{{ number_format(Cart::getTotal(), 0, ',', ' ') }} сум</strong>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <button class="btn btn-success" type="submit">
                <i class="fas fa-cash-register"></i>
                Оформить
            </button>
            <a href="/catalog" class="btn btn-primary">
                <i class="fas fa-undo-alt"></i>
                Продолжить
            </a>
            <a href="/empty-cart" class="btn btn-danger" type="button">
                <i class="far fa-trash"></i>
                Очистить
            </a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
