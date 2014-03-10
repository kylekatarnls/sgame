<?

JsParser

	YES = 'yes|true|on|1';
	NO = 'no|false|off|0';

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
				'#\/\/-\s*require\s*\(?\s*([\'"])(.*(?<!\\\\)(?:\\\\{2})*)\\1(?:[ \t]*,[ \t]*(' . :YES . '|' . :NO . '))?[ \t]*\)?[ \t]*(?=[\n\r])#i',
				f° $match use $coffeeFile
					$file = stripslashes($match[2]);
					$file = preg_match('#^(http|https|ftp|sftp|ftps):\/\/#', $file) ?
						$file :
						static::findFile($file);
					$isCoffee = empty($match[3]) ?
						ends_with($file, '.coffee') :
						in_array(strtolower($match[3]), explode('|', :YES));
					file_get_contents(**$file);
					if(!$isCoffee)
						$file = "`$file`";
					< $file;
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
		$jsDir = app_path() . '/../public/js/';
		foreach array($jsDir, $jsDir . 'lib/') as $dir
			foreach array('coffee', 'js') as $ext
				if file_exists($dir . $file . '.' . $ext)
					< $jsDir . $file . '.' . $ext;
		< null;