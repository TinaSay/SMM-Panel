@extends('layouts.app')
@section('title','Каталог наших услуг')
@section('content')

    <div id="app" class="services-catalog">
        <div class="services-catalog-group">
            <h4 class="services-catalog-action">1. Выберите социальную сеть</h4>
            <select class="selectpicker services-catalog-select form-control social-option">
                @foreach($socialNetworks as $socialNetwork)
                    <option value="{{$socialNetwork->id}}">
                        {{$socialNetwork->name}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="services-catalog-group">
            <h4 class="services-catalog-action">2. Выберите сервис</h4>
            <div class="services-catalog-row services-block">
               {{-- <div class="services-item">
                    <img src="{{asset('images/item1.png')}}">
                    <span class="services-item-title">Накрутка подписчиков</span>
                </div>--}}
            </div>
        </div>


    </div>

@endsection
@push('scripts')
    {{--<script src="{{asset('js/bootstrap-select.min.js') }}"></script>
    <script>
        $('.services-catalog-select').selectpicker();
    </script>--}}

    <script>
        $(document).ready(function () {

            var currentSN = $('.social-option').val();
            loadServices(currentSN);


            $('.social-option').on('change', function (e) {
                var currentSN = $(this).val();
                loadServices(currentSN);

            });


            function loadServices(currentSN) {
                $.post('/ajax/get-descendants', {
                    root: currentSN,
                    _token: '{!! csrf_token() !!}'
                }, function (response) {
                    console.log(response.categories);
                    $.each(response.categories, function (i, k) {
                        $('.services-block').append(k.name);

                        /*console.log(k.name);*/
                        /*$('.services-block').append('<div class="services-item">');
                        $('.services-block').html('<img src="">');
                        $('.services-block').html('<span class="services-item-title">');
                        $('.services-block').html('</div>');*/

                    });
                }).fail(function (error) {
                });

            }

        });

    </script>
@endpush
