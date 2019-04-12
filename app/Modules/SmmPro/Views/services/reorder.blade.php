@extends('layouts.smm-layout')
@section('title','Сервисы')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <a href="{{ route('service.create') }}" class="btn btn-primary btn-lilac">Добавить сервис</a>
                <a href="{{ route('services.index') }}" class="btn btn-primary btn-lilac">К списку сервисов</a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                @if(session()->has('success'))
                    <input type="hidden" id="success-session" value="{{ session('success') }}">
                @elseif((session()->has('fail')))
                    <input type="hidden" id="fail-session" value="{{ session('fail') }}">
                @endif

                <div class="clearfix">
                    <vue-sortable-list table-id="services-list" url="/ajax/get-services" save-url="/services/save-sorting" :columns="[
                    {name: 'ID', slug: 'id', width: 70},
                    {name: 'Название', slug: 'name'},
                    {name: 'Категория', slug: 'category_id', width: 240},
                    {name: 'Количество', slug: 'quantity', width: 130},
                    {name: 'Цена', slug: 'price', width: 170},
                    {slug: 'actions', className: 'actions text-right', sortable: false, width: 150}
                    ]"></vue-sortable-list>
                </div>
                <div class="mt-3">
                    <a href="{{ route('catalog') }}" class="btn btn-info btn-lilac">На главную</a>
                </div>
            </div>
        </div>
    </div>
@endsection
