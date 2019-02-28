<?php

Route::group([
    'middleware' => [
        'web', 'auth'
    ],
    'namespace' => 'App\Modules\Cart\Controllers'
], function () {

    Route::post('/ajax/get-cart-contents', 'CartController@ajaxGetCartContents');

    Route::post('/ajax/cart-add-item', 'CartController@ajaxAddItem');

    Route::post('/ajax/clear-cart', 'CartController@ajaxClearCart');

    Route::get('/cart/checkout', 'CartController@checkout');

    Route::post('/checkout', 'CartController@postCheckout')->name('post.checkout');

    Route::get('/empty-cart', 'CartController@emptyCart');

});