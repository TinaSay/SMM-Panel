<?php
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('oauth/login', 'Auth\OauthController@login')->name('oauth.login');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('login', 'Auth\LoginController@showLoginForm', function () {
    dd('kk');
})->name('login');

Route::get('/logout', 'Auth\LoginController@logout')->name('logout.get');

Route::get('auth/{provider}', 'Auth\SocialAuthController@redirectToProvider')->name('insta.login');
Route::get('auth/{provider}/callback', 'Auth\SocialAuthController@handleProviderCallback');

Route::get('politika_konfidencialnosti', function () {
    return view('politika_konfidencialnosti');
});

