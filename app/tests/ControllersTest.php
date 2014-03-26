<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ControllersTest extends TestCase {

	/**
	 * Vérifie que la classe HomeController fonctionne
	 *
	 * @return void
	 */
	public function testHomeController()
	{

		$this->assertTrue(method_exists('HomeController', 'searchBar'), "HomeController->searchBar() devrait exister");
		$this->assertTrue(method_exists('HomeController', 'searchResult'), "HomeController->searchResult() devrait exister");
		$this->assertTrue(method_exists('HomeController', 'goOut'), "HomeController->goOut() devrait exister");
		$this->assertTrue(method_exists('HomeController', 'addUrl'), "HomeController->addUrl() devrait exister");
		$this->assertTrue(method_exists('HomeController', 'mostPopular'), "HomeController->mostPopular() devrait exister");
		$this->assertTrue(method_exists('HomeController', 'history'), "HomeController->history() devrait exister");

		$homeController = new HomeController;
		$this->assertView($homeController->searchBar(), "HomeController->searchBar()");
		$this->assertView($homeController->searchResult(), "HomeController->searchResult()");

		$invalidId = -1;
		$validId = User::first()->id;

		try
		{
			$homeController->goOut('', $invalidId);
			$this->assertTrue(false, "Devrait retourner une erreur 404");
		}
		catch(NotFoundHttpException $e)
		{
			$this->assertTrue(true, "Devrait retourner une erreur 404");
		}
		$this->assertEquals($homeController->goOut('', $validId)->getStatusCode(), 302, "HomeController->goOut() devrait renvoyer un status 302 (redirection temporaire)");
		Auth::login(new User(array(
			'flags' => User::CONTRIBUTOR,
		)));
		$this->assertView($homeController->addUrl(), "HomeController->addUrl()");
		$this->assertEquals($homeController->list()->getStatusCode(), 302, "HomeController->list() devrait renvoyer un status 302 (redirection temporaire)");
		$this->assertView($homeController->delete($validId), "HomeController->delete()");
		Auth::logout();
		$this->assertEquals($homeController->addUrl()->getStatusCode(), 302, "HomeController->addUrl() devrait renvoyer un status 302 (redirection temporaire)");
		Auth::login(new User(array(
			'flags' => User::MODERATOR,
		)));
		try
		{
			$homeController->delete($invalidId);
			$this->assertTrue(false, "Devrait retourner une erreur 404");
		}
		catch(NotFoundHttpException $e)
		{
			$this->assertTrue(true, "Devrait retourner une erreur 404");
		}
		$this->assertView($homeController->delete($validId), "HomeController->delete()");
		$this->assertEquals($homeController->addUrl()->getStatusCode(), 302, "HomeController->addUrl() devrait renvoyer un status 302 (redirection temporaire)");
		$this->assertEquals($homeController->list()->getStatusCode(), 302, "HomeController->list() devrait renvoyer un status 302 (redirection temporaire)");
		Auth::logout();
		$this->assertEquals($homeController->delete($validId)->getStatusCode(), 302, "HomeController->delete() devrait renvoyer un status 302 (redirection temporaire)");
		Auth::login(new User(array(
			'flags' => User::ADMINISTRATOR,
		)));
		$this->assertEquals($homeController->addUrl()->getStatusCode(), 302, "HomeController->addUrl() devrait renvoyer un status 302 (redirection temporaire)");
		$this->assertView($homeController->list(), "HomeController->list()");
		$this->assertView($homeController->delete($validId), "HomeController->delete()");
		Auth::logout();
		$this->assertEquals($homeController->list()->getStatusCode(), 302, "HomeController->list() devrait renvoyer un status 302 (redirection temporaire)");
		$this->assertView($homeController->mostPopular(1), "HomeController->mostPopular()");
		$this->assertView($homeController->history(1), "HomeController->history()");

	}

	/**
	 * Vérifie que la classe DevController fonctionne
	 *
	 * @return void
	 */
	public function testDevController()
	{

		$this->assertTrue(method_exists('DevController', 'specs'), "DevController->specs() devrait exister");

		$devController = new DevController;
		$this->assertView($devController->specs(), "DevController->specs()");

	}
}