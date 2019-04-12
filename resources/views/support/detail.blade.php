@extends('layouts.smm-layout')

@section('title','Скриншот')

@section('content')
    <div class="container">
        <div class="row justify-content-center task-list">
            <div class="col-sm-8">
                <img src="{{ asset('uploads/support/').'/'.$screenshot->image }}" alt="">
            </div>


        </div>

        <a class="btn btn-primary btn-gray" href="{{ '/help/admin' }}">
            Назад
        </a>
    </div>
@endsection
