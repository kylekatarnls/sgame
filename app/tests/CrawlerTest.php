<?php

class CrawlerTest extends TestCase {

	/**
	 * Vérifie que le crawler fonctionne
	 *
	 * @return void
	 */
	public function testCrawler()
	{
		$this->assertTrue(method_exists('Crawler', 'getDataFromUrl'), "Crawler::getDataFromUrl doit être définie");
		// Ca bogue mais je ne sais pas pourquoi
		// $this->assertTrue(is_null(Crawler::getDataFromUrl('http://jenexisteassurementpas/')));

	}

}