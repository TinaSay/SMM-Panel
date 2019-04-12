<?php
Route::get('/', function () {
    if(!Auth::guest()) {
        return App::call('App\Modules\Bosslike\Controllers\NewTaskController@create');
    } else {
        return view('welcome');
    }
});

Auth::routes();

Route::get('oauth/login', 'Auth\OauthController@login')->name('oauth.login');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('login', 'Auth\OauthController@login', function () {
    dd('kk');
})->name('login');

Route::get('/logout', 'Auth\LoginController@logout')->name('logout.get');

Route::get('auth/{provider}', 'Auth\SocialAuthController@redirectToProvider')->name('insta.login');
Route::get('auth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback');

Route::get('politika_konfidencialnosti', function () {
    return view('politika_konfidencialnosti');
});

Route::post('/ajax/edit-intro', function (\Illuminate\Http\Request $request) {
    $exists = DB::table('intros')->where('id', $request->pk)->first();

    if ($exists) {
        DB::table('intros')->where('id', $request->pk)->update([
            'description' => str_replace("\r\n", '<br>', $request->input('value'))
        ]);
    } else {
        DB::table('intros')->insert([
            'id' => $request->pk,
            'description' => str_replace("\r\n", '<br>', $request->input('value'))
        ]);
    }
});
