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

Route::get('/title/station/{station}/date/{date}/bu/{bu}', 'TvGuideController@GetDataFromEPG');

Route::get('/movies/title/{title}', 'TvGuideController@GetInfoMovieByTitle');

Route::get('/movies/idTitle/{idTitle}/page/{page}/language/{language}', 'TvGuideController@GetIdRecommendationsTitle');

Route::get('/movies/id/{id}', 'TvGuideController@GetAllDetailsByIDMovie');

Route::get('/movies/genre/{genre}', 'TvGuideController@GetMoviesByGenres');


Route::get('/series/title/{title}', 'TvGuideController@GetInfoSeriesByTitle');

Route::get('/series/id/{id}', 'TvGuideController@GetAllDetailsByIDSeries');