<?php

Route::group([
    'middleware' => ['web', 'auth'],
    'namespace' => 'App\Modules\Support\Controllers'
], function () {

    Route::get('help', 'WebController@showSupportForm')
        ->name('help');

    Route::post('help/save', 'WebController@saveFeedback')
        ->name('help.save');

    Route::group([
        'middleware' => 'admin'
    ], function () {

        Route::get('/help/admin', 'WebController@showFeedback')
            ->name('help.admin');

        Route::get('/screenshot/{id}', 'WebController@showDetail');

    });

});
