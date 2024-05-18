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

Route::prefix('dashboard')
    ->middleware(['auth', 'role'])
    ->group(function () {
        Route::get('/', 'DashboardController@index')->name('dashboard');
    });
