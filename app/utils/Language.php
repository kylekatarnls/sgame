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

	static public function setLocale()
	{
		Lang::setLocale(self::getChoice());
	}
}

?>