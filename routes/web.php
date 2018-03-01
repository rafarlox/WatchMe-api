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

<<<<<<< HEAD
Route::get('/title/station/{station}/date/{date}/bu/{bu}', 'TvGuideController@GetGuideTv');

Route::get('/tmdb/title/{title}', 'TvGuideController@GetInfoByTMDb');
=======
Route::get('/title/station/{station}/date/{date}/bu/{bu}', 'TvGuideController@GetDataFromEPG');
>>>>>>> 59eaace5958cae1e922b812129e074af61d73193
