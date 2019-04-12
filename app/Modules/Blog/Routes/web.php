<?php

Route::group([
    'middleware' => 'web',
    'namespace' => 'App\Modules\Blog\Controllers'
], function () {

    /*
     * Public routes
     */



    /*
     * Admin routes
     */

    Route::group([
        'middleware' => ['auth', 'admin'],
    ], function () {

        Route::get('/blogs/{blog}/topics', 'BlogController@showTopics')
            ->name('blog.topics')
            ->where('blog', '[0-9]+');

        Route::get('/blogs/{blog}/add-topic', 'TopicController@addTopic')
            ->name('topic.add')
            ->where('blog', '[0-9]+');

        Route::post('/blogs/save-topic', 'TopicController@saveTopic')
            ->name('topic.save');

        Route::get('/topic/{topic}/edit', 'TopicController@editTopic')
            ->name('topic.edit')
            ->where([
                'topic' => '[0-9]+'
            ]);

        Route::get('/topic/{topic}/delete', 'TopicController@deleteTopic')
             ->name('topic.delete')
             ->where([
                 'topic' => '[0-9]+'
             ]);

    });

});