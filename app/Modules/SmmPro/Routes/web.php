<?php

Route::group([
    'middleware' => ['web', 'auth']
], function () {

    Route::group([
        'namespace' => 'App\Modules\SmmPro\Controllers',

    ], function () {
        //dashboard

        Route::get('/cat-categories/', 'CatalogController@api')->name('index');

        Route::get('/catalog', function () {
            return view('smmpro::catalog');
        })->name('catalog');

        Route::get('/my-catalog','CatalogController@ajaxCatalog')->name('my-catalog');


        Route::get('/my-orders', 'MyOrdersController@index')->name('my-orders');
        Route::post('/order', 'OrdersController@api')->name('order.store');
        Route::get('/deposit', 'DepositController@index')->name('deposit');

    });
    Route::group([
        'namespace' => 'App\Modules\SmmPro\Controllers\Admin',

    ], function () {
        //admin panel
        Route::get('users', 'UsersController@index')->name('users');

        //categories
        Route::get('categories', 'CategoriesController@index')->name('categories.index');
        Route::get('category/create', 'CategoriesController@create')->name('category.create');
        Route::post('category/store', 'CategoriesController@store')->name('category.store');
        Route::get('category/edit/{id}', 'CategoriesController@edit')->name('category.edit');
        Route::post('category/update/{id}', 'CategoriesController@update')->name('category.update');
        Route::delete('category/destroy/{id}', 'CategoriesController@destroy')->name('category.destroy');

        //tree
        Route::post('/rebuild-tree', 'CategoriesController@ajaxRebuildTree')
            ->name('taxonomy.rebuild-tree');

        Route::post('/ajax/get-descendants', 'CategoriesController@ajaxGetDescendants');
        Route::post('/ajax/get-ancestors', 'CategoriesController@ajaxGetAncestors');
        Route::post('/ajax/get-services', 'ServicesController@ajaxGetServices')->name('ajax.get-services');
        Route::post('/ajax/get-categories', 'CategoriesController@ajaxGetCategories');
        Route::post('/ajax/get-current', 'CategoriesController@ajaxGetCurrent');

        //services
        Route::get('services', 'ServicesController@index')->name('services.index');
        Route::get('service/create', 'ServicesController@create')->name('service.create');
        Route::post('service/store', 'ServicesController@store')->name('service.store');
        Route::get('service/edit/{id}', 'ServicesController@edit')->name('service.edit');
        Route::post('service/update/{id}', 'ServicesController@update')->name('service.update');
        Route::any('service/destroy/{id}', 'ServicesController@destroy')->name('service.destroy');
        Route::get('service/duplicate/{id}', 'ServicesController@duplicate')->name('service.duplicate');

        //orders
        Route::get('orders', 'OrdersController@index')->name('orders.index');
        Route::get('order/edit/{id}', 'OrdersController@edit')->name('order.edit');
        Route::post('order/update/{id}', 'OrdersController@update')->name('order.update');
        Route::delete('order/destroy/{id}', 'OrdersController@destroy')->name('order.destroy');

    });

});
