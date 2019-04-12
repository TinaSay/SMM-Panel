@extends('layouts.smm-layout')

@section('title','Тех. поддержка')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div class="form-wrapper support-form">
                    {!! Form::open(['route' => 'help.save', 'files' => true]) !!}

                    <div class="form-group">
                        <select name="subject" id="subject"
                                class="form-control{!! $errors->has('subject') ? ' is-invalid' : '' !!}" required>
                            <option value="0" selected disabled>-- Выберите тему --</option>
                            @foreach (Help::getSubjects() as $key => $subject)
                                <option value="{{ $key }}">{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <textarea name="message" id="message" cols="30" rows="10"
                                  class="form-control{!! $errors->has('subject') ? ' is-invalid' : '' !!}"
                                  placeholder="Ваше сообщение" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="form-group">
                        <fieldset id="multi-input">
                            <legend style="font-size: 14px;">
                                <b>Загрузите снимки экрана</b>
                                <small class="d-block text-muted">Не обязательное поле, однако снимки экрана могут
                                    помочь быстрее разобраться с проблемой
                                </small>
                            </legend>
                            <div class="field-wrapper row" id="field-0" data-index="0">
                                <div class="col-md-10">
                                    <input type="file" name="images[0]" class="form-control-file"
                                           placeholder="Выберите файл">
                                </div>
                                <div class="col-md-2">

                                </div>
                            </div>
                        </fieldset>

                        <div class="multi-input-controls mt-2">
                            <button id="add" class="btn btn-primary" type="button">
                                <i class="fa fa-plus"></i>
                                Добавить снимок
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lilac">
                            Отправить
                        </button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#add').click(function () {
                var lastField = $('#multi-input div.field-wrapper:last');
                var id = (lastField && lastField.length && lastField.data('index') + 1) || 1;
                var fieldWrapper = $('<div class="field-wrapper row mt-1" id="field-' + id + '" />');

                fieldWrapper.attr('data-index', id);

                var fieldQuantity = $('<div class="col-md-10"><input type="file" class="form-control-file" name="images[' + id + ']" placeholder="Выберите файл"></div>');
                var removeButton = $('<div class="col-md-2"><button class="btn btn-danger" type="button"><i class="fa fa-minus"></i></button></div>');

                removeButton.click(function () {
                    $(this).parent().remove();
                });

                fieldWrapper.append(fieldQuantity);
                fieldWrapper.append(removeButton);
                $("#multi-input").append(fieldWrapper);

                console.log(id);
            });
        });
    </script>
@endpush
