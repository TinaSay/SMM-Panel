@extends('layouts.app')
@section('title','Редактировать сервис')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('service.update', $service->id) }}">
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
                                value="{{ $parentCategory->id }}" {{ $service->category_id == $parentCategory->id ? 'selected' :'' }}>
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

                    </select>
                </div>


                <div class="form-group">
                    <label for="quantity">Количество</label>

                    <input id="quantity" type="number"
                           class="form-control{{ $errors->has('quantity') ? ' is-invalid' : '' }}"
                           name="quantity" value="{{ $service->quantity }}">

                    @if ($errors->has('quantity'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="service_api">Добавить API</label>

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
                    <label for="service_order_api">Добавить чек-статус API</label>

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

                <div class="form-group">
                    <label for="price">Цена</label>

                    <input id="price" type="text"
                           class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}"
                           name="price" value="{{ $service->price }}" required>

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
                           name="reseller_price" value="{{ $service->reseller_price }}">

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
            // call ajax to get current category
            loadCategories({{ $service->category_id }});

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

                        if (_currentId != k.id) {

                            _categorySelect.append('<option value="' + k.id + '">' + ("&nbsp;&nbsp;&nbsp;".repeat(k.depth - 1)) + k.name + '</option>');
                            $('#category_id option[value=' + _currentId + ']').attr('selected', true);
                        }


                    });
                }).fail(function (error) {
                    console.log(error);
                });
            });


            function loadCategories($id) {
                var _rootCategorySelect = $('#root_category');
                var _categorySelect = $('#category_id');

                $.post('/ajax/get-ancestors', {
                    id: $id,
                    _token: '{!! csrf_token() !!}'
                }, function (response) {
                    _rootCategorySelect.val(response.root.id);
                    _rootCategorySelect.trigger('change');

                    _categorySelect.val($id);
                }).fail(function (error) {
                    console.log(error);
                });
            }
        });
    </script>
@endpush
