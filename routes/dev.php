<?php

Route::group(['prefix' => 'dev'], function () {

    Route::get('test/{method}', 'Dev\TestController@main');

});