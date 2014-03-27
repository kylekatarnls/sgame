<?

RouteTest:TestCase

	/**
	 * Vérifie que les routes mènes aux bonnes méthodes
	 *
	 * @return void
	 */
	+ testResponses

		>assertFilter('GET', '/', 'h1:contains("InSearch")');
		>assertFilter('GET', '/terme-de-recherche', 'form input[value="terme-de-recherche"]');

		>assertNotFound('GET', '/je/nexiste/pas');
		>assertFound('GET', '/most-popular/1');
		>assertFound('GET', '/most-popular/1/100');
		>assertFound('GET', '/history/1');

		>assertJsonResponse('POST', '/autocomplete', array('q' => 'p'));
