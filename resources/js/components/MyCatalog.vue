<template>
    <div>
        <h3>Выберите соц. сеть:</h3>
        <ul>
            <li class="list-inline-item" v-for="item in cats">
                {{ item.name }}
            </li>
        </ul>

    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                cats: [],
            };
        },
        methods: {
            getData: function () {
                axios.get('/my-catalog/').then(function (response) {
                    console.log(response.data);
                    this.cats = response.data.name;
                }.bind(this));
            },

        },
        mounted: function () {
            this.getData();
        }
    }
</script>

<style scoped>

</style>
