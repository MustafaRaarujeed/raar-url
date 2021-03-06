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

Route::get('/', 'UrlController@index')->name('url.get');
Route::post('/', 'UrlController@store')->name('url.post');

// Handle Redirects
Route::get('/{hash_url}', 'UrlController@redirects')->name('url.redirect');