<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{path}', function () {
    return view('index');
})->where('path', '(.*)');

/*
|--------------------------------------------------------------------------
| Project Routes
|--------------------------------------------------------------------------
*/

Route::resource('projects', 'ProjectsController', ['except' => ['show']]);
Route::post('projects/search', [
    'as' => 'projects.search',
    'uses' => 'ProjectsController@search'
]);

/*
|--------------------------------------------------------------------------
| Task Routes
|--------------------------------------------------------------------------
*/

Route::resource('tasks', 'TasksController', ['except' => ['show']]);
Route::post('tasks/search', [
    'as' => 'tasks.search',
    'uses' => 'TasksController@search'
]);

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

Route::resource('users', 'UsersController', ['except' => ['show']]);
Route::post('users/search', [
    'as' => 'users.search',
    'uses' => 'UsersController@search'
]);

/*
|--------------------------------------------------------------------------
| Issue Routes
|--------------------------------------------------------------------------
*/

Route::resource('issues', 'IssuesController', ['except' => ['show']]);
Route::post('issues/search', [
    'as' => 'issues.search',
    'uses' => 'IssuesController@search'
]);
