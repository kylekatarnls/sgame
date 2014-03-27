<?

LanguageTest:TestCase

	/**
	 * Vérifie que la méthode Language::getChoices fonctionne
	 *
	 * @return void
	 */
	+ testGetChoices

		$languages = Language::getChoices();
		>assertTrue(is_array($languages), "Language::getChoices() doit renvoyer un array");
		>assertGreaterThan(0, count($languages), "Language::getChoices() doit renvoyer au moins 1 langue");


	/**
	 * Vérifie que la méthode Language::getChoice fonctionne
	 *
	 * @return void
	 */
	+ testGetChoice

		>assertTrue(!!preg_match('#^[a-z]{2}[a-z0-9._-]*$#i', Language::getChoice()), "Language::getChoice() doit renvoyer une langue");
