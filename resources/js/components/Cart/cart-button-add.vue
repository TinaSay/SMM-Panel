<template>
    <span>
        <button type="submit"
                class="add-cart d-block btn btn-primary btn-block pull-left" @click.prevent="addItem">
            <span class="hide-on-phone"> <i class="fas fa-fw fa-cart-arrow-down"></i></span>
            В корзину
        </button>
    </span>
</template>

<script>
    import bus from './../bus';

    export default {
        props: {
            item: {
                required: true
            }
        },
        methods: {
            addItem: function () {
                axios.post('/ajax/cart-add-item', {
                    item: this.item
                })
                    .then(function (response) {
                        this.$nextTick(function () {
                            toastr.success(response.data.message);
                        });

                        bus.$emit('item-added');
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error.message);
                    });
            }
        }
    }
</script>
