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
Route::get('/test', 'HomeController@test')->name('test');

Route::get('/procult', 'HomeController@procult')->name('procult');
Route::get('/progard', 'HomeController@progard')->name('progard');
Route::get('/orga', 'HomeController@orga')->name('orga');
Route::get('/admin', 'HomeController@admin')->name('admin');