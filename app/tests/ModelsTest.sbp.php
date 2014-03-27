<?

ModelsTest:TestCase

	/**
	 * Vérifie que la classe Model fonctionne
	 *
	 * @return void
	 */
	+ testModel

		$crawledContent = new CrawledContent;
		>assertTrue(method_exists($crawledContent, 'newQuery'), "CrawledContent->newQuery() devrait exister");
		>assertTrue($crawledContent->newQuery() instanceof ModelBuilder, "CrawledContent->newQuery() devrait retourner une instance de ModelBuilder");
		>assertTrue($crawledContent->crossDriver(array('default' => 'truc')) is 'truc', "CrawledContent->crossDriver(array('default' => 'truc')) devrait retourner 'truc'");
		>assertTrue(method_exists($crawledContent, 'findAndCount'), "CrawledContent->findAndCount() devrait exister");
		>assertTrue(method_exists($crawledContent, 'substr'), "CrawledContent->substr() devrait exister");
		>assertTrue(method_exists($crawledContent, 'substring'), "CrawledContent->substring() devrait exister");
		>assertTrue(method_exists($crawledContent, 'caseWhen'), "CrawledContent->caseWhen() devrait exister");
		>assertTrue($crawledContent->search('truc') instanceof ModelBuilder, "CrawledContent->search() devrait retourner une instance de ModelBuilder");
		>assertTrue(Model::REMEMBER is false || is_int(Model::REMEMBER), "Model::REMEMBER devrait être un nombre entier (int) ou false");
		>assertTrue(is_int(Model::KEY_WORD_SCORE), "Model::KEY_WORD_SCORE devrait être un nombre entier (int)");
		>assertTrue(is_int(Model::COMPLETE_QUERY_SCORE), "Model::COMPLETE_QUERY_SCORE devrait être un nombre entier (int)");
		>assertTrue(is_int(Model::ONE_WORD_SCORE), "Model::ONE_WORD_SCORE devrait être un nombre entier (int)");


	/**
	 * Vérifie que la classe CrawledContent fonctionne
	 *
	 * @return void
	 */
	+ testCrawledContent

		>assertTrue(is_int(CrawledContent::SAME_LANGUAGE), "CrawledContent::SAME_LANGUAGE devrait être un nombre entier (int)");
		>assertTrue(is_int(CrawledContent::SAME_PRIMARY_LANGUAGE), "CrawledContent::SAME_PRIMARY_LANGUAGE devrait être un nombre entier (int)");
		$crawledContent = new CrawledContent;
		>assertTrue($crawledContent->getSearchResult('truc') instanceof ModelBuilder, "CrawledContent->getSearchResult() devrait retourner une instance de ModelBuilder");
		>assertTrue($crawledContent->popular() instanceof ModelBuilder, "CrawledContent->popular() devrait retourner une instance de ModelBuilder");
		>assertTrue(method_exists($crawledContent, 'keyWords'), "CrawledContent->keyWords() devrait exister");
		>assertTrue(method_exists($crawledContent, 'scan'), "CrawledContent->scan() devrait exister");
		>assertTrue(is_string($crawledContent->outgoingLink), "CrawledContent->outgoingLink devrait retourner une chaîne (string)");
		>assertTrue(is_string($crawledContent->urlAndLanguage), "CrawledContent->urlAndLanguage devrait retourner une chaîne (string)");
		>assertTrue(is_numeric($crawledContent->count), "CrawledContent->count devrait retourner une valeur numérique");
		>assertTrue(is_string($crawledContent->content), "CrawledContent->content devrait retourner une chaîne (string)");
		>assertTrue(is_string($crawledContent->title), "CrawledContent->title devrait retourner une chaîne (string)");


	/**
	 * Vérifie que la classe KeyWord fonctionne
	 *
	 * @return void
	 */
	+ testKeyWord

		>assertTrue(method_exists('KeyWord', 'crawledContents'), "KeyWord->crawledContents() devrait exister");


	/**
	 * Vérifie que la classe LogOutgoingLink fonctionne
	 *
	 * @return void
	 */
	+ testLogOutgoingLink

		$logOutgoingLink = LogOutgoingLink::first();
		$count = $logOutgoingLink->count();
		>assertTrue(is_numeric($count), "LogOutgoingLink->count() devrait retourner une valeur numérique");
		>assertTrue($count is 0 || $logOutgoingLink->created_at instanceof TranslatableDateTime, "LogOutgoingLink->created_at devrait exister");


	/**
	 * Vérifie que la classe LogSearch fonctionne
	 *
	 * @return void
	 */
	+ testLogSearch

		>assertTrue(method_exists('LogSearch', 'log'), "LogSearch->log() devrait exister");
		>assertTrue(LogSearch::mine() instanceof ModelBuilder, "LogSearch::mine() devrait retourner une instance de ModelBuilder");
		>assertTrue(is_array(LogSearch::startWith('a')), "LogSearch::startWith('a') devrait retourner un array");
		$page = -2;
		>assertTrue(is_array(LogSearch::mine()->paginatedData($page, 40)), "LogSearch->paginatedData() devrait retourner un array");
		>assertTrue($page is 1, "\$page devrait valloir 1");


