<?

JsParser

	YES = 'yes|true|on|1'
	NO = 'no|false|off|0'
	// RAW_JS_REQUIRE = "`try{{code}\n}catch(e){ if(window['console']) console.error(e) }`"
	RAW_JS_REQUIRE = "`{code}\n`"

	* $coffeeFile

	+ __construct $coffeeFile
		>coffeeFile = $coffeeFile

	+ out $jsFile
		< file_put_contents(
			$jsFile,
			>parse(>coffeeFile)
		)

	s+ resolveRequire $coffeeFile, $firstFile = null, $jsOnly = false
		if is_null($firstFile)
			$firstFile = $coffeeFile
		< replace(file_get_contents($coffeeFile), array(
			'#(?<=^|[\n\r])([ \t]*)\/\/-\s*require\s*plugins[ \t]*(?=[\r\n]|$)#'
				=> f° $match
					$require = $match[1] . '//- require '
					< $require . implode("\n" . $require, array_map('json_encode', PluginManager::getScripts()))
				,
			'#(?<=^|[\n\r])([ \t]*)\/\/-\s*require\s*\(?\s*([\'"])(.*(?<!\\\\)(?:\\\\{2})*)\\2(?:[ \t]*,[ \t]*(' . :YES . '|' . :NO . '))?[ \t]*\)?[ \t]*(?=[\r\n]|$)#i'
				=> f° $match use $coffeeFile, $firstFile, $jsOnly
					list($all, $indent, $quote, $file) = $match;
					$file **= stripslashes()
					$file **= assetRessourceName()
					$file = preg_match('#^(http|https|ftp|sftp|ftps):\/\/#', $file) ?
						$file :
						static::findFile($file)
					$isCoffee = empty($match[4]) ?
						ends_with($file, '.coffee') :
						in_array(strtolower($match[4]), explode('|', :YES))
					DependancesCache::add($firstFile, $file)
					$code = str_replace(array("\r\n", "\r"), "\n", "\n" . ($isCoffee not $jsOnly ?
						static::resolveRequire($file, $firstFile) :
						str_replace('{code}', static::resolveRequire($file, $firstFile, true), :RAW_JS_REQUIRE)
					))
					< $indent . str_replace("\n", "\n" . $indent, $code)
				,
		))

	+ parse $coffeeFile
		DependancesCache::flush($coffeeFile)
		try
			$code = CoffeeScript\Compiler::compile(
				$precompiled = static::resolveRequire($coffeeFile), {
					filename = $coffeeFile
					bare = true
				}
			)
		catch CoffeeScript\Error $e
			file_put_contents(__DIR . '/../storage/logs/last-coffee-file-in-error.coffee', $precompiled)
			throw $e
		if ! Config::get('app.debug')
			$code = preg_replace('#;(?:\\r\\n|\\r|\\n)\\h*#', ';', $code)
			$code = preg_replace('#(?:\\r\\n|\\r|\\n)\\h*#', ' ', $code)
		< $code

	s* findFile $file
		if file_exists($file)
			< $file;
		$coffeeFile = static::coffeeFile($file)
		if file_exists($coffeeFile)
			< $coffeeFile
		< static::jsFile($file)

	s+ coffeeFile $file, &$isALib = null
		$files = array(
			app_path() . '/assets/scripts/' . $file . '.coffee',
			app_path() . '/../public/js/lib/' . $file . '.coffee',
		)
		foreach $files as $iFile
			if file_exists($iFile)
				$isALib = str_contains($iFile, 'lib/')
				< $iFile
		< array_get($files, 0)

	s+ jsFile $file, &$isALib = null
		$jsDir = app_path() . '/../public/js/'
		foreach array($jsDir, $jsDir . 'lib/') as $dir
			foreach array('coffee', 'js') as $ext
				if file_exists($dir . $file . '.' . $ext)
					$isALib = ends_with($dir, 'lib/')
					< $dir . $file . '.js'
		< app_path() . '/../public/js/' . $file . '.js'