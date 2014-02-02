<?php

class FunctionsTest extends TestCase {

	/**
	 * Vérifie que la fonction de traduction § fonctionne
	 *
	 * @return void
	 */
	public function test§()
	{

		Language::setLocale('en');
		$this->assertTrue(§('global.results.count', 0, array('title' => 'T')) === 'T - Any result', "§('global.results.count', 0) devrait retourner 'T - Any result'");
		$this->assertTrue(§('global.results.count', 1, array('title' => 'T')) === 'T - One result', "§('global.results.count', 1) devrait retourner 'T - One result'");
		$this->assertTrue(§('global.results.count', 2, array('title' => 'T')) === 'T - 2 results', "§('global.results.count', 2) devrait retourner 'T - 2 results'");
		Language::setLocale('fr');
		$this->assertTrue(§('global.results.count', 0, array('title' => 'T')) === 'T - Aucun résultat', "§('global.results.count', 0) devrait retourner 'T - Aucun résultat'");
		$this->assertTrue(§('global.results.count', 1, array('title' => 'T')) === 'T - Un résultat', "§('global.results.count', 1) devrait retourner 'T - One result'");
		$this->assertTrue(§('global.results.count', 2, array('title' => 'T')) === 'T - 2 résultats', "§('global.results.count', 2) devrait retourner 'T - 2 résultats'");

	}

	/**
	 * Vérifie que la fonction normalize fonctionne
	 *
	 * @return void
	 */
	public function testNormalize()
	{
		$this->assertTrue(normalize("L'été") === "l'ete", "normalize(\"L'été\") devrait retourner \"l'ete\"");
		$this->assertTrue(normalize("L'été", false) === "L'ete", "normalize(\"L'été\", false) devrait retourner \"L'ete\"");

	}

	/**
	 * Vérifie que la fonction array_maps fonctionne
	 *
	 * @return void
	 */
	public function testArrayMaps()
	{

		$this->assertTrue(array_maps('strtolower,ucfirst', array('aZeRtY')) === array('Azerty'), "array_maps('strtolower,ucfirst', array('aZeRtY')) devrait retourner array('Azerty')");

	}

	/**
	 * Vérifie que la fonction scanUrl fonctionne
	 *
	 * @return void
	 */
	public function testScanUrl()
	{

		//$this->assertTrue(scanUrl('http://localhost/') !== Crawler::NOT_FOUND, "scanUrl('http://localhost/') ne devrait pas retourner Crawler::NOT_FOUND");

	}

	/**
	 * Vérifie que la fonction ip2bin fonctionne
	 *
	 * @return void
	 */
	public function testIp2Bin()
	{

		$this->assertTrue(ip2bin('255.36.126.15') === 'ff247e0f', "ip2bin('255.36.126.15') devrait retourner 'ff247e0f'");
		$this->assertTrue(ip2bin() === ip2bin(Request::getClientIp()), "ip2bin devrait utiliser Request::getClientIp() à défaut de paramètre");

	}

	/**
	 * Vérifie que la fonction replace fonctionne
	 *
	 * @return void
	 */
	public function testReplace()
	{

		$this->assertTrue(replace('#[0-9]#', '-', '1y7u88ilk') === '-y-u--ilk', "replace('#[0-9]#', '-', '1y7u88ilk') devrait retourner '-y-u--ilk'");
		$this->assertTrue(replace('#[0-9]#', function ($v) { return $v[0] + 1; }, '1y7u88ilk') === '2y8u99ilk', "replace('#[0-9]#', function (\$v) { return \$v[0] + 1; }, '1y7u88ilk') devrait retourner '2y8u99ilk'");
		$this->assertTrue(replace(array(
			'#[0-9]#' => function ($v) { return str_repeat('-', $v[0]); },
			'#-{10}#' => '[10]',
			'u' => 'A',
			'ilk' => 'B'
		), '1y7u88ilk') === '-y-------A[10]------B', "replace(..., '1y7u88ilk') devrait retourner '-y-------A[10]------B'");

	}

	/**
	 * Vérifie que la fonction accents2entities fonctionne
	 *
	 * @return void
	 */
	public function testAccents2Entities()
	{

		$this->assertTrue(accents2entities('étà') === '&eacute;t&agrave;', "accents2entities('étà') devrait retourner '&eacute;t&agrave;'");

	}

	/**
	 * Vérifie que la fonction utf8 fonctionne
	 *
	 * @return void
	 */
	public function testUtf8()
	{

		//$this->assertTrue(utf8_decode(utf8('étà')) === 'étà', "utf8(utf8_decode('étà')) devrait retourner '&eacute;t&eacute;'");

	}

	/**
	 * Vérifie que la fonction http_negotiate_language fonctionne
	 *
	 * @return void
	 */
	public function testHttpNegotiateLanguage()
	{

		$this->assertTrue(function_exists('http_negotiate_language'), "http_negotiate_language devrait être définie");

	}

}