<?

ResultsPerPageTest:TestCase

	/**
	 * Vérifie que la méthode ResultsPerPage::getChoices() fonctionne
	 *
	 * @return void
	 */
	+ testGetChoices

		$choices = ResultsPerPage::getChoices();
		>assertTrue(is_array($choices), "ResultsPerPage::getChoices() devrait retourner un array");
		>assertGreaterThan(1, count($choices), "ResultsPerPage::getChoices() devrait renvoyer au moins 2 choix");


	/**
	 * Vérifie que la méthode ResultsPerPage::getChoice() fonctionne
	 *
	 * @return void
	 */
	+ testGetChoice

		>assertTrue(is_int(ResultsPerPage::getChoice()), "ResultsPerPage::getChoice() devrait retourner un nombre entier (int)");


	/**
	 * Vérifie que la méthode ResultsPerPage::completeUrl() fonctionne
	 *
	 * @return void
	 */
	+ testCompleteUrl

		>assertTrue(str_contains(
				ResultsPerPage::completeUrl('/test{keepResultsPerPage}', 3),
				array('/test', 3)
			),
			"ResultsPerPage::completeUrl(\$url, \$results) devrait retourner une URL contenant \$results basée sur \$url");


	/**
	 * Vérifie que la méthode ResultsPerPage::paginate() fonctionne
	 *
	 * @return void
	 */
	+ testPaginate

		ResultsPerPage::paginate(10, $page);
		>assertTrue($page is 1, "\$page devrait valloir 1 et pas " . $page);
		$page = -6;
		ResultsPerPage::paginate(10, $page);
		>assertTrue($page is 1, "\$page devrait valloir 1 et pas " . $page);
		$page = 6;
		ResultsPerPage::paginate(1, $page);
		>assertTrue($page is 1, "\$page devrait valloir 1 et pas " . $page);
		$resultsPerPage = ResultsPerPage::getChoice(last(ResultsPerPage::getChoices()));
		$page = 6;
		$r = $resultsPerPage;
		$nbResults = (int) $resultsPerPage * 5.5;
		ResultsPerPage::paginate($nbResults, $page, $choice, $resultsPerPage, $nbPages);
		>assertTrue($page is 6, "\$page devrait valloir 6 et pas " . $page);
		>assertTrue($r is $resultsPerPage, "\$resultsPerPage devrait valloir " . $r . " et pas " . $resultsPerPage);
		$n = ceil($nbResults / $resultsPerPage);
		>assertTrue($nbPages is $n, "\$nbPages devrait valloir " . $n . " et pas " . $nbPages);


	/**
	 * Vérifie que la méthode ResultsPerPage::init() fonctionne
	 *
	 * @return void
	 */
	//+ testInit

		//$this->tryRequest('GET', '/');
		//ResultsPerPage::init();
		//$this->assertViewHas('resultsPerPage');

