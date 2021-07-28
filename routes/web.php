<?php

use Illuminate\Support\Facades\Route;

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


Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/', 'OrdersController@index')->name('home');
    Route::get('/upload', 'UploadController@index')->name('upload.index');
    Route::post('/upload', 'UploadController@store')->name('upload.store');
    Route::get('/orders', 'OrdersController@index')->name('orders.index');
    Route::get('/items', 'ItemsController@index')->name('items.index');
    Route::get('/customers', 'CustomersController@index')->name('customers.index');
    Route::get('/sales', 'SalesController@index')->name('sales.index');
});



