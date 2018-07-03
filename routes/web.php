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
Route::pattern('id', '\d+');
Route::pattern('slug', '[\w-]+');
Route::pattern('unique_id', '\w+');
Route::pattern('search_query', '[\w-]+');

Route::namespace ('CSN')->as('main.')->group(function () {
    Route::get('/bai-hat/{slug}-{id}.html', 'SongController@index')->name('bai-hat');
    Route::get('/tim-kiem/{search_query}', 'SearchController@search')->name('tim-kiem');
});
