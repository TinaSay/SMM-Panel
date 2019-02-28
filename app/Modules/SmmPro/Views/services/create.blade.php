@extends('layouts.app')
@section('title','Новый сервис')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('service.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Название</label>

                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name" value="{{ old('name') }}" autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="description">Описание</label>

                    <textarea name="description" id="description" cols="45"
                              rows="8">{{ old('description') }}</textarea>
                    @if ($errors->has('description'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="root_category">Категория</label>

                    <div class="form-group">
                        <select class="selectpicker btn-group bootstrap-select dropup form-control"
                                data-style="form-control"
                                name="root_category"
                                id="root_category">
                            <option disabled selected value> -- выберите корневую категорию --</option>

                            @foreach($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}">
                                    {{ $parentCategory->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="form-group">

                        <div class="form-group">
                            <select class="selectpicker btn-group bootstrap-select dropup form-control"
                                    data-style="form-control"
                                    name="subcategory"
                                    id="category_id">
                                <option disabled selected value> -- выберите категорию услуги --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="quantity">Количество</label>

                    <input id="quantity" type="number"
                           class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}"
                           name="quantity" value="{{ old('quantity') }}">

                    @if ($errors->has('quantity'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="form-group form-group-default">
                            <div id="service-api-input">
                                <button type="button" onClick="addAPI();" class="pull-left btn btn-info">Добавить API
                                </button>
                            </div>
                        </div>
                        @if ($errors->has('service_api'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('service_api') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="form-group form-group-default">
                            <div id="service-order-api-input">
                                <button type="button" onClick="addOrderAPI();" class="pull-left btn btn-info">Добавить
                                    чек-статус API
                                </button>
                            </div>
                        </div>
                        @if ($errors->has('service_order_api'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('service_order_api') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="type">Тип</label>

                    <select class="selectpicker btn-group bootstrap-select dropup form-control"
                            data-style="form-control"
                            name="type"
                            id="type">
                        <option value="1">{{ \App\Modules\SmmPro\Models\Service::TYPE_DEFAULT }}</option>
                    </select>

                    @if ($errors->has('type'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="price">Цена</label>

                    <input id="price" type="text"
                           class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}"
                           name="price" value="{{ old('price') }}">

                    @if ($errors->has('price'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="reseller_price">Цена реселлеру</label>

                    <input id="reseller_price" type="text"
                           class="form-control{{ $errors->has('reseller_price') ? ' is-invalid' : '' }}"
                           name="reseller_price" value="{{ old('reseller_price') }}">

                    @if ($errors->has('reseller_price'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('reseller_price') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="active">Активна</label>

                    <select class="selectpicker btn-group bootstrap-select dropup form-control"
                            data-style="form-control"
                            name="active"
                            id="active">
                        <option value="1">
                            Да
                        </option>
                        <option value="0">
                            Нет
                        </option>
                    </select>
                    @if ($errors->has('active'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('active') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lilac">
                            Создать
                        </button>
                        <a class="btn btn-info btn-gray" href="{{ route('services.index') }}">
                            Отмена
                        </a>
                    </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function addAPI() {
            $('#service-api-input').html('<label>Service API</label>' +
                '<input type="text" value="http://reseller.com/api/v2/?key=your_key&action=add&service=service_id&quantity=[QUANTITY]&link=[LINK]" ' +
                'id="ServiceAPI" name="service_api" class="form-control">');
        }

        function addOrderAPI() {
            $('#service-order-api-input').html('<label>Service Order API - Checking order status</label>' +
                '<input type="text" value="http://reseller.com/api/v2?key=your_key&action=status&order=[OrderID]" ' +
                'id="ServiceOrderAPI" name="service_order_api" class="form-control">');
        }

        $(function () {
            $('#root_category').on('change', function (e) {
                var _root = $(this).val();
                var _categorySelect = $('#category_id');

                $('option:not(:disabled)', _categorySelect).remove();

                $.post('/ajax/get-descendants', {
                    root: _root,
                    _token: '{!! csrf_token() !!}'
                }, function (response) {
                    $.each(response.categories, function (i, k) {
                        _categorySelect.append('<option value="' + k.id + '">' + ("&nbsp;&nbsp;&nbsp;".repeat(k.depth - 1)) + k.name + '</option>');
                    });
                }).fail(function (error) {
                    console.log(error);
                });
            });
        });
    </script>
@endpush

