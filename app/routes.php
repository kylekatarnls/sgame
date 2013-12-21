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

Route::get('/', 'HomeController@searchBar');
Route::get('/{page}/{q}', 'HomeController@searchBar');
Route::post('/', 'HomeController@searchResult');