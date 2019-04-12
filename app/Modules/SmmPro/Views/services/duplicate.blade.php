@extends('layouts.app')
@section('title','Редактировать сервис')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('service.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Название</label>

                    <input id="name" type="text"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name" value="{{ $service->name }}" autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="description">Описание</label>

                    <textarea name="description" id="description" cols="45"
                              rows="8"
                              class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ $service->description }}</textarea>
                    @if ($errors->has('description'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="root_category">Категория</label>

                    <select
                        class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('root_category') ? ' is-invalid' : '' }}"
                        data-style="form-control"
                        name="root_category"
                        id="root_category">
                        <option disabled selected value> -- выберите корневую категорию --</option>

                        @foreach($parentCategories as $parentCategory)
                            <option
                                value="{{ $parentCategory->id }}"{!! $parentCategory->id == $rootCategory->id ? ' selected' :'' !!}>
                                {{ $parentCategory->name }}
                            </option>
                        @endforeach

                    </select>
                    @if ($errors->has('root_category'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('root_category') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <select class="selectpicker btn-group bootstrap-select dropup form-control"
                            data-style="form-control"
                            name="category_id"
                            id="category_id">
                        <option disabled selected value> -- выберите категорию услуги --</option>
                        @if ($categories->isNotEmpty())
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}"{!! $c->id == $service->category_id ? ' selected' : '' !!}>{{ $c->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <fieldset id="multi-input">
                        <legend style="font-size: 14px;"><b>Добавьте количество и цену</b></legend>
                        @if ($quantity->isNotEmpty())
                            @foreach ($quantity as $key => $value)
                                <div class="field-wrapper row mt-1" id="field-{{ $key }}" data-index="0">
                                    <div class="col-md-5">
                                        <input type="text" name="quantities[{{ $key }}]" value="{{ $value->quantity }}" class="form-control" placeholder="Количество">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="prices[{{ $key }}]" value="{{ $value->price }}" class="form-control" placeholder="Цена">
                                    </div>
                                    <div class="col-md-2">
                                        @if (!$loop->first)
                                            <button class="btn btn-danger btn-remove" type="button"><i class="fa fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="field-wrapper row" id="field-0" data-index="0">
                                <div class="col-md-5">
                                    <input type="text" name="quantities[0]" class="form-control" placeholder="Количество">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="prices[0]" class="form-control" placeholder="Цена">
                                </div>
                                <div class="col-md-2">

                                </div>
                            </div>
                        @endif
                    </fieldset>

                    <div class="multi-input-controls mt-2">
                        <button id="add" class="btn btn-primary" type="button">
                            <i class="fa fa-plus"></i>
                            Добавить поля
                        </button>
                    </div>

                    @if ($errors->has('quantity'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="service_api">API создания заказа</label>

                    <textarea name="service_api" id="service_api" cols="45" rows="5"
                              class="form-control{{ $errors->has('service_api') ? ' is-invalid' : '' }}"
                              required>{{ $service->service_api }}</textarea>

                    @if ($errors->has('service_api'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('service_api') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="service_order_api">API проверки статуса заказа</label>

                    <textarea name="service_order_api" id="service_order_api" cols="45" rows="5"
                              class="form-control{{ $errors->has('service_order_api') ? ' is-invalid' : '' }}">{{ $service->service_order_api }}</textarea>

                    @if ($errors->has('service_order_api'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('service_order_api') }}</strong>
                            </span>
                    @endif
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

                {{--<div class="form-group">
                    <label for="reseller_price">Цена реселлеру</label>

                    <input id="reseller_price" type="text"
                           class="form-control{{ $errors->has('reseller_price') ? ' is-invalid' : '' }}"
                           name="reseller_price" value="{{ $service->reseller_price }}">

                    @if ($errors->has('reseller_price'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('reseller_price') }}</strong>
                            </span>
                    @endif
                </div>--}}

                <div class="form-group">
                    <label for="active">Активна</label>

                    <select class="selectpicker btn-group bootstrap-select dropup form-control"
                            data-style="form-control"
                            name="active"
                            id="active">
                        <option value="1" {{ $service->active == 1 ? 'selected' : '' }}>
                            Да
                        </option>
                        <option value="0" {{ $service->active == 0 ? 'selected' : '' }}>
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
                        Обновить
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
    $(function () {
        // handle category change
        $('#root_category').on('change', function (e) {

            var _root = $(this).val();
            var _categorySelect = $('#category_id');
            var _currentId = {{ $service->category_id }};

            $('option:not(:disabled)', _categorySelect).remove();

            $.post('/ajax/get-descendants', {
                root: _root,
                _token: '{!! csrf_token() !!}'
            }, function (response) {
                $.each(response.categories, function (i, k) {
                    _categorySelect.append('<option value="' + k.id + '">' + ("&nbsp;&nbsp;&nbsp;".repeat(k.depth - 1)) + k.name + '</option>');
                    $('#category_id option[value=' + _currentId + ']').attr('selected', true);
                });
            }).fail(function (error) {
                console.log(error);
            });
        });

        $('#add').click(function () {
            var lastField = $('#multi-input div.field-wrapper:last');
            var id = (lastField && lastField.length && lastField.data('index') + 1) || 1;
            var fieldWrapper = $('<div class="field-wrapper row mt-1" id="field-' + id + '" />');

            fieldWrapper.attr('data-index', id);

            var fieldQuantity = $('<div class="col-md-5"><input type="text" class="form-control" name="quantities[' + id + ']" placeholder="Количество"></div>');
            var fieldPrice = $('<div class="col-md-5"><input type="text" class="form-control" name="prices[' + id + ']" placeholder="Цена"></div>');
            var removeButton = $('<div class="col-md-2"><button class="btn btn-danger" type="button"><i class="fa fa-minus"></i></button></div>');

            removeButton.click(function () {
                $(this).parent().remove();
            });

            fieldWrapper.append(fieldQuantity);
            fieldWrapper.append(fieldPrice);
            fieldWrapper.append(removeButton);
            $("#multi-input").append(fieldWrapper);
        });

        $('.btn-remove').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>
@endpush
