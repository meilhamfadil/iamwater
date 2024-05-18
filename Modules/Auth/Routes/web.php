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

Route::prefix('auth')
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('login');
        })->middleware(['guest']);

        Route::prefix('/login')
            ->middleware(['guest'])
            ->group(function () {
                Route::get('/', 'LoginController@index')->name('login');
                Route::post('/check', 'LoginController@authenticate')->name('authenticate');
                Route::post('/forgot', 'LoginController@forgot')->name('forgot');
            });

        Route::get('/logout', 'LoginController@logout')->name('logout');
    });
