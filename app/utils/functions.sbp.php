<?

@f §
	$args = func_get_args()
	if isset($args[1]) && is_numeric($args[1])
		$translated = call_user_func_array('trans_choice', $args)
		if ! isset($args[4]) && $args[0] is $translated
			$translated = trans($args[0], $args[1], isset($args[2]) ? $args[2] : array(), isset($args[3]) ? $args[3] : 'messages', Language::altLang())
	else
		$translated = call_user_func_array('trans', $args)
		if isset($args[0]) && ! isset($args[3]) && $args[0] is $translated
			$translated = trans($args[0], isset($args[1]) ? $args[1] : array(), isset($args[2]) ? $args[2] : 'messages', Language::altLang())
	< $translated


@f normalize $string, $lowerCase = true
	$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ'
	$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr'
	utf8_decode(**$string)
	strtr(**$string, utf8_decode($a), $b)
	if $lowerCase
		strtolower(**$string)

	utf8_encode(**$string)
	< $string


@f array_maps $maps, array $array
	if !is_array($maps)
		$maps = explode(',', $maps)

	foreach $maps as $map
		$array = array_map($map, $array)

	< $array


@f scanUrl $url, $followLinks = false, $recursions = 0
	< Crawler::scanUrl($url, $followLinks, $recursions)


@f ip2bin $ip = null
	< bin2hex(inet_pton(is_null($ip) ? Request::getClientIp() : $ip))


@f replace $replacement, $to, $string = null
	if is_null($string)
		if !is_array($replacement)
			if !is_array($to)
				throw new InvalidArgumentException("Signatures possibles : string, string, string / array, string / array, string, string / string, array")
				< false
			< replace($to, strval($replacement))

		$string = $to
		$to = null
	if !is_null($to)
		$replacement = (array) $replacement
		$to = (array) $to
		$count = count($replacement)
		$countTo = count($to)
		if $count < $countTo
			array_slice(**$to, 0, $count)

		else if $count > $countTo
			$last = last($to)
			for $i = $countTo; $i < $count; $i++
				array_push($to, $last)

		$replacement = array_combine((array) $replacement, (array) $to)

	foreach $replacement as $from => $to
		if is_callable($to)
			$string = preg_replace_callback($from, $to, $string)

		else
			try
				// Si possible, on utilise les RegExep
				$string = preg_replace($from, $to, $string)

			catch ErrorException $e
				// Sinon on rempalcement simplement la chaîne
				$string = str_replace($from, $to, $string)

	< $string


@f accents2entities $string
	< strtr($string, array(
		'é' => '&eacute;',
		'è' => '&egrave;',
		'ê' => '&ecirc;',
		'ë' => '&euml;',
		'à' => '&agrave;',
		'ä' => '&auml;',
		'ù' => '&ugrave;',
		'û' => '&ucirc;',
		'ü' => '&uuml;',
		'ô' => '&ocirc;',
		'ò' => '&ograve;',
		'ö' => '&ouml;',
		'ï' => '&iuml;',
		'ç' => '&ccedil;',
		'ñ' => '&ntild;',
		'É' => '&Eacute;',
	))


@f utf8 $string
	$string = str_replace('Ã ', '&agrave; ', $string)
	if strpos($string, 'Ã') not false and strpos(utf8_decode($string), 'Ã') is false
		$string = utf8_decode(accents2entities($string))
	if !mb_check_encoding($string, 'UTF-8') and mb_check_encoding(utf8_encode($string), 'UTF-8')
		$string = utf8_encode(accents2entities($string))
	< $string


@f flashAlert $textKey, $type = 'danger'
	Session::flash('alert', $textKey)
	Session::flash('alert-type', $type)
	if $type is 'danger'
		Input::flash()


@f fileLastTime $file
	< max(filemtime($file), filectime($file))


@f checkAssets $state = null
	static $_state = null
	if !is_null($state)
		$_state = !!$state
	elseif is_null($_state)
		$_state = Config::get('app.debug')
	< $_state


@f addPlugin $name, $ressources = null
	< PluginManager::addPlugin($name, $ressources)


@f removePlugin $name
	< PluginManager::removePlugin($name)


@f assetRessourceName $name
	< preg_replace('#\(([^,\(\)]*)(?:,([^,\(\)]*))?\)#', Config::get('app.debug') ? '$2' : '$1', $name)


@f assetRessourceArgs $args, $extension, $inputFileFunction, $outputFileFunction = null
	$args[0] **= assetRessourceName()
	if checkAssets()
		$parser = ucfirst($extension) . 'Parser'
		if ! function_exists($inputFileFunction)
			if ! method_exists($parser, $inputFileFunction)
				$inputFileFunction .= 'File'
				$inputFileFunction = array($parser, $inputFileFunction)
		if is_null($outputFileFunction)
			$outputFileFunction = $extension . 'File'
		if ! function_exists($outputFileFunction)
			if ! method_exists($parser, $outputFileFunction)
				$outputFileFunction .= 'File'
			$outputFileFunction = array($parser, $outputFileFunction)
		$inputFile = $inputFileFunction($args[0])
		$outputFile = $outputFileFunction($args[0], $isALib)
		$time = 0
		if file_exists($inputFile)
			$time = DependancesCache::lastTime($inputFile, 'fileLastTime')
			if !file_exists($outputFile) || $time > fileLastTime($outputFile)
				(new $parser($inputFile))->out($outputFile)
			$time -= 1363188938
		$args[0] = $extension . '/' . ($isALib ? 'lib/' : '') . $args[0] . '.' . $extension . ($time ? '?' . $time : '')
	else
		$args[0] = $extension . '/' . (!file_exists(app_path() . '/../public/' . $extension . '/' . $args[0] . '.' . $extension) ? 'lib/' : '') . $args[0] . '.' . $extension
	< $args


