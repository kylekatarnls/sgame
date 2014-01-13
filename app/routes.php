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
Route::pattern('q', '[^/]+');
Route::pattern('resultsPerPage', '[1-9][0-9]*');
Route::pattern('page', '[1-9][0-9]*');
Route::get('/{q}', 'HomeController@searchResult');
Route::get('/{page}/{q}/{resultsPerPage?}', 'HomeController@searchResult');

// Clic sur un lien sortant
//Route::model('id', 'CrawledContent');
Route::pattern('id', '[1-9][0-9]*');
Route::get('/out/{q}/{id}', 'HomeController@goOut');

// Ajout manuel d'une URL
Route::post('/add-url', 'HomeController@addUrl');

// Résultats les plus populaires
Route::get('/most-popular/{page}/{resultsPerPage?}', 'HomeController@mostPopular');

// Auto-complétion
Route::post('/autocomplete', function ()
{
	return LogSearch::startWith(Input::get('q'));
});

// Gestion de l'erreur 404
App::missing(function ()
{
	return View::make('errors.notFound');
});
