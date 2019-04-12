@extends('layouts.smm-layout')

@section('title', $pageTitle)

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <vue-table-extended table-id="posts-list" class="posts-list" url="/ajax/get-posts" :columns="[
                    {name: 'ID', slug: 'id', width: 70},
                    {name: 'Название', slug: 'name'},
                    {name: 'Категория', slug: 'category_id', width: 240},
                    {name: 'Количество', slug: 'quantity', width: 130},
                    {name: 'Цена', slug: 'price', width: 170},
                    {name: 'Дата', slug: 'created_at', width: 120},
                    {slug: 'actions', className: 'actions text-right', sortable: false, width: 150}
                ]"></vue-table-extended>
            </div>
        </div>
    </div>
@endsection