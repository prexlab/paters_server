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


Route::get('/', 'EtcController@index');

Route::get('privacy', 'EtcController@privacy');

Route::get('contact', 'EtcController@contact');
Route::post('contact', 'EtcController@contact');
