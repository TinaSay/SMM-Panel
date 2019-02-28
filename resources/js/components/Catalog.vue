<template>
    <div>
        <div class="add">
            <div class="add__inner">
                <div id="wrap">
                    <div class="shop__socials-selector clearfix">
                        <h3>Выберите соц. сеть:</h3>
                        <ul class="selector-wrapper socials list-inline">
                            <li class="list-inline-item" v-for="item in cats">
                                <a :class="[{active: social.id == item.id}]"
                                   class="social-network shop__social-selector"
                                   @click="getChild(item)">
                                    <img v-if="item.icon" :src="'storage/uploads/' + item.icon" alt="">
                                    {{ item.name }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="shop__categories-selector clearfix" v-if="social.children">
                        <h3>Выберите сервис:</h3>
                        <div class="selector-wrapper subcategory">
                            <a :class="[{active: service.id == item.id || filter.id == item.id}]"
                               class="shop__category-selector sub-category"
                               v-for="item in social.children" @click="getChild(item, service)">{{ item.name }}</a>
                        </div>
                    </div>

                    <div class="shop__categories-selector clearfix" v-if="filter.children">
                        <h3>Выберите фильтр:</h3>
                        <div class="selector-wrapper subcategory">
                            <a :class="[{active: service.id == item.id}]" class="shop__category-selector sub-category"
                               v-for="item in filter.children" @click="getChild(item, service)">{{ item.name }}</a>
                        </div>
                    </div>

                    <div v-if="service.services">
                        <h3>Выберите услугу:</h3>
                        <div class="selector-wrapper services row justify-content-center">
                            <div v-for="(form, k) in service.services" class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                <form v-on:submit.prevent="Create(form, $event)" class="serviceForm mb-3">
                                    <div class="text-right">
                                        <a href="#" id="details-link">детали</a>
                                    </div>

                                    <div class="details">
                                        <strong>Вид накрутки</strong>
                                        <p :id="'service-name-' + k" class="pr-2 pl-2 mt-3"
                                           style="height: 60px; overflow: hidden;">
                                            {{ form.name }}
                                        </p>
                                        <strong>Описание услуги</strong>
                                        <p :id="'service-description-' + k"
                                           style="height: 66px; overflow: hidden;">
                                            {{ form.description }}
                                        </p>
                                        <strong>Цена</strong>
                                        <p>
                                            {{ number_format(form.price, 0, ',', ' ') }}<strong> сум</strong>
                                        </p>
                                        <strong>Количество</strong>
                                        <p>{{ number_format(form.quantity, 0, ',', ' ') }}</p>
                                    </div>

                                    <h3 :id="'service-name-' + k" class="pr-2 pl-2 mt-3"
                                        style="height: 60px; overflow: hidden;">
                                        {{ form.name }}
                                    </h3>
                                    <!--<p :id="'service-description-' + k" class="text-center"
                                       style="height: 66px; overflow: hidden;">
                                        {{ form.description }}
                                    </p>-->
                                    <label class="d-block col-form-label text-muted">
                                        Ссылка
                                    </label>
                                    <div class="form-group">
                                        <input type="url" class="form-control" value="" required=""
                                               placeholder="Указывать полную ссылку на публикацию">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="form-group">
                                                    <span id="servicePrice"
                                                          class="bold-price">{{ number_format(form.price, 0, ',', ' ') }}<strong> сум</strong></span>
                                                <span class="small">Количество: {{ number_format(form.quantity, 0, ',', ' ') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-center pt-2">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 mb-2" style="max-width: 49%">
                                                    <cart-button-add :item="form"></cart-button-add>
                                                </div>
                                                <div class="col-12 col-sm-6" style="max-width: 49%">
                                                    <button type="submit"
                                                            class="send-order d-block btn btn-primary btn-block pull-right"
                                                            data-oneservice="2">
                                                        Купить
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div v-if="noResult">
                        <div class="row justify-content-center">
                            <div class="col-12 text-center pt-4 pb-4">
                                Таких услуг пока нет.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modalka" :class="[{active: modal}]">
            <div class="modal-content">
                <div class="closebutton" @click="modalHide">x</div>
                <h2>Спасибо! Ваш заказ принят!</h2>
                <span>ID вашего заказа: {{ response.id }}</span>
            </div>
        </div>
    </div>
</template>
<style scoped>
    .serviceForm {
        padding: 10px;
    }

    .send-order {
        height: 50px;
        background-color: #7acb51 !important;
        border-radius: 5px;
        color: #fff;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: inset 0px 1px 0px rgba(255, 255, 255, 0.05);
        user-select: none;
        box-sizing: border-box;
        border: none;
        margin: 0 auto;
        font-size: 14px;
    }

    .add-cart .btn-block {
        font-size: 14px;
    }

    /*safari*/

    @media (min-width: 992px) {
        .services .col-lg-4 {
            max-width: 33%;
        }
    }


    @media (max-width: 570px) {
        .d-block,
        .send-order {
            margin-bottom: 5px;
        }
    }

    @media (max-width: 480px) {
        .list-inline-item {
            display: block;
        }

        .list-inline-item a {
            display: block;
            width: 100%;
            height: auto;
        }

        .list-inline > li {
            display: block;
        }

        .list-inline-item a,
        .shop__social-selector,
        .shop__category-selector {
            display: block;
            width: 100%;
            height: auto;
        }

        /*.shop__categories-selector {
            margin-right: 13px;
            margin-left: 17px;
        }*/
        .btn-block {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .add-cart {
            font-size: 14px;
        }
    }

</style>
<script>
    export default {
        data: function () {
            return {
                cats: [],
                filter: [],
                modal: false,
                noResult: false,
                response: {},
                service: {},
                social: {}
            };
        },
        methods: {
            Create: function (form, e) {
                var link = e.target[0]['value'];
                let uri = '/order';
                var config = {
                    headers: {'X-CSRF-TOKEN': Laravel.csrfToken}
                };

                let data = new FormData();

                data.append('servid', form.id);
                data.append('link', link);

                axios.post(uri, data, config)
                    .then(function (response) {
                        this.response = response.data;
                        this.modal = true;
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error.response.data);
                        this.$nextTick(function () {
                            toastr.error(error.response.data);
                        });
                    }.bind(this));
            },
            getChild: function (item, service) {
                var _service = service || null;

                this.service = {};

                if (item.children.length>1) {
                    this.filter = [];
                    if (!_service) {
                        this.social = item;
                    } else {
                        this.filter = item;
                    }
                } else {
                    this.getService(item);
                }
            },

            getData: function () {

                axios.get('/cat-categories/').then(function (response) {
                    console.log(response.data);

                    this.cats = response.data;
                }.bind(this));
            },
            getService: function (service) {
                this.service = service;
                this.noResult = !this.service.services.length;

                if (this.service.services.length) {
                    this.service.services.sort(function (a, b) {
                        return a.price - b.price;
                    });
                }
            },
            modalHide: function () {
                this.modal = false;
            },
            number_format: function (number, decimals, dec_point, thousands_sep) {
                var i, j, kw, kd, km;

                if (isNaN(decimals = Math.abs(decimals))) {
                    decimals = 2;
                }
                if (dec_point == undefined) {
                    dec_point = ",";
                }
                if (thousands_sep == undefined) {
                    thousands_sep = ".";
                }

                i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

                if ((j = i.length) > 3) {
                    j = j % 3;
                } else {
                    j = 0;
                }

                km = (j ? i.substr(0, j) + thousands_sep : "");
                kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
                kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

                return km + kw + kd;
            }
        },
        mounted: function () {
            this.getData();
        }
    }
</script>
