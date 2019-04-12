@extends('layouts.catalog')
@section('title','Выберите услугу')
@section('content')
    <div class="alert-wrapper">
        <div class="alert-catalog alert alert-info alert-dismissable text-center">
            <ul>
                <li>
                    АККАУНТЫ ДОЛЖНЫ БЫТЬ ОТКРЫТЫМИ И ИМЕТЬ АВАТАРКУ
                </li>
                <li>
                    ОДНОВРЕМЕННЫЙ ЗАПУСК НЕСКОЛЬКИХ ОДНОТИПНЫХ НАКРУТОК ПО ОДНОЙ ССЫЛКЕ ПРИВЕДЕТ К НЕДОКРУТКЕ БЕЗ
                    ВОЗМОЖНОСТИ ПЕРЕРАСЧЕТА
                </li>
                <li>
                    ПОСЛЕ ОФОРМЛЕНИЯ ЗАКАЗ НЕВОЗМОЖНО БУДЕТ ОТМЕНИТЬ
                </li>
                <li>
                    ПЕРЕД ЗАКАЗОМ ПРОЧТИТЕ ОПИСАНИЕ УСЛУГИ!
                </li>
                <li>
                    ПРИ ОТМЕНЕ ВАШЕГО ЗАКАЗА С НАШЕЙ СТОРОНЫ ПОТРАЧЕННЫЕ СРЕДСТВА БУДУТ ВОЗВРАЩЕНЫ НА ВАШ СЧЕТ
                </li>
            </ul>
        </div>
    </div>

    <div id="app" class="services-catalog">
        @include('smmpro::additional')

        <div class="services-catalog-group products_cont">
            <div class="services-catalog-row services-products">
                @foreach($services as $service)
                    <div class="services-item products col-xxl-4 col-xl-6 col-lg-12" data-id="{{$service->id}}"
                         data-cat="{{$service->id}}">
                        <div class="services-inner-box">
                            <span class="services-item-title">{{$service->name}}</span>
                            <span class="services-item-description">{!! $service->description !!}</span>
                            <input type="text" class="prod_link" placeholder="Введите ссылку">
                            <div class="item-row">
                                <div class="services-item-range-slider">
                                    <input type="text" class="js-range-slider hidden"
                                           data-grid="true"
                                           data-values="[{{ implode(',', $service->quantities->toArray()) }}]"
                                           data-prices="[{{ implode(',', $service->prices->toArray()) }}]"
                                           name="my_range" value=""/>
                                </div>
                                <div class="price">
                                    <span class="services-item-price"><price>{{number_format($service->prices->first(), 0, ',', ' ')}}</price> сум</span>
                                <!--<span class="services-item-amount">Кол-во: {{$service->quantity}}</span>-->
                                </div>
                                <div class="services-item-buttons">
                                    <button class="services-item-buy services-item-btn buyit" data-qty=""
                                            data-cat="{{$service->id}}" data-id="{{$service->id}}">Купить
                                    </button>
                                    <button class="services-item-add-to-cart services-item-btn"><i
                                            class="fas fa-fw fa-cart-arrow-down"></i>
                                        В корзину
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <form action="/order" id="order-post" method="post" style="display: none;">
            @csrf
            <input type="hidden" id="servid" name="servid">
            <input type="hidden" id="link" name="link">
            <input type="hidden" id="qty" name="qty">
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('js/ionRangeSlider/css/ion.rangeSlider.min.css') }}">
    <style>
        .services-catalog-row.services-products .services-item .services-item-title {
            height: 56px;
            overflow: hidden;
        }

        .services-catalog-row.services-products .services-item .services-item-description {
            display: block;
            height: 60px;
            overflow: hidden;
        }

        .hidden {
            display: none !important;
        }

        .services-item-range-slider {
            margin-bottom: 1em;
        }

        .irs--round .irs-from, .irs--round .irs-to, .irs--round .irs-single {
            background-color: #47347b;
            color: #fff !important;
        }

        .irs--round .irs-from:before, .irs--round .irs-to:before, .irs--round .irs-single:before {
            border-top-color: #47347b;
        }

        .irs--round .irs-handle {
            border: 4px solid #47347b;
            background-color: white;
            box-shadow: 0 1px 3px rgba(71, 52, 123, 0.3);
        }

        .irs--round .irs-bar {
            background-color: #47347b;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/jquery.equalheights.min.js') }}"></script>
    <script src="{{ asset('js/ionRangeSlider/js/ion.rangeSlider.min.js?v='.time()) }}"></script>
    <script>
        $(function () {
            $(".js-range-slider").ionRangeSlider({
                onStart: function (obj) {
                    updateCard(obj);
                },
                onFinish: function (obj) {
                    updateCard(obj);
                },
                skin: 'round'
            });

//        $('.services-item.products').equalHeights();
        });

        function updateCard(obj) {
            var priceHolder = obj.input.parent().parent().find('price');
            var price = obj.input.data('prices')[obj.from_pretty];
            var buyButton = obj.input.parent().parent().find('.buyit');

            buyButton.attr('data-qty', obj.from_value);
            priceHolder.empty().text(number_format(price, 0, ',', ' '));
        }
    </script>
@endpush
