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
		/*
		$homeController = new HomeController;
		$this->assertView($homeController->searchBar(), "HomeController->searchBar()");
		$this->assertView($homeController->searchResult(), "HomeController->searchResult()");
		try
		{
			$homeController->goOut('', -1);
			$this->assertTrue(false, "Devrait retourner une erreur 404");
		}
		catch(NotFoundHttpException $e)
		{
			$this->assertTrue(true, "Devrait retourner une erreur 404");
		}
		$this->assertView($homeController->addUrl(), "HomeController->addUrl()");
		$this->assertView($homeController->mostPopular(), "HomeController->mostPopular()");
		$this->assertView($homeController->history(), "HomeController->history()");
		*/

	}

	/**
	 * Vérifie que la classe DevController fonctionne
	 *
	 * @return void
	 */
	public function testDevController()
	{

		$this->assertTrue(method_exists('DevController', 'specs'), "DevController->specs() devrait exister");
		/*
		$devController = new DevController;
		$this->assertView($devController->specs(), "DevController->specs()");
		*/

	}
}