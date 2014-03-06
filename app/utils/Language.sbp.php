<?

Language

	/*
	|--------------------------------------------------------------------------
	| Liste des langues (disponible sur toutes les vues)
	|--------------------------------------------------------------------------
	*/

	s- $languages = array(
		'en' => 'English',
		'fr' => 'Français',
		'tr' => 'Türkçe',
	);

	s- $locales = array(
		'en' => array('GB', 'US'),
		'fr' => array('FR', 'BE', 'CA'),
	);

	s+ getChoices
		< self::$languages;

	s+ getChoice
		if Input::has('language')
			$language = Input::get('language');
			if isset(self::$languages[$language])
				Cookie::queue('language', $language, 144000);
			else
				unset($language);

		if !isset($language)
			$language = Cookie::get('language', http_negotiate_language(array_keys(self::$languages)));

		< $language;

	s+ setLocale $choice = null
		if is_null($choice)
			$choice = self::getChoice();
		Lang::setLocale($choice);
		$choice **= strtr('-', '_');
		$list = array($choice);
		$underscore = strpos('_', $choice);
		if $underscore === false
			if isset(self::$locales[$choice])
				foreach self::$locales[$choice] as $country
					$list[] = $choice . '_' . $country;
			else
				$list[1] = $choice . '_' . strtoupper($choice);
			putenv('LANG=' . $list[1] . '.UTF8');
			putenv('LANGUAGE=' . $list[1] . '.UTF8');
		else
			$list[1] = substr($choice, $underscore);
			putenv('LANG=' . $choice . '.UTF8');
			putenv('LANGUAGE=' . $choice . '.UTF8');
		call_user_func_array('setlocale',
			array(LC_ALL) +
			array_map(f° $choice { < $choice . '.UTF8'; }, $list) +
			array_map(f° $choice { < $choice . '.UTF-8'; }, $list) +
			$list
		);

		// On rend la liste des langues accessible dans toutes les vues
		View::share('languages', self::getChoices());

?>