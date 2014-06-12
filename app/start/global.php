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
	app_path().'/utils/git',

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

if(method_exists($app['whoops.handler'], 'setResourcesPath'))
{
	$app['whoops.handler']->setResourcesPath(app_path() . '/utils/exception/resources');
}

if(App::bound("whoops"))
{
	ob_start(function ($content)
	{
		return defined('WHOOPS_PRETTY_TEMPLATE_CALLED') ?
			str_replace("\\n", "\n", preg_replace('#^<!DOCTYPE\shtml>.+<!DOCTYPE\shtml>#', '<!DOCTYPE html>', str_replace("\n", "\\n", $content))) :
			$content;
	});
}

App::error(function(Exception $exception, $code)
{
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

if(! defined('HOLOGAME_NAMESPACE'))
{
	define('HOLOGAME_EXCLUDE_FILES', 'translation');
	define('HOLOGAME_NAMESPACE', 'Hologame');
	include_once __DIR__ . '/../utils/hologame/functions.php';
	include_once __DIR__ . '/../utils/hologame/constants.php';
	spl_autoload_register(function ($class)
	{
		if(strpos($class, HOLOGAME_NAMESPACE . '\\') === 0 || strpos($class, HOLOGAME_NAMESPACE . 'Hologame°') === 0)
		{
			$dir = __DIR__ . '/../utils/hologame/class/';
			$class = str_replace(['\\', '°'], '/', substr($class, strlen(HOLOGAME_NAMESPACE)));
			$file = strtolower(ltrim($class, '/'));
			if(file_exists($dir . $file . '.php'))
			{
				include_once $dir . $file . '.php';
			}
			elseif(file_exists($dir . $file . '/index.php'))
			{
				include_once $dir . $file . '/index.php';
			}
		}
	});
	// \Hologame\Javascript::wrap('plugins(function () {', '});');
}
sbp_add_plugin('jQuery', [
	'#\$' . \Sbp\Sbp::PARENTHESES . '#' => '(new \\Hologame\\Jquery°Call$1)',
	'$->' => '(new \\Hologame\\Jquery)->',
]);
sbp_include_once(app_path().'/filters.php');

