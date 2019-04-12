@extends('layouts.smm-layout')

@section('title','Тех. поддержка')

@section('content')
    <div class="container">
        <div class="row justify-content-center task-list">
            <div class="col-sm-10">
                @if ($feedbacks->isEmpty())
                    <div class="no-orders-container">
                        <h3 class="no-orders-heading">На данный момент обращений нет.</h3>
                        <div class="ghost-icon"></div>
                    </div>
                @else

                    <div class="col-12 col-sm-12 col-md-12 table-responsive">

                        <table class="table table-hover">
                            <thead>
                            <tr class="table-info">
                                <th>Дата</th>
                                <th>Пользователь</th>
                                <th>Сообщение</th>
                                <th>Тема</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($feedbacks as $feedback)

                                <tr {{$feedback->is_read==0 ? 'class=font-weight-bold' : ''}}>
                                    <td>{{ $feedback->created_at->format('d M y') }}</td>
                                    <td>{{ $feedback->user->login }}</td>
                                    <td><a href="/screenshot/{{$feedback->id}}">{{ $feedback->message }}</a></td>
                                    <td>{{ Help::getSubjects()[$feedback->subject] }}</td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>

                @endif
            </div>
        </div>
    </div>
@endsection
