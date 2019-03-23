<?php

Route::group([
    'middleware' => ['web', 'auth']
], function () {

    Route::group([
        'namespace' => 'App\Modules\Bosslike\Controllers',

    ], function () {
        Route::get('profile', 'ProfileController@index')->name('profile');
        Route::get('session/{currentUser}', 'ApiController@addToSession');

        Route::delete('ok-user/delete/{id}', 'OkController@delete')->name('ok-user.delete');

        Route::delete('social/user/delete/{id}', 'ProfileController@deAuth')->name('deauth');

        Route::get('telegram', 'ProfileController@telegram')->name('telegram');

        Route::get('twitter/login', 'ProfileController@twitter_login')->name('twitter.login');
        Route::get('twitter/callback', 'ProfileController@twitter_callback')->name('twitter.callback');

        Route::get('task/new', 'NewTaskController@create')->name('task.create');
        Route::get('task/speed/{social}/{service}', 'NewTaskController@getTaskSpeed')->name('task.speed');
        Route::get('task/new/services/{socialId}', 'NewTaskController@getServicesAjax');
        Route::post('task/store', 'NewTaskController@store')->name('task.store');
        Route::get('task/show/{id}', 'TasksController@show')->name('task.show');
        Route::get('task/hide/{id}', 'TasksController@hide')->name('task.hide');

        Route::get('tasks/my', 'MyTasksController@index')->name('tasks.my');
        Route::get('tasks/all', 'TasksController@index')->name('tasks.all');
        Route::get('tasks/check/{id}', 'TasksController@check')->name('tasks.check');
        Route::put('task/update/{id}', 'MyTasksController@updateAjax');
        Route::post('task/delete/{id}', 'MyTasksController@delete')->name('task.delete');

        Route::get('balance', 'ApiController@getFormattedBalance');
        Route::get('raw-balance', 'ApiController@getUserBalance');
        Route::get('profile/check/{id}', 'ProfileController@checkProfile')->name('profile.check');
        Route::get('profile/history', 'ProfileController@history')->name('profile.history');

        Route::get('info', 'MyInfoController@addInfo');
        Route::post('info/save', 'MyInfoController@store');
        Route::get('info/edit', 'MyInfoController@edit');
        Route::post('info/update/{id}', 'MyInfoController@update')->name('info.update');

        Route::get('tasks-done', 'MyTasksController@countDone');
        Route::get('storage-link',function(){
            Artisan::call('storage:link');
        });

    });
});
