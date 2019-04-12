@extends('layouts.smm-layout')

@section('title', $pageTitle)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <a href="{{ route('topic.add', $blog->id) }}" class="btn btn-primary btn-lilac">Добавить тему</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-sm-8 mt-3">
                @if ($topics->isNotEmpty())
                    <div class="topics-wrapper">
                        @foreach ($topics as $topic)
                            <div class="topic-item">
                                <div class="topic-item-name">
                                    <a href="{{ route('topic.posts', $topic->id) }}">
                                        {{ $topic->name }}
                                    </a>
                                </div>
                                <div class="topic-item-description">
                                    @if ($topic->description)
                                        {!! $topic->description !!}
                                    @endif
                                </div>
                                <div class="topic-item-posts">
                                    Постов: {{ $topic->posts()->count() }}
                                </div>
                                <div class="topic-item-actions">
                                    <a href="{{ route('topic.edit', $topic->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button data-href="{{ route('topic.delete', $topic->id) }}" data-name="{{ $topic->name }}" data-toggle="modal" data-target="#delete-modal" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        В этом блоге пока нет тем.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Подтвердите удаление</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить тему <b>"<span class="name"></span>"</b>?
                </div>
                <div class="modal-footer">
                    <a href="" class="btn btn-danger">Удалить</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#delete-modal').on('shown.bs.modal', function (e) {
            var button = $(e.relatedTarget);
            var name = button.attr('data-name');
            var recipient = button.attr('data-href');

            var modal = $(this);
            modal.find('.name').empty().text(name);
            modal.find('.btn-danger').attr('href', recipient);
        });
    });
</script>
@endpush