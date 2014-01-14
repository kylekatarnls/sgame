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
		$choice = self::getChoice();
		Lang::setLocale($choice);
		$choice = strtr($choice, '-', '_');
		if(strpos('_', $choice) === false) {
			$secondChoice = $choice . '_' . strtoupper($choice);
			putenv('LANG=' . $secondChoice . '.UTF8');
			putenv('LANGUAGE=' . $secondChoice . '.UTF8');
		}
		else {
			$secondChoice = $choice;
			$choice = $choice . '_' . strtoupper($choice);
			putenv('LANG=' . $choice . '.UTF8');
			putenv('LANGUAGE=' . $choice . '.UTF8');
		}
		setlocale(LC_ALL, $choice . '.UTF-8', $choice . '.UTF8', $secondChoice . '.UTF-8', $secondChoice . '.UTF8');
	}
}

?>