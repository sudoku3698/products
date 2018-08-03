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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/posts', 'HomeController@posts')->name('posts');



//Export And Import Excel 
Route::get('product_import_export', 'ProductController@product_import_export')->name('product_import_export');

//export url for product database
Route::get('downloadExcel/{type}', 'ProductController@downloadExcel');

//import excel file
Route::post('importExcel', 'ProductController@importExcel');