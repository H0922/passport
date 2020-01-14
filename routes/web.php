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
Route::get('/phpinfo', function () {
    phpinfo();
});
Route::post('pass/reg','Passport\PassController@reg');
Route::post('pass/login','Passport\PassController@login');
Route::get('pass/Userinfo','Passport\PassController@Userinfo')->middleware('token');
Route::post('gitpush','Passport\PassController@gitpush');
Route::get('token','Passport\PassController@token')->middleware('token');



