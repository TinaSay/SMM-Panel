@extends('layouts.app')
@section('title','Редактировать категорию')
@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="{{ route('category.update',$category->id) }}"
                  enctype="multipart/form-data" id="edit-form">
                @csrf
                <div class="form-group">
                    <label for="name">Название</label>

                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                           name="name" value="{{ $category->name }}" autofocus>

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
                              class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ $category->description }}</textarea>
                    @if ($errors->has('description'))
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="alias">Slug (Короткое название латинскими буквами)</label>

                    <input id="alias" type="text" name="alias" value="{{ $category->slug }}"
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
                    <div class="avatar-upload">
                        <div class="avatar-edit">
                            <input id="imageUpload" type="file" accept=".png, .jpg, .jpeg"
                                   class="form-control{{ $errors->has('icon') ? ' is-invalid' : '' }}"
                                   name="icon"
                                   value="{{ $category->icon == null ? asset('images/ava.png') :asset('uploads/icons'.$category->icon)}}">
                            <label for="imageUpload">
                                <i class="fas fa-pencil-alt"></i>
                            </label>
                        </div>
                        <div class="avatar-preview">
                            <div id="imagePreview"
                                 style="background-image:url({{ asset('uploads/icons/'.$category->icon) }})">
                            </div>
                        </div>

                        @if ($errors->has('icon'))
                            <span class="invalid-feedback" role="alert">
                                     <strong>{{ $errors->first('icon') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="active">Активна</label>

                    <select class="selectpicker btn-group bootstrap-select dropup form-control"
                            data-style="form-control{{ $errors->has('active') ? ' is-invalid' : '' }}"
                            name="active"
                            id="active">
                        <option value="1" {{ $category->active == 1 ? 'selected' : '' }}>
                            Да
                        </option>
                        <option value="0" {{ $category->active == 0 ? 'selected' : '' }}>
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
                    <label for="root_category">Категория</label>

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
                        <option selected value="null"> -- выберите категорию --</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lilac" id="button-update">
                        Обновить
                    </button>
                    <a class="btn btn-gray" href="{{ route('categories.index') }}">
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
            loadCategories({{ $category->id }});

            // handle category change
            $('#root_category').on('change', function (e) {
                var _root = $(this).val();
                var _categorySelect = $('#category_id');
                var _currentId = {{ $category->id }};

                $('option:gt(0)', _categorySelect).remove();

                $.post('/ajax/get-descendants', {
                    root: _root,
                    _token: '{!! csrf_token() !!}'
                }, function (response) {
                    $.each(response.categories, function (i, k) {
                        if (_currentId != k.id) {
                            console.log(_currentId);

                            _categorySelect.append('<option value="' + k.id + '">' + ("&nbsp;&nbsp;&nbsp;".repeat(k.depth - 1)) + k.name + '</option>');
                            $('#category_id option[value=' + _currentId + ']').attr('selected', true);
                        }
                        _categorySelect.val({{ $category->parent_id }});

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

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#imagePreview').hide();
                        $('#imagePreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#imageUpload").change(function () {
                readURL(this);
            });

        });
    </script>
@endpush
