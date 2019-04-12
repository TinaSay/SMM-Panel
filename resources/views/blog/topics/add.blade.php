@extends('layouts.smm-layout')

@section('title', $pageTitle)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-8">
                {!! Form::open(['route' => 'topic.save']) !!}

                {!! Form::hidden('blog_id', $blog->id) !!}

                <div class="form-group required">
                    <label for="name" class="control-label">
                        Название
                    </label>
                    {!! Form::text('name', old('name'), [
                        'class' => 'form-control'.($errors->has('name') ? ' is-invalid' : '')
                    ]) !!}
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            Укажите название темы.
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="description" class="control-label">
                        Описание
                    </label>
                    {!! Form::textarea('description', old('description'), [
                        'class' => 'form-control resize-vertical summernote'
                    ]) !!}
                </div>

                <div class="form-group">
                    <button class="btn btn-success" type="submit">Сохранить</button>
                    <a href="{{ route('blog.topics', $blog->id) }}" class="btn btn-secondary">Отмена</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.summernote').summernote();
    });
</script>
@endpush