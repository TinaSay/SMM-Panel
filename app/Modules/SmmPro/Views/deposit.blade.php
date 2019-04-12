@extends('layouts.app')
@section('title','Пополнить баланс')
@section('content')
    @if(session()->has('success'))
        <div class="alert alert-info alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">×</span>
                <span class="sr-only">Close</span>
            </button>
            <ul class="list-unstyled">
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="deposit-form">
        <div class="row">
            <div class="col-sm-12">
                <form action="">
                    <input type="number" name="_amount" id="amount" placeholder="Минимум 10 000 сум" min="10000"
                           step="100"
                           data-type="number"
                           class="form-control" value="" required/>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs pay active mb-3" role="tablist">
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"
                                               class="active">
                            <img src="{{ asset('images/payme.png') }}" height="34" alt="payme">
                        </a></li>
                    <li role="presentation">
                        <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                            <img src="{{ asset('images/click.png') }}" height="34" alt="click">
                        </a></li>
                    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                            <img src="{{ asset('images/upay.png') }}" height="34" alt="upay">
                        </a></li>
                    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">
                            <img src="{{ asset('images/paynet.png') }}" height="34" alt="paynet">
                        </a></li>
                </ul>
            </div>
        </div>
        <!-- Nav tabs -->
        <div class="row">
            <div class="col-sm-12">

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="profile">
                        <form action="https://checkout.paycom.uz" class="payment-form" method="POST">
                            <!-- Идентификатор WEB Кассы -->
                            <input type="hidden" name="merchant" value="5bbf17112860d7d754f6d30c"/>

                            <!-- Сумма платежа в тиинах -->
                            <input type="hidden" name="amount"
                                   value="<?php echo isset($_GET['funds']) ? (int)$_GET['funds'] * 100 : 0; ?>"/>

                            <input type="hidden" name="account[userId]"
                                   value="picstar{{ Auth()->user()->billing_id }}"/>

                            <input type="hidden" name="referrer" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="redirect" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="callback" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="callback_timeout" value="0">
                            <input type="hidden" name="RETURN_URL" value="{{ \App\User::RETURN_URL }}">

                            <!-- Payment form with description and button -->
                            <button class="submit_btn btn btn-primary btn-mod btn-medium btn-round btn-lilac"
                                    id="payme-submit"
                                    type="submit">
                                Пополнить через Payme
                            </button>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="home">
                        <form action="https://my.click.uz/pay/" class="payment-form" method="POST">
                            <input type="hidden" name="MERCHANT_ID" value="8464">
                            <input type="hidden" name="MERCHANT_TRANS_ID"
                                   value="picstar{{ Auth()->user()->billing_id }}">
                            <input type="hidden" name="MERCHANT_SERVICE_ID" value="12564">
                            <input type="hidden" name="MERCHANT_TRANS_NOTE" value="Пополнение баланса на Picstar.uz">
                            <input type="hidden" name="SIGN_TIME" value="">
                            <input type="hidden" name="SIGN_STRING" value="">
                            <input type="hidden" name="MERCHANT_TRANS_AMOUNT" value="">

                            <input type="hidden" name="referrer" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="redirect" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="callback" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="callback_timeout" value="0">
                            <input type="hidden" name="RETURN_URL" value="{{ \App\User::RETURN_URL }}">

                            <!-- Payment form with description and button -->
                            <button class="submit_btn btn btn-primary btn-mod btn-medium btn-round btn-lilac"
                                    id="click-submit"
                                    type="submit">
                                Пополнить через Click
                            </button>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="settings">
                        <form action="https://pay.smst.uz/prePay.do" class="payment-form" method="POST">
                            <input type="hidden" name="serviceId" value="318"/>
                            <input type="hidden" name="apiVersion" value="1">
                            <input type="hidden" name="amount"
                                   value="<?php echo isset($_GET['funds']) ? (int)$_GET['funds'] * 100 : 0; ?>"/>
                            <input type="hidden" name="personalAccount"
                                   value="picstar{{ Auth()->user()->billing_id }}"/>

                            <input type="hidden" name="referrer" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="redirect" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="callback" value="{{ \App\User::RETURN_URL }}">
                            <input type="hidden" name="callback_timeout" value="0">
                            <input type="hidden" name="RETURN_URL" value="{{ \App\User::RETURN_URL }}">

                            <!-- Payment form with description and button -->
                            <button class="submit_btn btn btn-primary btn-mod btn-medium btn-round btn-lilac"
                                    id="upay-submit"
                                    type="submit">
                                Пополнить через Upay
                            </button>
                        </form>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="messages">
                        <p style="font-size: 24px; font-weight: bold;">Вы можете пополнить свой баланс через любой пункт
                            Paynet - просто назовите сервис SMM-PRO и
                            свой ID - {{ Auth()->user()->billing_id }}.</p>
                        <p>Ваш ID отображается в верхнем меню, если вы зарегистрированы и авторизованы на сайте. </p>
                        <p><strong>ВАЖНО:</strong> пополнить баланс можно ТОЛЬКО после регистрации на сайте.</p>
                        <p><strong>НЕ МЕНЕЕ ВАЖНО:</strong> если оператор Paynet говорит, что сервиса SMM-PRO нет в
                            списке - попросите его обновить конфигурацию терминала, это быстрая процедура.</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 mt-3">
                <div class="alert alert-info alert-dismissable text-center">
                    <p>
                        При оплате через приложение PayMe, Click или UPAY найдите сервис SMM-PRO и укажите свой свой ID
                        - picstar{{ Auth()->user()->billing_id }}.
                    </p>
                </div>

                <h3>Условия использования</h3>
                <p>Цены и условия услуг Picstar.uz могут быть в любой момент изменены без предварительного уведомления.
                    Актуальные условия изложены в описании услуг.
                </p>
                <p>Мы никогда не даем гарантии по скорости и срокам выполнения услуг. Все изложенные в описании числа
                    носят
                    лишь статистический характер. Получая информацию о сроках вы узнаете лишь нашу примерную оценку
                    времени.
                </p>
                <h3>Отказ от ответственности
                </h3>
                <p>Picstar.uz не несет ответственности за любой ущерб, принесенный вам или вашему бизнесу. Во время
                    нашей
                    накрутки Инстаграм не применяет санкции к аккаунтам, но если это произойдет, мы не будем нести
                    ответственность.
                </p>
                <h3>Обслуживание</h3>
                <p>
                    Допустимая погрешность выполнения -5%.
                </p>
                <p>Мы гарантируем выполнение обязательств по выбранной накрутке, но не гарантируем, что новые подписчики
                    будут с вами взаимодействовать или отвечать любым другим ожиданиям.
                </p>
                <p>Мы стремимся сделать наиболее привлекательную накрутку, но не гарантируем что 100% аккаунтов будет
                    иметь
                    аватар, описание и публикации.
                </p>
                <p>Вы соглашаетесь с тем, что не будете добавлять запрещенные материалы, включая наготу, наркотические
                    вещества, оружие, призывы к суициду или предложения по накрутке.
                </p>
                <p>Во время накрутки по большинству услуг мы ориентируемся на имеющееся в инстаграм число подписчиков,
                    лайков, просмотров или комментариев. Поэтому, во время накрутки вы не должны использовать каких-либо
                    еще
                    способов увеличения показателей на данной странице. (Исключение - накрутки id 2, 3 и 10)
                </p>
                <p>После запуска нукрутку невозможно отменить (возможно лишь в некоторых случаях).
                </p>
                <p>Во время накрутки подписчиков аккаунт должен быть открыт, если иное не оговорено в условиях выбранной
                    накрутки.
                </p>
                <p>Все статусы и дополнительная информация о заказах может быть неактуальной и обновляться с задержкой,
                    отследить актуальные показатели можно на раскручиваемой странице в инстаграм.
                </p>
                <p>Мы не оказываем поддержку и консультации по вопросам накрутки. Если вы не разбираетесь в тонкостях
                    этого
                    процесса - воспользуйтесь услугами специалиста или задавайте вопросы на форуме.
                </p>
                <p>Мы оставляем за собой право не выполнять накрутку и не возвращать средства в случае нарушения вами
                    наших
                    условий.
                </p>
                <p>Мы можем в любой момент отказать вам в обслуживании и заблокировать доступ к сайту без объяснения
                    причин.
                    Если у вас останутся вопросы или претензии, вы можете сообщить их на нашу почту.
                </p>
                <h3>Гарантия от списания подписчиков
                </h3>
                <p>Любая накрутка подписчиков в инстаграм имеет списания. Размер этих списаний зависит от способа
                    накрутки.
                </p>
                <p>Если в течение гарантийного срока после окончания накрутки у вас будут списания и станет меньше
                    подписчиков, чем изначально планируемое количество, можно запросить возврат пропорциональной части
                    оплаты или дополнительную накруткy. Гарантия действует только при списании подписчиков, накрученных
                    с
                    гарантией. Выполнение новой накрутки означает окончание гарантийного срока предыдущей накрутки на ту
                    же
                    страницу. В гарантии может быть отказано, если вы уже накручивали других подписчиков и невозможно
                    достоверно определить какие именно подписчики были списаны.
                </p>
                <h3>Политика конфиденциальности
                </h3>
                <p>Мы серьезно относимся к вашей частной жизни и предоставленной вами информации. Принимаем меры для её
                    защиты и гарантируем, что личная информация не будет передаваться третьим лицам. Кроме случаев
                    нарушения
                    законодательства.
                </p>
                <p>Условия могут изменяться в любой момент без уведомления. Каждый раз, совершая заказ, вы даете
                    согласие,
                    что изучили актуальные условия и согласны с ними.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="m-t-20">
                    <a href="{{ route('catalog') }}" class="btn btn-info btn-lilac">На главную</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('#amount').on('change keyup', function (e) {
            var $this = $(this);
            $('#pts').text($this.val());
            $('#sum').text($this.val());

            $('input[name="MERCHANT_TRANS_AMOUNT"]').val(parseInt($this.val()));
            $('input[name="amount"]').val(parseInt($this.val()) * 100);

            $.ajax({
                url: '/deposit/update/',
                type: 'GET',
                dataType: 'json',
                data: {
                    uid: $('input[name="MERCHANT_TRANS_ID"]').val(),
                    amount: parseInt($this.val())
                },
                success: function (json) {
                    $('input[name="SIGN_TIME"]').val(json.time);
                    $('input[name="SIGN_STRING"]').val(json.string);
                }
            });
        });

        $('.submit_btn').on('click', function (e) {
            e.preventDefault();

            if ($('#amount').val() < 10000) {
                window.toastr.error('Введите сумму не менее 10000 сум');
            }

            if ($(this).is('#click-submit') && $('input[name="MERCHANT_TRANS_AMOUNT"]').val() >= 10000) {
                this.form.submit();
            }
            if ($(this).is('#payme-submit') && $('input[name="amount"]').val() >= 1000000) {
                this.form.submit();
            }

            if ($(this).is('#upay-submit')) {
                $('input[name="amount"]').val(parseInt($('input[name="amount"]').val()) / 100);
                this.form.submit();
            }
        });
    </script>
@endpush
