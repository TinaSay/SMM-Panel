@extends('layouts.app')
@section('title','Редактировать заказ ' . $order->name . ' пользователя '. $order->user_id)
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('order.update', $order->id) }}">
                @csrf

                <div class="form-group">
                    <label for="service_id">Сервис</label>

                    <select class="selectpicker btn-group bootstrap-select dropup form-control"
                            data-style="form-control"
                            name="service_id"
                            id="service_id">
                        @foreach($services as $service)
                            <option
                                value="{{ $service->id }}" {{ $service->id == $order->service_id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Количество</label>

                    <input id="quantity" type="number"
                           class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}"
                           name="quantity" value="{{ $order->quantity}}">

                    @if ($errors->has('quantity'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="link">Ссылка</label>

                    <input id="link" type="text"
                           class="form-control{{ $errors->has('link') ? ' is-invalid' : '' }}"
                           name="link" value="{{ $order->link}}">

                    @if ($errors->has('link'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('link') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="charge">Цена</label>

                    <input id="charge" type="text"
                           class="form-control{{ $errors->has('charge') ? ' is-invalid' : '' }}"
                           name="charge" value="{{ $order->charge}}">

                    @if ($errors->has('charge'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('charge') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="order_api_id">Order Api Id</label>

                    <input id="order_api_id" type="text"
                           class="form-control{{ $errors->has('order_api_id') ? ' is-invalid' : '' }}"
                           name="order_api_id" value="{{ $order->order_api_id}}">

                    @if ($errors->has('order_api_id'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('order_api_id') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="status">Статус</label>

                    <input id="status" type="text"
                           class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}"
                           name="status" value="{{ $order->status}}">

                    @if ($errors->has('status'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="type">Тип</label>

                    <input id="type" type="text"
                           class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}"
                           name="type" value="{{ $order->type}}">

                    @if ($errors->has('type'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="start_count">Было</label>

                    <input id="start_count" type="text"
                           class="form-control{{ $errors->has('start_count') ? ' is-invalid' : '' }}"
                           name="start_count" value="{{ $order->start_count}}">

                    @if ($errors->has('start_count'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('start_count') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="remains">Остаток</label>

                    <input id="remains" type="text"
                           class="form-control{{ $errors->has('remains') ? ' is-invalid' : '' }}"
                           name="remains" value="{{ $order->remains}}">

                    @if ($errors->has('remains'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('remains') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lilac">
                        Обновить
                    </button>
                    <a class="btn btn-info btn-gray" href="{{ route('orders.index') }}">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
