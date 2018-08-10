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



//Export and Import Excel 
Route::get('product_import_export', 'ProductController@product_import_export')->name('product_import_export');

//Export Excel File for Product database
Route::get('downloadExcel/{type}', 'ProductController@downloadExcel');

//Import Excel file
Route::post('importExcel', 'ProductController@importExcel');

Route::get('test_importExcel','ProductController@test_importExcel')->name('test_importExcel');

Route::get('/foo', function () {
    $exitCode = Artisan::call('import:products');
    print_r($exitCode);
});