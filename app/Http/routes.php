<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('home', [
        'step' => session('step', 0)
    ]);
});
Route::post('/command', 'CubeController@command');
Route::post('/reset', 'CubeController@reset');
