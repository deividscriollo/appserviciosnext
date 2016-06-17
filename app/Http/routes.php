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
    return view('welcome');
});

Route::group(['middleware' => 'cors'], function(){
    Route::get('getDatos','datosController@getDatos');
    Route::post('cosultarMovil','datosController@consultar_Movil');
    Route::post('estado_factura','datosController@estado_fac_electronica');
});
