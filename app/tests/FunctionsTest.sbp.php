<?

FunctionsTest:TestCase

	/**
	 * Vérifie que la fonction de traduction § fonctionne
	 *
	 * @return void
	 */
	+ test§

		Language::setLocale('en');
		>assertTrue(§('global.results.count', 0, array('title' => 'T')) === 'T - Any result', "§('global.results.count', 0) devrait retourner 'T - Any result'");
		>assertTrue(§('global.results.count', 1, array('title' => 'T')) === 'T - One result', "§('global.results.count', 1) devrait retourner 'T - One result'");
		>assertTrue(§('global.results.count', 2, array('title' => 'T')) === 'T - 2 results', "§('global.results.count', 2) devrait retourner 'T - 2 results'");
		Language::setLocale('fr');
		>assertTrue(§('global.results.count', 0, array('title' => 'T')) === 'T - Aucun résultat', "§('global.results.count', 0) devrait retourner 'T - Aucun résultat'");
		>assertTrue(§('global.results.count', 1, array('title' => 'T')) === 'T - Un résultat', "§('global.results.count', 1) devrait retourner 'T - One result'");
		>assertTrue(§('global.results.count', 2, array('title' => 'T')) === 'T - 2 résultats', "§('global.results.count', 2) devrait retourner 'T - 2 résultats'");


	/**
	 * Vérifie que la fonction normalize fonctionne
	 *
	 * @return void
	 */
	+ testNormalize

		>assertTrue(normalize("L'été") === "l'ete", "normalize(\"L'été\") devrait retourner \"l'ete\"");
		>assertTrue(normalize("L'été", false) === "L'ete", "normalize(\"L'été\", false) devrait retourner \"L'ete\"");


	/**
	 * Vérifie que la fonction array_maps fonctionne
	 *
	 * @return void
	 */
	+ testArrayMaps

		>assertTrue(array_maps('strtolower,ucfirst', array('aZeRtY')) === array('Azerty'), "array_maps('strtolower,ucfirst', array('aZeRtY')) devrait retourner array('Azerty')");


	/**
	 * Vérifie que la fonction scanUrl fonctionne
	 *
	 * @return void
	 */
	+ testScanUrl

		>assertTrue(in_array(
			scanUrl('http://insearch.selfbuild.fr/'),
			array(Crawler::ADDED, Crawler::UPDATED, Crawler::DUPLICATED)
		), "scanUrl('http://insearch.selfbuild.fr/') ne devrait pas retourner Crawler::NOT_FOUND");


	/**
	 * Vérifie que la fonction ip2bin fonctionne
	 *
	 * @return void
	 */
	+ testIp2Bin

		>assertTrue(ip2bin('255.36.126.15') === 'ff247e0f', "ip2bin('255.36.126.15') devrait retourner 'ff247e0f'");
		>assertTrue(ip2bin() === ip2bin(Request::getClientIp()), "ip2bin devrait utiliser Request::getClientIp() à défaut de paramètre");


	/**
	 * Vérifie que la fonction replace fonctionne
	 *
	 * @return void
	 */
	+ testReplace

		>assertTrue(replace('#[0-9]#', '-', '1y7u88ilk') === '-y-u--ilk', "replace('#[0-9]#', '-', '1y7u88ilk') devrait retourner '-y-u--ilk'");
		>assertTrue(replace('#[0-9]#', f° $v
			< $v[0] + 1;
		, '1y7u88ilk') === '2y8u99ilk', "replace('#[0-9]#', function (\$v) { return \$v[0] + 1; }, '1y7u88ilk') devrait retourner '2y8u99ilk'");
		>assertTrue(replace(array(
			'#[0-9]#' => f° $v
				< str_repeat('-', $v[0]);
			,
			'#-{10}#' => '[10]',
			'u' => 'A',
			'ilk' => 'B'
		), '1y7u88ilk') === '-y-------A[10]------B', "replace(..., '1y7u88ilk') devrait retourner '-y-------A[10]------B'");


	/**
	 * Vérifie que la fonction accents2entities fonctionne
	 *
	 * @return void
	 */
	+ testAccents2Entities

		>assertTrue(accents2entities('étà') === '&eacute;t&agrave;', "accents2entities('étà') devrait retourner '&eacute;t&agrave;'");


	/**
	 * Vérifie que la fonction utf8 fonctionne
	 *
	 * @return void
	 */
	+ testUtf8

		>assertTrue(utf8('étà') === 'étà', "utf8('étà') devrait retourner 'étà' sans changement si la chaîne d'origine est en UTF-8");
		>assertTrue(utf8(utf8_encode('étà')) === 'étà', "utf8(utf8_encode('étà')) devrait retourner 'étà' avec changement si la chaîne d'origine n'est pas en UTF-8");
		>assertTrue(utf8(utf8_decode('étà')) === 'étà', "utf8(utf8_decode('étà')) devrait retourner 'étà' avec changement si la chaîne d'origine n'est pas en UTF-8");


	/**
	 * Vérifie que la fonction http_negotiate_language fonctionne
	 *
	 * @return void
	 */
	+ testHttpNegotiateLanguage

		>assertTrue(function_exists('http_negotiate_language'), "http_negotiate_language devrait être définie");
