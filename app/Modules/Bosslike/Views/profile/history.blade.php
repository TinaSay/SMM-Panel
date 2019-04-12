@extends('layouts.app')
@section('title','История')
@section('content')

    <div class="row justify-content-left">

        <div class="filter_history">
            <div class="date_filter_select">
                <input type="text" name="daterange" placeholder="Дата выполнения" />
            </div>
            <div class="dropdown-box">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="type_filter_select" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Любой тип
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item common_selector history_type" data-value="0"><span class="text">Любой тип</span></a>
                        <a class="dropdown-item common_selector history_type" data-value="in"><span class="text-success"><i class="fa fa-plus-circle"></i> Доход</span></a>
                        <a class="dropdown-item common_selector history_type" data-value="out"><span class="text-danger"><i class="fa fa-minus-circle"></i> Расход</span></a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="action_filter_select" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Все операции
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item common_selector history_action" data-value="0"><span class="text">Любое действие</span></a>
                        @foreach($actions as $action)
                            <a class="dropdown-item common_selector history_action" data-value="{{ $action->action }}"><span class="text">{{ $action->action }}</span></a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-12 table-responsive">

            <table id="transaction_history" class="table table-hover">
                <thead>
                    <tr class="table-info">
                        <th>Дата</th>
                        <th>Тип</th>
                        <th>Операция</th>
                        <th>Описание</th>
                        <th>Сум</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <div class="pagination-cover">

            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('js//moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/daterangepicker.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daterangepicker.css') }}" />

    <script>


    </script>
@endpush