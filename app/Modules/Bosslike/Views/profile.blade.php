@extends('layouts.app')
@section('title','Настройки профиля')
@section('content')
    @if(session()->has('success'))
        <input type="hidden" id="success-session" value="{{ session('success') }}">
    @elseif((session()->has('fail')))
        <input type="hidden" id="fail-session" value="{{ session('fail') }}">
    @endif
    <div class="row">
        <div class="col-12 col-sm-6 col-md-6">
            <div class="form-group">
                <label>Одноклассники</label>
                <a href="{{ route('ok.login') }}"
                   class="btn btn-block btn-lg btn-odnoklassniki">
                    <i class="fab fa-odnoklassniki"></i>
                    Привязать аккаунт Одноклассники</a>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            var _success = ('#success-session').val();
            var _fail = ('#fail-session').val();
            if (_success != null) {
                window.toastr.success(_success);
            } else if (_fail != null) {
                window.toastr.error(_fail);
            }

            $.ajax({});

        });

    </script>
@endpush
