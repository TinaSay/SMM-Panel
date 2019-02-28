@extends('layouts.app')
@section('title','Каталог')
@section('content')
    <div id="app">
        <catalog></catalog>
    </div>
@endsection
@push('scripts')
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>

        @if ($errors->any())
        $(function () {
            @foreach ($errors->all() as $error)
            toastr.error('{{  $error }}');
            @endforeach
        });
        @endif

        @if (Session::has('messages') && count(Session::get('messages')))
        $(function () {
            @foreach (Session::get('messages') as $message)
            toastr.{{ $message['class'] }}('{{  $message['message'] }}');
            @endforeach
        });
        @endif
    </script>

@endpush
