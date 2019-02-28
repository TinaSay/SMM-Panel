@extends('layouts.app')
@section('title','Личный кабинет')
@section('content')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

@endsection
@stack('scripts')

