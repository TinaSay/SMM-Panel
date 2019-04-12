<template>
    <div class="dynamic-component vue-table-component">
        <div class="vt-wrapper mt-4 mb-4">
            <div class="vt-controls-wrapper controls-top">
                <div class="columns">
                    <div class="row">
                        <div class="col-12 vt-controls">
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
                    </div>
                    
                    <div class="row mt-3 mb-2">
                        <div class="col-12 vt-controls">
                            <div class="row">
                                <div class="col">
                                    <select name="root-category" id="root-category" class="form-control"
                                            v-model="rootCategory" @change="loadDescendants">
                                        <option selected disabled value="0">-- Выберите корневую категорию --</option>
                                        <option v-for="category in rootCategories" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select name="category" id="category" class="form-control" v-model="category"
                                            @change="filterTable" :disabled="!categories.length">
                                        <option selected disabled value>-- Выберите категорию --</option>
                                        <option value="0">Все</option>
                                        <option v-for="category in categories" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col vt-controls">
                                    <div class="vt-has-icons">
                                        <input type="text" class="form-control" v-model="search" v-on:keyup="filterTable"
                                               placeholder="Поиск">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table :id="tableId" class="table table-services">
                    <thead>
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

                    <!-- TBODY -->
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
                    <tbody v-else class="vt--loading">
                    <tr>
                        <td :colspan="columns.length" class="text-center">
                            <moon-loader :loading="loading" :color="'#3490dc'" :size="'60px'"></moon-loader>
                        </td>
                    </tr>
                    </tbody>
                    <!-- /TBODY -->

                    <tfoot>
                    <tr>
                        <th v-for="(column, i) in columns" :style="{width: columnWidth(column)}" :class="column.className">
                            {{ column.name ? column.name : null }}
                        </th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="vt-controls-wrapper controls-bottom">
                <div class="columns">
                    <div class="col vt-controls">
                    <span class="rows-info" v-if="rows.length">
                        Показано записей с {{ (page - 1) * limit + 1}} по {{ (page - 1) * limit + rows.length }} из {{ total }}
                    </span>
                    </div>
                    <div class="col vt-controls text-right">
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
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                categories: [],
                category: 0,
                filter: {},
                limit: 25,
                loading: false,
                rootCategories: [],
                rootCategory: 0,
                rows: [],
                page: 1,
                pages: 0,
                search: '',
                sorting: this.initialSorting,
                total: 0,
                totalFiltered: 0
            };
        },
        methods: {
            changePage(page) {
                this.page = page;

                this.loadRows();
            },
            columnWidth: function (column) {
                return column.width ? column.width + 'px' : false;
            },
            filterTable: function () {
                if (this.category && this.category > 0) {
                    this.$cookies.set('category', this.category, 60 * 60 * 12);

                    this.filter = {
                        type: 'category',
                        value: [this.category]
                    };
                } else {
                    this.$cookies.remove('category');

                    this.filter = {
                        type: 'category',
                        value: $.map(this.categories, function (o) {
                            return o["id"];
                        })
                    };
                }

                this.$cookies.set('filter', this.filter, 60 * 60 * 12);

                this.loadRows();
            },
            loadCategories: function () {
                axios.post('/ajax/get-categories')
                .then(function (response) {
                    this.rootCategories = response.data.rootCategories;
                }.bind(this))
                .catch(function (error) {
                    console.log(error);
                }.bind(this));
            },
            loadDescendants: function () {
                this.$cookies.set('rootCategory', this.rootCategory, 60 * 60 * 12);
                this.$cookies.remove('category');
                this.$cookies.remove('filter');

                axios.post('/ajax/get-descendants', {
                    root: this.rootCategory
                })
                .then(function (response) {
                    this.categories = response.data.categories;

                    this.$cookies.set('categories', JSON.stringify(this.categories), 60 * 60 * 12);

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
            },
            loadRows: function () {
                this.loading = true;

                axios.post(this.url, {
                    limit: this.limit,
                    search: this.search,
                    page: this.page,
                    filter: this.filter
                }).then(function (response) {
                    this.total = response.data.total;
                    this.totalFiltered = response.data.totalFiltered;
                    this.rows = response.data.rows;
                    this.pages = Math.ceil(this.total / this.limit);

                    this.loading = false;
                }.bind(this)).catch(function (error) {
                    console.log(error.response);
                    this.loading = false;
                }.bind(this));
            },
            setLimit(number) {
                this.limit = number;
                this.loadRows();
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
                }

                this.loadRows();
            }
        },
        mounted: function () {
            this.rootCategory = this.$cookies.get('rootCategory') || 0;
            this.category = this.$cookies.get('category') || 0;
            this.categories = JSON.parse(this.$cookies.get('categories')) || [];
            this.filter = this.$cookies.get('filter') || {};

            this.loadCategories();

            this.loadRows();
        },
        props: {
            columns: {
                required: true,
                type: Array
            },
            initialSorting: {
                required: false,
                type: Object,
                default: function () {
                    return {
                        column: 'id',
                        order: 'desc'
                    };
                }
            },
            tableId: {
                required: false,
                type: String,
                default: 'vt-table'
            },
            url: {
                required: true,
                type: String
            }
        }
    }
</script>

<style>
    .v-spinner > .v-moon {
        margin: 0 auto;
    }
    .btn-group-xs > .btn, .btn-xs {
        padding: .25rem .4rem;
        font-size: .755rem;
        line-height: .5;
        border-radius: .2rem;
    }
</style>