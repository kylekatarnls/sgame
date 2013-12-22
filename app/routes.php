<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Pour vérifier votre configuration, décommenter la ligne ci-dessous :
// phpinfo();exit;

// Accueil
Route::get('/', 'HomeController@searchBar');

// Résultats
Route::post('/', 'HomeController@searchResult');
Route::pattern('q', '[^/]*');
Route::pattern('page', '[1-9][0-9]*');
Route::get('/{q}', 'HomeController@searchResult');
Route::get('/{page}/{q}', 'HomeController@searchResult');
Route::get('/{page}/{resultsPerPage}/{q}', 'HomeController@searchResult')
	->where('resultsPerPage', '[1-9][0-9]*');