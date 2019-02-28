@extends('layouts.app')
@section('title','Новая категория')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('category.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Название</label>

                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required
                           autofocus>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="description">Описание</label>

                    <textarea name="description" id="description" cols="45" rows="7"
                              class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{old('description')}}</textarea>

                    @if ($errors->has('description'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="alias">Slug (Короткое название латинскими буквами)</label>

                    <input id="alias" type="text" name="alias" value="{{ old('alias') }}"
                           class="form-control{{ $errors->has('alias') ? ' is-invalid' : '' }}"
                           placeholder="например: instagram" onfocus="this.placeholder = ''"
                           onblur="this.placeholder = 'например: instagram'">

                    @if ($errors->has('alias'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('alias') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="icon">Иконка</label>
                    <img src="" alt="" class="img-thumbnail d-none" id="preview">

                    <input id="icon" type="file"
                           class="form-control{{ $errors->has('icon') ? ' is-invalid' : '' }}" name="icon"
                           value="{{ old('icon') }}">

                    @if ($errors->has('icon'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('icon') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="active">Активна</label>

                    <select
                        class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('active') ? ' is-invalid' : '' }}"
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
                    <label for="parent_id">Категория</label>

                    <select
                        class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('root_category') ? ' is-invalid' : '' }}"
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
                    <select
                        class="selectpicker btn-group bootstrap-select dropup form-control{{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                        data-style="form-control"
                        name="category_id"
                        id="category_id">
                        <option disabled selected value> -- выберите категорию --</option>
                    </select>
                    @if ($errors->has('category_id'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('parent_id') }}</strong>
                            </span>
                    @endif
                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lilac">
                        Создать
                    </button>
                    <a class="btn btn-primary btn-gray" href="{{ route('categories.index') }}">
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
            /*$('#icon').on('change', function (e) {
                var icon = $('#icon').val();
                $('#preview').removeClass('d-none').attr('src', icon);
            });*/


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
