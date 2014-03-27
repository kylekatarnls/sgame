<?

TranslatableDateTimeTest:TestCase

	/**
	 * Vérifie que la classe TranslatableDateTime->recentDate fonctionne
	 *
	 * @return void
	 */
	+ testRecentDate

		Language::setLocale('en');
		(new TranslatableDateTime)->recentDate should be 'today';
		Language::setLocale('fr');
		(new TranslatableDateTime)->recentDate should be "aujourd'hui";


	/**
	 * Vérifie que la classe TranslatableDateTime->recentTime fonctionne
	 *
	 * @return void
	 */
	+ testRecentTime

		Language::setLocale('en');
		should str_is('*ago', (new TranslatableDateTime)->recentTime), "recentTime doit se terminer par 'ago' en anglais";
		Language::setLocale('fr');
		should str_is('il y a*', (new TranslatableDateTime)->recentTime), "recentTime doit commencer par 'il y a' en français";
