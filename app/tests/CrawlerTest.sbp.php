<?

CrawlerTest:TestCase

	/**
	 * Vérifie que le crawler fonctionne
	 *
	 * @return void
	 */
	+ testCrawler()

		>assertTrue(is_int(Crawler::RECURSION_LIMIT), "Crawler::RECURSION_LIMIT devrait être un nombre entier (int)");
		>assertTrue(is_int(Crawler::ADDED), "Crawler::ADDED devrait être un nombre entier (int)");
		>assertTrue(is_int(Crawler::UPDATED), "Crawler::UPDATED devrait être un nombre entier (int)");
		>assertTrue(is_int(Crawler::DUPLICATED), "Crawler::DUPLICATED devrait être un nombre entier (int)");
		>assertTrue(is_int(Crawler::NOT_FOUND), "Crawler::NOT_FOUND devrait être un nombre entier (int)");

		>assertTrue(method_exists('Crawler', 'getDataFromUrl'), "Crawler::getDataFromUrl doit être définie");
		>assertTrue(is_null(Crawler::getDataFromUrl('http://jenexisteassurementpas/')), "Crawler::getDataFromUrl('http://jenexisteassurementpas/') devrait retourner null");
		>assertTrue(is_array(Crawler::getDataFromUrl('http://insearch-intranet.selfbuild.fr/')), "Crawler::getDataFromUrl('http://insearch-intranet.selfbuild.fr/') devrait retourner un array");
		>assertTrue(is_array(Crawler::getDataFromUrl('https://insearch-intranet.selfbuild.fr/')), "Crawler::getDataFromUrl('https://p.holowar.com/duel') devrait retourner un array");

