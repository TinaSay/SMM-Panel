<?php

Route::group([
    'middleware' => ['web', 'auth']
], function () {

    Route::group([
        'namespace' => 'App\Modules\Instashop\Controllers',

    ], function () {

        Route::get('instashop-home', 'InstaShopController@home')->name('insta.home');
        Route::get('instashop/create', 'InstaShopController@create')->name('insta.create');

        Route::get('search/tags', 'InstaShopController@tags')->name('insta.tags');
        Route::get('search/posts', 'InstaShopController@posts')->name('insta.posts');
        Route::get('search/full', 'InstaShopController@full')->name('insta.full');


    });
});