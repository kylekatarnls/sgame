<?

TranslatableDateTimeTest:TestCase

	/**
	 * Vérifie que la classe TranslatableDateTime->recentDate fonctionne
	 *
	 * @return void
	 */
	+ testRecentDate

		Language::setLocale('en');
		>assertTrue((new TranslatableDateTime)->recentDate is 'today', "recentDate doit retourner 'today' en anglais");
		Language::setLocale('fr');
		>assertTrue((new TranslatableDateTime)->recentDate is "aujourd'hui", "recentDate doit retourner 'aujourd'hui' en français");


	/**
	 * Vérifie que la classe TranslatableDateTime->recentTime fonctionne
	 *
	 * @return void
	 */
	+ testRecentTime

		Language::setLocale('en');
		>assertTrue(str_is('*ago', (new TranslatableDateTime)->recentTime), "recentTime doit se terminer par 'ago' en anglais");
		Language::setLocale('fr');
		>assertTrue(str_is('il y a*', (new TranslatableDateTime)->recentTime), "recentTime doit commencer par 'il y a' en français");
