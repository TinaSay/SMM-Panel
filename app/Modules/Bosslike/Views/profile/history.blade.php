@extends('layouts.app')
@section('title','История')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif

    @if($data->isEmpty())
        <h3>История пуста</h3>
    @else

    <div class="row justify-content-left">
        <div class="col-12 col-sm-12 col-md-12">

            <table id="transaction_history">
                <thead>
                    <tr>
                        <td>Дата</td>
                        <td>Тип</td>
                        <td>Операция</td>
                        <td>Описание</td>
                        <td>Сум</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y')}}</td>
                            <td>{!! ($item->type == 'in') ? '<span class="text-success"><i class="fa fa-plus-circle"></i></span>' : '<span class="text-danger"><i class="fa fa-minus-circle"></i></span>' !!}</td>
                            <td>{{ $item->action }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{!! ($item->type == 'in') ? '<span class="text-success">+' . $item->points . '</span>' : '<span class="text-danger">-' . $item->points . '</span>' !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endsection
