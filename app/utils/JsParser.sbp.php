<?

JsParser

	* $coffeeFile;

	+ __construct $coffeeFile
		>coffeeFile = $coffeeFile;

	+ out $jsFile
		< file_put_contents(
			$jsFile,
			>parse(>coffeeFile)
		);

	+ parse $coffeeFile
		$code = CoffeeScript\Compiler::compile(
			preg_replace_callback(
				'#\/\/-\s*require\s*\(?\s*([\'"])(.*(?<!\\\\)(?:\\\\{2})*)\\1#',
				f° $match use $coffeeFile
					$file = stripslashes($match[2]);
					< file_get_contents(preg_match('#^(http|https|ftp|sftp|ftps):\/\/#', $file) ?
						$file :
						static::findFile($file)
					);
				,
				file_get_contents($coffeeFile)
			),
			array(
				'filename' => $coffeeFile,
				'bare' => true
			)
		);
		if !Config::get('app.debug')
			$code = preg_replace('#;(?:\\r\\n|\\r|\\n)\\h*#', ';', $code);
			$code = preg_replace('#(?:\\r\\n|\\r|\\n)\\h*#', ' ', $code);
		< $code;

	s* findFile $file
		if file_exists($file)
			< $file;
		$coffeeFile = static::coffeeFile($file);
		if file_exists($coffeeFile)
			< $coffeeFile;
		< static::jsFile($file);

	s+ coffeeFile $file
		< app_path() . '/assets/scripts/' . $file . '.coffee';

	s+ jsFile $file
		< app_path() . '/../public/js/' . $file . '.js';