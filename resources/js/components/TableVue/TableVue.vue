<template>
    <div class="tv--table-wrapper mt-4 mb-4">
        <div class="tv--top-controls">
            <div class="columns">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-3 tv--controls">
                        <div class="dropdown">
                            Показать&nbsp;
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                {{ limit }}
                            </button>
                            &nbsp;записей
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" :class="{active: limit == 10}" href="#"
                                   @click.prevent="setLimit(10)">10</a>
                                <a class="dropdown-item" :class="{active: limit == 25}" href="#"
                                   @click.prevent="setLimit(25)">25</a>
                                <a class="dropdown-item" :class="{active: limit == 50}" href="#"
                                   @click.prevent="setLimit(50)">50</a>
                                <a class="dropdown-item" :class="{active: limit == 100}" href="#"
                                   @click.prevent="setLimit(100)">100</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-3 tv--controls">
                        <div class="tv--has-icons icon-right">
                            <input type="text" class="form-control" v-model="search" v-on:keyup="filterTable"
                                   :placeholder="placeholder">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 col-sm-12 col-md-7 tv--controls">
                        <div class="row">
                            <div class="col">
                                <select name="root-category" id="root-category" class="form-control"
                                        v-model="rootCategory" @change="getDescendants">
                                    <option selected disabled value="0">-- Выберите корневую категорию --</option>
                                    <option v-for="category in rootCategories" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col">
                                <select name="category" id="category" class="form-control" v-model="category"
                                        @change="filterTable">
                                    <option selected disabled value>-- Выберите категорию --</option>
                                    <option v-for="category in categories" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table :id="tableId" class="table-services">
                <thead v-if="columns.length">
                <tr>
                    <th v-for="(column, i) in columns" :style="{width: columnWidth(column)}" :class="column.className">
                        {{ column.name ? column.name : null }}
                        <span v-if="column.sortable || column.sortable !== false" class="sorter">
                            <span class="sorter-icon" v-on:click="setSorting(column.slug)">
                                <i class="fas fa-sort-up"
                                   v-if="sorting.column == column.slug && sorting.order == 'asc'"></i>
                                <i class="fas fa-sort-down"
                                   v-else-if="sorting.column == column.slug && sorting.order == 'desc'"></i>
                                <i class="fas fa-sort" v-else></i>
                            </span>
                        </span>
                    </th>
                </tr>
                </thead>
                <tbody v-if="total && !loading">
                <tr v-for="(row, i) in rows">
                    <td v-for="(column, k) in columns" v-html="row[column.slug]" :class="column.className">

                    </td>
                </tr>
                </tbody>
                <tbody v-else-if="!total && !loading">
                <tr>
                    <td :colspan="columns.length" class="text-center py-3">
                        <strong>Нет результатов</strong>
                    </td>
                </tr>
                </tbody>
                <tbody v-else class="tv--loading">
                <tr>
                    <td :colspan="columns.length" class="text-center">
                        <moon-loader :loading="loading" :color="'#3490dc'" :size="'60px'"></moon-loader>
                    </td>
                </tr>
                </tbody>
                <tfoot v-if="columns.length">
                <tr>
                    <th v-for="(column, i) in columns" :style="{width: columnWidth(column)}" :class="column.className">
                        {{ column.name ? column.name : null }}
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="tv--bottom-controls">
            <div class="columns">
                <div class="col tv--controls">
                    <span class="rows-info" v-if="rows.length">
                        Показано записей с {{ (page - 1) * perPage + 1}} по {{ (page - 1) * perPage + rows.length }} из {{ total }}
                    </span>
                </div>
                <div class="col tv--controls text-right">
                    <paginate
                        v-if="pages > 1"
                        v-model="page"
                        :page-count="pages"
                        :page-range="3"
                        :margin-pages="2"
                        :click-handler="changePage"
                        :prev-text="'Пред'"
                        :next-text="'След'"
                        :container-class="'pagination m-2'"
                        :page-class="'page-item'"
                        :page-link-class="'page-link'">
                    </paginate>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .v-spinner > .v-moon {
        margin: 0 auto;
    }
</style>

<style scoped>
    .tv--top-controls .dropdown-menu .dropdown-item {
        font-size: 14px;
    }
</style>

<script>
    export default {
        props: {
            perPage: {
                type: Number,
                default: 25
            },
            placeholder: {
                type: String,
                default: 'Search'
            },
            tableId: {
                type: String,
                required: true
            },
            columns: {
                type: Array,
                required: true
            },
            url: {
                type: String,
                required: true
            },
            customData: {
                type: String,
                default: ''
            },
            showTotal: {
                type: Boolean,
                default: false
            },
            initialSorting: {
                type: Object,
                required: false,
                default: function () {
                    return {
                        column: 'id',
                        order: 'asc'
                    }
                }
            }
        },
        data: function () {
            return {
                limit: this.perPage,
                steps: [25, 50, 100, 200],
                search: null,
                total: 0,
                totalFiltered: 0,
                rows: [],
                page: 1,
                pages: 0,
                loading: true,
                sorting: this.initialSorting,
                totalData: null,
                rootCategories: [],
                categories: [],
                rootCategory: 0,
                category: 0,
                filter: {}
            };
        },
        methods: {
            setLimit(number) {
                this.limit = number;
                this.callAjax();
            },
            columnWidth(column) {
                return column.width ? column.width + 'px' : false;
            },
            callAjax() {
                axios.interceptors.request.use(function (config) {
                    this.loading = true;

                    return config;
                }.bind(this), function (error) {
                    return Promise.reject(error);
                });

                axios.interceptors.response.use(function (config) {
                    this.loading = false;

                    return config;
                }.bind(this), function (error) {
                    return Promise.reject(error);
                });

                axios.post(this.url, {
                    limit: this.limit,
                    search: this.search,
                    page: this.page,
                    filter: this.filter,
                    customData: this.customData,
                    category: this.category
                })
                    .then(function (response) {
                        console.log(response.data);
                        this.total = response.data.total;
                        this.totalFiltered = response.data.totalFiltered;
                        this.rows = response.data.rows;
                        this.pages = Math.ceil(this.total / this.limit);
                        this.totalData = response.data.totalData || null;
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            setSorting(column) {
                if (column == this.sorting.column) {
                    if (this.sorting.order == 'asc') {
                        this.sorting.order = 'desc';
                    } else {
                        this.sorting.order = 'asc';
                    }
                } else {
                    this.sorting.column = column;
                    this.sorting.order = 'asc';

                    this.callAjax();
                }
            },
            changePage(page) {
                this.page = page;

                this.callAjax();
            },
            filterTable() {
                this.callAjax();
            },
            getCategories() {
                axios.post('/ajax/get-categories')
                    .then(function (response) {
                        this.rootCategories = response.data.rootCategories;
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error);
                    }.bind(this));
            },
            getDescendants() {
                axios.post('/ajax/get-descendants', {
                    root: this.rootCategory
                })
                    .then(function (response) {
                        this.categories = response.data.categories;

                        this.filter = {
                            type: 'category',
                            value: $.map(this.categories, function (o) {
                                return o["id"];
                            })
                        };

                        this.filterTable();
                    }.bind(this))
                    .catch(function (error) {
                        console.log(error);
                    }.bind(this));
            }
        },
        mounted: function () {
            this.getCategories();
            this.callAjax()
        }
    }
</script>
