<?php

Route::group([
    'middleware' => ['web', 'auth']
], function () {

    Route::group([
        'namespace' => 'App\Modules\Bosslike\Controllers',

    ], function () {
        Route::get('profile', 'ProfileController@index')->name('profile');

    });
});
