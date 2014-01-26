<?php

class Language {

	/*
	|--------------------------------------------------------------------------
	| Liste des langues (disponible sur toutes les vues)
	|--------------------------------------------------------------------------
	*/

	static private $languages = array(
		'en' => 'English',
		'fr' => 'Français'
	);

	static private $locales = array(
		'en' => array('GB', 'US'),
		'fr' => array('FR', 'BE', 'CA')
	);

	static public function getChoices()
	{
		return self::$languages;
	}

	static public function getChoice()
	{
		if(Input::has('language'))
		{
			$language = Input::get('language');
			if(isset(self::$languages[$language]))
			{
				Cookie::queue('language', $language, 144000);
			}
			else
			{
				unset($language);
			}
		}

		if(!isset($language))
		{
			$language = Cookie::get('language', http_negotiate_language(array_keys(self::$languages)));
		}

		return $language;
	}

	static public function setLocale($choice = null)
	{
		if(is_null($choice))
		{
			$choice = self::getChoice();
		}
		Lang::setLocale($choice);
		$choice = strtr($choice, '-', '_');
		$list = array($choice);
		$underscore = strpos('_', $choice);
		if($underscore === false)
		{
			if(isset(self::$locales[$choice]))
			{
				foreach(self::$locales[$choice] as $country)
				{
					$list[] = $choice . '_' . $country;
				}
			}
			else
			{
				$list[1] = $choice . '_' . strtoupper($choice);
			}
			putenv('LANG=' . $list[1] . '.UTF8');
			putenv('LANGUAGE=' . $list[1] . '.UTF8');
		}
		else
		{
			$list[1] = substr($choice, $underscore);
			putenv('LANG=' . $choice . '.UTF8');
			putenv('LANGUAGE=' . $choice . '.UTF8');
		}
		call_user_func_array('setlocale',
			array(LC_ALL) +
			array_map(function ($choice) { return $choice . '.UTF8'; }, $list) +
			array_map(function ($choice) { return $choice . '.UTF-8'; }, $list) +
			$list
		);

		// On rend la liste des langues accessible dans toutes les vues
		View::share('languages', self::getChoices());
	}
}

?>