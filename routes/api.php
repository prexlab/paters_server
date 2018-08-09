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

Route::get('/masters', 'Api\MasterController@index');

Route::get('/users', 'Api\UsersController@index');
Route::post('/user/save', 'Api\UsersController@save');
Route::get('/user/{id}', 'Api\UsersController@show');


Route::get('/masters/seeder', 'Api\MasterController@seeder');
Route::get('/users/seeder', 'Api\UsersController@seeder');

Route::post('/upload', 'Api\UploadController@upload');


