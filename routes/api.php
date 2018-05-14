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

Route::get('/callback', 'ApiController@index');
Route::post('/callback', 'ApiController@callback');


Route::get('/receive_email/{file_name}', 'ApiController@receiveEmail');


#Route::middleware('auth:api')->get('/user', function (Request $request) {
#    return $request->user();
#});
