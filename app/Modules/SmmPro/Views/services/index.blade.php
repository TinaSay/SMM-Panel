@extends('layouts.app')
@section('title','Сервисы')
@section('content')
    <a href="{{ route('service.create') }}" class="btn btn-primary btn-lilac">Добавить сервис</a>
    @if($services->isEmpty())
        <h3>Нет сервисов</h3>
    @else
        @if(session()->has('success'))
            <input type="hidden" id="success-session" value="{{ session('success') }}">
        @elseif((session()->has('fail')))
            <input type="hidden" id="fail-session" value="{{ session('fail') }}">
        @endif

        <div class="clearfix">
            <table-vue table-id="services-list"
                       class="services-list"
                       url="/ajax/get-services"
                       placeholder="Поиск"
                       v-bind:columns="[
                            {name: 'ID', slug: 'id', width: 70},
                            {name: 'Название', slug: 'name', width: 450},
                            {name: 'Описание', slug: 'description', sortable: false, width: 300},
                            {name: 'Категория', slug: 'category_id'},
                            {name: 'Количество', slug: 'quantity'},
                            {name: 'Цена', slug: 'price'},
                            {name: 'Дата', slug: 'created_at'},
                            {slug: 'actions', className: 'actions text-right', sortable: false, width: 150}
                       ]"></table-vue>
        </div>
    @endif
    <div class="mt-3">
        <a href="{{ route('catalog') }}" class="btn btn-info btn-lilac">На главную</a>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            var _successSession = $('#success-session').val();
            var _failSession = $('#fail-session').val();
            if (_successSession != null) {
                window.toastr.success(_successSession);
            } else if (_failSession != null) {
                window.toastr.error(_failSession);
            }
            /*$('.table-services').DataTable({
                "language": {
                    "search": 'Поиск',
                    "lengthMenu": "Показать _MENU_ записей",
                    "info": "Страница _PAGE_ из _PAGES_",
                    "infoFiltered": " - найдено из _MAX_ записей",
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    },
                }
            });*/
        });
    </script>
@endpush
