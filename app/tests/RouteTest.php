<?php

class RouteTest extends TestCase {

	/**
	 * Vérifie que les routes mènes aux bonnes méthodes
	 *
	 * @return void
	 */
	public function testRouteToMethodes()
	{
		$content = $this->getUrl('/');
		$this->assertTrue(strpos($content, 'name="q"') !== false);

		$content = $this->getUrl('/je/nexiste/pas');
		$this->assertTrue(strpos($content, 'name="q"') !== false);
		/*
		$crawler = $this->client->request('GET', '/');

		$this->assertTrue($this->client->getResponse()->isOk());
		$this->assertRedirectedToAction('HomeController@searchBar');
		foreach(array(
			'/terme-de-recherche' => 'HomeController@searchResult',
			'/3/terme-de-recherche' => 'HomeController@searchResult',
			'/2/terme-de-recherche/50' => 'HomeController@searchResult',
			'/out/terme-de-recherche/1' => 'HomeController@goOut',
			'/most-popular/1' => 'HomeController@mostPopular',
			'/most-popular/1/100' => 'HomeController@mostPopular',
			'/history/1' => 'HomeController@history',
			'/history/1/100' => 'HomeController@history',
		) as $getUrl => $route)
		{
			$crawler = $this->client->request('GET', $getUrl);
			$this->assertRedirectedToAction($route);
		}
		foreach(array(
			'/' => 'HomeController@searchResult',
			'/add-url' => 'HomeController@addUrl',
		) as $postUrl => $route)
		{
			$crawler = $this->client->request('POST', $postUrl);
			$this->assertRedirectedToAction($route);
		}

		$crawler = $this->client->request('GET', '/je/nexiste/pas');

		$this->assertTrue($this->client->getResponse()->isKo());

		$crawler = $this->client->request('POST', '/autocomplete');

		$this->assertTrue(strpos($this->client->getResponse(), '{') === 0);
		*/

	}

}