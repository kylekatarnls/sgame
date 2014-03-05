<?php

ini_set('xdebug.max_nesting_level', 2000);

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/models/tools',
	app_path().'/database/seeds',
	app_path().'/utils',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/
use Whoops\Handler\Handler;

// Use the Laravel IoC to get the Whoops\Run instance, if whoops
// is available (which will be the case, by default, in the dev
// environment)
/*
if(App::bound("whoops"))
{
	ob_start(function ($content)
	{
		$content = preg_replace_callback(
			'~'.preg_quote(app_path().DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'sbp'.DIRECTORY_SEPARATOR, '~').'[0-9a-fA-F]+\.php~',
			function ($match)
			{
				return \sbp\laravel\ClassLoader::sbpFromFile($match[0]);
			},
			$content
		);
		$content = preg_replace_callback(
			'~'.preg_quote(DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'sbp'.DIRECTORY_SEPARATOR, '~').'[0-9a-fA-F]+\.php~',
			function ($match)
			{
				return \sbp\laravel\ClassLoader::sbpFromFile($match[0]);
			},
			$content
		);
		return $content;
	});
}
//*/

App::error(function(Exception $exception, $code)
{
	/*
	if(\sbp\laravel\ClassLoader::sbpIsRunning())
	{
		$static = get_class($exception);
		$exception = new $static(
			"Error " . get_class($exception) .
			" in " . \sbp\laravel\ClassLoader::lastSbpFile() . ":" .
			$exception->getLine() . " : " .
			$exception->getMessage()
		);
		Log::error($exception);
		(new \Illuminate\Exception\WhoopsDisplayer(new \Whoops\Run, false))->display($exception);
		exit;
		return '<pre>'.$exception.'</pre>';
	}
	//*/
	Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

sbp_include(app_path().'/filters.php');

