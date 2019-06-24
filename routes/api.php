<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::get('test', 'Api\TestController@tasks');

    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

/*
|--------------------------------------------------------------------------
| Project API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/projects', 'Api\ProjectsController', ['as' => 'api']);
});

/*
|--------------------------------------------------------------------------
| Task API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/tasks', 'Api\TasksController', ['as' => 'api']);
});

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/users', 'Api\UsersController', ['as' => 'api']);
});

/*
|--------------------------------------------------------------------------
| Issue API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/issues', 'Api\IssuesController', ['as' => 'api']);
});

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('v1/logs', 'Api\TasksController@logs', ['as' => 'api']);
});

/*
|--------------------------------------------------------------------------
| File API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/files', 'Api\FilesController', ['as' => 'api']);
    Route::post('v1/download', 'Api\MediaController@download', ['as' => 'api']);
});