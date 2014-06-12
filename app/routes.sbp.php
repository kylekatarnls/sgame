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
// phpinfo()

// Accueil
Route::get('/', 'HomeController@searchBar')

// Résultats
Route::post('/', 'HomeController@searchResultForm')
Route::pattern('q', '[^/]+')
Route::pattern('resultsPerPage', '[1-9][0-9]*')
Route::pattern('page', '[1-9][0-9]*')
#Route::get('/{q}', 'HomeController@searchResult')
#Route::get('/{page}/{q}/{resultsPerPage?}', 'HomeController@searchResult')

// Clic sur un lien sortant
Route::model('crawledContent', 'CrawledContent')
Route::get('/out/{q}/{crawledContent}', 'HomeController@goOut')

Route::get('/delete/{crawledContent}', 'HomeController@delete')
Route::get('/delete/confirm/{crawledContent}', 'HomeController@deleteConfirm')

// Ajout manuel d'une URL
Route::post('/add-url', 'HomeController@addUrl')->before('csrf')
Route::get('/error/wrong-token', 'HomeController@wrongToken')

// Résultats les plus populaires
Route::get('/most-popular/{page}/{resultsPerPage?}', 'HomeController@mostPopular')

// Résultats des précédentes recherches
Route::get('/history/{page}/{resultsPerPage?}', 'HomeController@history')

// Auto-complétion
Route::post('/autocomplete', f°()
	< LogSearch::startWith(Input::get('q'))
)

// URLs accessibles uniquement en environement de développement
if Config::get('app.debug')
    Route::get('/specs/1', 'DevController@specs')
    Route::get('/specs', 'DevController@specs')
    Route::get('/survey', 'DevController@survey')
    Route::post('/survey', 'DevController@postSurvey')
    Route::get('/survey/image/delete', 'DevController@deleteImage')->before('csrf')
    Route::post('/survey/image/to-be-replaced', 'DevController@imageToBeReplaced')
    Route::post('/survey/diff', 'DevController@diff')
	Route::get('/lang/csv', f°
		< Response::download(Utils\Lang\CSV::convert())
	)

//// Espace membre
// Connexion
Route::get('/user/login', 'UserController@login')
Route::post('/user/login', 'UserController@tryLogin')->before('csrf')
Route::get('/user/logout', 'UserController@logout')
// Inscription
Route::get('/user/signin', 'UserController@signin')
Route::post('/user/signin', 'UserController@trySignin')->before('csrf')
// Administration des utilisateurs
#Route::get('/user/list', 'UserController@listAll')

//// Chat
Route::get('/chat/{canal}', 'ChatController@html')->before('auth')
Route::post('/chat/{canal}', 'ChatController@html')->before(array('csrf', 'auth'))
Route::post('/chat/ajax/{canal}', 'ChatController@json')->before(array('csrf', 'auth'))

// Gestion de l'erreur 404
App::missing(f°
	$uri = trim(str_replace('/.', '/', '/' . __URI), '/')
	$controllerName = preg_replace('#[^a-zA-Z0-9_/]#', '', $uri) . 'Controller'
	$file = __DIR . '/controllers/' . $controllerName . '.php'
	if \Sbp\Sbp::fileExists($file, $path)
		include_once $path
		$class = '\\'.strtr($controllerName, '/', '\\');
		$controller = new $class
		< $controller->getOutput()
	$view = preg_replace('#[^a-zA-Z0-9._/-]#', '', $uri)
	if file_exists(dirname(__DIR . '/views/' . $view))
		$controller = new HtmlController
		< $controller->getView($view, array(
			'fromController' => false,
		))
	< BaseController::notFound()
)
