<template>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle cart--dropdown-toggle" data-toggle="dropdown">
            <i class="fas fa-shopping-cart yellow-icons"></i>
        </a>
        <ul class="dropdown-menu dropdown-user animated flipInY cart--dropdown">
            <li v-if="!items.length" class="cart--dropdown cart--zero-state text-center">
                Корзина пуста
            </li>
            <li v-if="items.length" class="cart--dropdown col-item">
                <div class="info">
                    <div v-for="item in items" class="row">
                        <div class="col-12 truncate">
                            {{ item.name }}
                        </div>
                        <div class="col-12">
                            {{ numberFormat(item.price, 0, ',', ' ') }} x {{ numberFormat(item.qty, 0, ',', ' ') }}
                            <span class="float-right">
                                <strong>{{ numberFormat(item.price * item.qty, 0, ',', ' ') }} сум</strong>
                            </span>
                        </div>
                    </div>
                    <div class="separator"></div>
                    <div class="row mt-4">
                        <div class="col-4">
                            Итого:
                        </div>
                        <div class="col-8 text-right">
                            <strong>{{ numberFormat(total, 0, ',', ' ') }} сум</strong>
                        </div>
                    </div>
                    <div class="row mb-3 mt-4">
                        <div class="col-12">
                            <a href="/cart/checkout" class="btn btn-block btn-primary" type="button">
                                <i class="fas fa-cash-register"></i>
                                Оформить
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-block btn-danger" type="button" @click.prevent="clearCart">
                                <i class="far fa-trash"></i>
                                Очистить
                            </button>
                        </div>
                    </div>
                    <div class="clearfix">
                    </div>
                </div>
            </li>
        </ul>
    </li>
</template>

<style scoped>
    .cart--dropdown-toggle i {
        color: #ffc200;;
    }

    .cart--dropdown .cart--zero-state, .cart--dropdown .col-item {
        padding: 10px;
    }

    .dropdown-menu[x-placement^=bottom] {
        left: -50px !important;
    }

    .truncate {
        width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .separator {
        border-bottom: 1px solid #eee;
        height: 10px;
    }
</style>

<script>
    import bus from './../bus';

    export default {
        data: function () {
            return {
                items: [],
                total: 0
            };
        },
        methods: {
            clearCart: function () {
                axios.post('/ajax/clear-cart')
                    .then(function (response) {
                        this.items = [];
                        this.total = 0;
                        this.$nextTick(function () {
                            toastr.success('Корзина очищена.');
                        });
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error.response.data.message);
                    });
            },
            getContents: function () {
                axios.post('/ajax/get-cart-contents')
                    .then(function (response) {
                        this.items = response.data.items;
                        this.items.forEach(function (i, k) {
                            this.total += parseInt(i.price) * parseInt(i.qty);
                        }.bind(this));
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error.response.data.message);
                    });
            },
            numberFormat: function (number, decimals, dec_point, thousands_sep) {
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
            this.getContents();
            bus.$on('item-added', function () {
                this.getContents();
            }.bind(this));
        }
    }
</script>
