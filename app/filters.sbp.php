<?php

/*
|--------------------------------------------------------------------------
| Chargement des fonctions supplémentaires
|--------------------------------------------------------------------------
*/

sbp_include_once(app_path() . '/utils/functions.php')


/*
|--------------------------------------------------------------------------
| Configuration de SBP
|--------------------------------------------------------------------------
*/

@f __sbp_in $needle, $haystack = null
	if func_num_args() is 1
		< array_search(true, $neelde)
	< is_array($haystack) ?
		in_array($needle, $haystack) :
		strpos($haystack, $needle) !== false


/*
|--------------------------------------------------------------------------
| N'autoriser que les domaines de confiance si la config le précise
|--------------------------------------------------------------------------
*/

if($trustedHosts = Config::get('app.trusted'))
	Request::setTrustedHosts($trustedHosts)


/*
|--------------------------------------------------------------------------
| Calcul de la langue à utiliser pour l'affichage des textes
|--------------------------------------------------------------------------
*/

Language::setLocale()


/*
|--------------------------------------------------------------------------
| Calcul du nombre de résultats par page et initialisation de la pagination
|--------------------------------------------------------------------------
*/

ResultsPerPage::init()


/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(f° $request
	addPlugin('jquery', 'jquery-1.11.0.min')
	addPlugin('bootstrap', array( 'bootstrap.min.css', 'bootstrap.min'))
	addPlugin('underscore')
	addPlugin('modernizr')
	Blade::extend(f° $value, $compiler
		< replace(
			array(
				'/(?<=\s)@switch\((.*)\)(\s*)@case\((.*)\)(?=\s)/' => '<?php switch($1):$2case $3: ?>',
				'/(?<=\s)@endswitch(?=\s)/' => '<?php endswitch; ?>',
				'/(?<=\s)@case\((.*)\)(?=\s)/' => '<?php case $1: ?>',
				'/(?<=\s)@default(?=\s)/' => '<?php default: ?>',
				'/(?<=\s)@break(?=\s)/' => '<?php break; ?>',
			),
			$value
			// Credit : https://github.com/francescomalatesta
		)
	)
	header_remove('Server')
)


App::after(f° $request, $response
	//
)

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', f°()
	if (Auth::guest())
		<Redirect::guest('/user/login')
)


Route::filter('auth.basic', f°()
	<Auth::basic()
)

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', f°()
	if (Auth::check())
		<Redirect::to('/')
)

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', f°()
	if (Session::token() != Input::get('_token'))
		//throw new Illuminate\Session\TokenMismatchException;
		<Redirect::to('/error/wrong-token')
)


/*
|--------------------------------------------------------------------------
| Observateurs d'événements
|--------------------------------------------------------------------------
|
| Les observateurs permettent d'exécuter des actions à chaque fois qu'un
| événement survient.
|
| Par exemple, la méthode CrawledContentObserver::saved() est exécutée à
| chaque fois qu'un objet CrawledContent est créé ou modifié en base de
| données.
|
*/

CrawledContent::observe(new CrawledContentObserver)
Message::observe(new MessageObserver)
