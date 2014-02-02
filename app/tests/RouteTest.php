<?php

class RouteTest extends TestCase {

	/**
	 * Vérifie que les routes mènes aux bonnes méthodes
	 *
	 * @return void
	 */
	public function testResponses()
	{

		$this->assertFilter('GET', '/', 'h1:contains("InSearch")');
		$this->assertFilter('GET', '/terme-de-recherche', 'form input[value="terme-de-recherche"]');

		$this->assertNotFound('GET', '/je/nexiste/pas');
		$this->assertFound('GET', '/most-popular/1');
		$this->assertFound('GET', '/most-popular/1/100');
		$this->assertFound('GET', '/history/1');

		$this->assertJsonResponse('POST', '/autocomplete', array('q' => 'p'));

	}

}