@f assetRessourceFile $file, $extension, $inputFileFunction, $outputFileFunction = null
	$args = assetRessourceArgs(array($file), $extension, $inputFileFunction, $outputFileFunction)
	< $args[0]


@f assetRessource $args, $extension, $inputFileFunction, $outputFileFunction = null, $method = null
	if is_null($method)
		$method = $extension is 'css' ? 'style' : 'script'
	< call_user_func_array(array('HTML', $method), assetRessourceArgs($args, $extension, $inputFileFunction, $outputFileFunction))


@f style
	< assetRessource(func_get_args(), 'css', 'stylus')


@f script
	< assetRessource(func_get_args(), 'js', 'coffee')


@f image $path, $alt = null, $width = null, $height = null, $attributes = array(), $secure = null
	$time = 0
	$complete = f° $ext use &$path, &$asset, &$publicFile
		$asset .= '.' . $ext
		$publicFile .= '.' . $ext
		$path .='.' . $ext
	;
	$asset = app_path() . '/assets/images/' . $path
	$publicFile = app_path() . '/../public/img/' . $path
	if checkAssets()
		if !file_exists($asset) && !file_exists($publicFile)
			if file_exists($asset . '.png') || file_exists($publicFile . '.png')
				$complete('png')
			elseif file_exists($asset . '.jpg') || file_exists($publicFile . '.jpg')
				$complete('jpg')
			elseif file_exists($asset . '.gif') || file_exists($publicFile . '.gif')
				$complete('gif')
		if file_exists($asset)
			$time = fileLastTime($asset)
			if !file_exists($publicFile) || $time > fileLastTime($publicFile)
				copy($asset, $publicFile)
			$time -= 1363188938
	else
		if !file_exists($publicFile)
			if file_exists($publicFile . '.png')
				$complete('png')
			elseif file_exists($publicFile . '.jpg')
				$complete('jpg')
			elseif file_exists($publicFile . '.gif')
				$complete('gif')
	$image = '/img/' . $path . ($time ? '?' . $time : '')
	if ! is_null($alt) || ! is_null($width) || ! is_null($height) || $attributes !== array() || ! is_null($secure)
		if is_array($alt)
			$attributes = $alt
			$alt = null
		elseif is_array($width)
			$attributes = $width
			$width = null
		elseif is_array($height)
			$attributes = $height
			$height = null
		if ! is_null($width)
			$attributes['width'] = $width
		if ! is_null($height)
			$attributes['height'] = $height
		$image = HTML::image($image, $alt, $attributes, $secure)
	< $image


@f lang
	< Lang::locale()


@f starRate $id = '', $params = ''
	< (new StarPush($id))
		->images(StarPush::GRAY_STAR, StarPush::BLUE_STAR, StarPush::GREEN_STAR)
		->get($params)


@f array_undot $array
	$results = array();
	foreach $array as $key => $value
		$dot = strpos($key, '.')
		if $dot === false
			$results[$key] = $value
		else
			list($first, $second) = explode('.', $key, 2)
			if ! isset($results[$first])
				$results[$first] = array()
			$results[$first][$second] = $value
	< array_map(f° $value
		< is_string($value) ? $value : array_undot($value)
	, $results)


@f backUri $currentUri
	$uri = Request::server('REQUEST_URI')
	if $uri === $currentUri
		$uri = Request::server('HTTP_REFERER')
	< $uri


@f http_negotiate_language $available_languages, &$result = null
	$http_accept_language = Request::server('HTTP_ACCEPT_LANGUAGE', '')
	preg_match_all(
		"/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
		"(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
		$http_accept_language,
		$hits,
		PREG_SET_ORDER
	)
	$bestlang = $available_languages[0]
	$bestqval = 0
	foreach $hits as $arr
		$langprefix = strtolower($arr[1])
		if !empty($arr[3])
			$langrange = strtolower($arr[3])
			$language = $langprefix . "-" . $langrange

		else
			$language = $langprefix

		$qvalue = 1.0
		if !empty($arr[5])
			$qvalue = floatval($arr[5])

		if in_array($language, $available_languages) && ($qvalue > $bestqval)
			$bestlang = $language
			$bestqval = $qvalue

		else if in_array($langprefix, $available_languages) && (($qvalue*0.9) > $bestqval)
			$bestlang = $langprefix
			$bestqval = $qvalue*0.9

	< $bestlang


?>