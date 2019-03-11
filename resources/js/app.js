require('./bootstrap');

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


window.Vue = require('vue');

import Vue from 'vue';

import VueRouter from 'vue-router';

Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';

Vue.use(VueAxios, axios);
import VueLocalForage from 'vue-localforage'

Vue.use(VueLocalForage);

window.toastr = require('toastr');
require('nestable2');
window.mCustomScrollbar = require('malihu-custom-scrollbar-plugin');
require('jquery-mousewheel');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
var Paginate = require('vuejs-paginate');
Vue.component('paginate', Paginate);
Vue.component('moon-loader', require('vue-spinner/src/MoonLoader.vue').default);
Vue.component('table-vue', require('./components/TableVue/TableVue.vue').default);
Vue.component('catalog', require('./components/Catalog.vue').default);
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('cart-dropdown', require('./components/Cart/cart-dropdown.vue').default);
Vue.component('cart-button-add', require('./components/Cart/cart-button-add.vue').default);

Vue.component('my-catalog', require('./components/MyCatalog').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});
