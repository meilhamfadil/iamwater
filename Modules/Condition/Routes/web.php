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

use Illuminate\Support\Facades\Route;

Route::prefix('condition')->group(function () {
    Route::get('/', 'ConditionController@index');


    Route::prefix('/api')->group(function () {
        Route::get('/nodes', 'ConditionController@nodes');
        Route::get('/histories', 'ConditionController@histories');
        Route::get('/calculate', 'ConditionController@preProcess');
        Route::get('/detail', 'ConditionController@detail');
    });

    Route::prefix('/nodes')->group(function () {
        Route::get('/', 'ConditionController@n_crud')->name('nodes-crud');
        Route::post('/datatable', 'ConditionController@n_datatable')->name('nodes-datatable');
        Route::post('/store', 'ConditionController@n_store')->name('nodes-store');
        Route::delete('/remove', 'ConditionController@n_destroy')->name('nodes-remove');
    });

    Route::prefix('/histories')->group(function () {
        Route::get('/', 'ConditionController@h_crud')->name('histories-crud');
        Route::post('/datatable', 'ConditionController@h_datatable')->name('histories-datatable');
        Route::post('/store', 'ConditionController@h_store')->name('histories-store');
        Route::delete('/remove', 'ConditionController@h_destroy')->name('histories-remove');
    });
});
