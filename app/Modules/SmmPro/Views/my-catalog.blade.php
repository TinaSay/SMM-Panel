@extends('layouts.app')
@section('title','Каталог наших услуг')
@section('content')

    <div id="app" class="services-catalog">
        <div class="services-catalog-group">
            <h4 class="services-catalog-action">1. Выберите социальную сеть</h4>
            <select class="selectpicker services-catalog-select form-control">
                @foreach($socialNetworks as $socialNetwork)
                    <option value="{{$socialNetwork->id}}"
                            data-content="<span><img src={{asset('images/icons/')}} alt>Instagram</span>">
                        {{$socialNetwork->name}}
                    </option>
                @endforeach
            </select>
        </div>


    </div>

@endsection
@push('scripts')
    <script src="{{asset('js/bootstrap-select.min.js') }}"></script>
    <script>
        $('.services-catalog-select').selectpicker();
    </script>

    <script>
        $(document).ready(function () {

        });

    </script>
@endpush
