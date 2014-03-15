<?

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
Route::post('/', 'HomeController@searchResultForm');
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

// Résultats des précédentes recherches
Route::get('/history/{page}/{resultsPerPage?}', 'HomeController@history');

// Auto-complétion
Route::post('/autocomplete', f°()
	<LogSearch::startWith(Input::get('q'));
);

// URLs accessibles uniquement en environement de développement
if Config::get('app.debug')
    Route::get('/specs/1', 'DevController@specs');
	Route::get('/lang/csv', f°
		< Response::download(Utils\Lang\CSV::convert());
	);

// Gestion de l'erreur 404
App::missing(f°()
	<BaseController::notFound();
);