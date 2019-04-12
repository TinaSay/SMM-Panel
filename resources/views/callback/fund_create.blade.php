@extends('layouts.app')
@section('title','Мой баланс')
@section('content')
<div class="add-new-task-form">
    <div class="row">
        <div class="col-md-12">
            <div class="user-balance-block">
                <div class="user-balance">
                    <span>Ваш баланс: <strong>{{ $balance }}</strong> сум</span>
                    <a class="balance-refill-link" href="/deposit">Пополнить баланс</a>
                    <p class="intro alert alert-success alert-dismissable text-center">Сумма вашего баланса, включая пополнения, заработанные на заданиях, бонусы.</p>
                </div>
                <div class="user-allowed">
                    <span>Максимальный лимит для вывода: <strong>{{ number_format($allowed, 0, '', ' ') }}</strong> сум</span>
                    <p class="intro alert alert-success alert-dismissable text-center">Сумма, которую вы заработали на сайте, при помощи заданий, бонусов.</p>
                </div>
            </div>
            <div class="alert alert-warning alert-dismissable text-center">
                <p>Вы можете вывести заработанные средства на свою пластиковую карту (UzCard).<br />
                    Минимальная сумма вывода 100 000 сум.</p>
            </div>
        </div>
    </div>
    <form method="POST" id="fundMoneyForm" action="{{ route('funds.store') }}">
        <div class="page-small-title">
            <h3>Вывод средств</h3>
        </div>
        @csrf

        <div class="form-container">
            <div class="field-container">
                <label for="name">Имя</label>
                <input id="name" placeholder="Василий" name="name" value="{{ old('name') }}" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" maxlength="200" required="required" type="text">
            </div>
            <div class="field-container">
                <label for="last_name">Фамилия</label>
                <input id="last_name" placeholder="Петров" value="{{ old('last_name') }}" name="last_name" class="form-control {{ $errors->has('last_name') ? ' is-invalid' : '' }}" maxlength="200" required="required" type="text">
            </div>
            <div class="field-container">
                <label for="cardnumber">Номер карты</label>
                <input id="cardnumber" placeholder="8600 0000 0000 0000" value="{{ old('cardnumber') }}" name="cardnumber" type="text" class="form-control {{ $errors->has('cardnumber') ? ' is-invalid' : '' }}" required="required" inputmode="numeric">
            </div>
            <div class="field-container">
                <label for="amount">Сумма для вывода</label>
                <input id="amount" placeholder="100 000" value="{{ old('amount') }}" name="amount" maxlength="{{ $allowed }}" min="100000" type="text" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}" required="required" inputmode="numeric">
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lilac submit-task">
                Отправить
            </button>
            {{--<a class="btn btn-primary btn-gray" href="{{ route('tasks.all') }}">--}}
            {{--Отмена--}}
            {{--</a>--}}
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/jquery.mask.js')}}"></script>
<script>
    var allowed = {{ $allowed }};
    $('#cardnumber').mask('0000 0000 0000 0000');
    $('#amount').mask("# ##0", {
        reverse: true,
        onKeyPress: function(cep, event, currentField, options){
            var processed = cep.replace(/ /g, '');
            var output = parseInt(processed, 10);
            if(output > allowed) {
                $('#fundMoneyForm .submit-task').prop('disabled', true);
                currentField.css('border-color', 'red');
            } else {
                $('#fundMoneyForm .submit-task').prop('disabled', false);
                currentField.css('border-color', 'initial');
            }
        }
    }).attr('maxlength', allowed);
</script>
@endpush