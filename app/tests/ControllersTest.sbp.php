<?

ControllersTest:TestCase

	/**
	 * Vérifie que la classe HomeController fonctionne
	 *
	 * @return void
	 */
	+ testHomeController

		should method_exists('HomeController', 'searchBar'), "HomeController->searchBar() devrait exister";
		should method_exists('HomeController', 'searchResult'), "HomeController->searchResult() devrait exister";
		should method_exists('HomeController', 'goOut'), "HomeController->goOut() devrait exister";
		should method_exists('HomeController', 'addUrl'), "HomeController->addUrl() devrait exister";
		should method_exists('HomeController', 'mostPopular'), "HomeController->mostPopular() devrait exister";
		should method_exists('HomeController', 'history'), "HomeController->history() devrait exister";

		$homeController = new HomeController;
		$userController = new UserController;

		$invalidId = -1;
		$validId = User::first()->id;

		>assertView($homeController->searchBar(), "HomeController->searchBar()");
		>assertView($homeController->searchResult(), "HomeController->searchResult()");

		>assertThrowNotFoundHttpException($homeController, 'goOut', array('', $invalidId));

		>assertStatus($homeController->goOut('', $validId), 302, "HomeController->goOut()");
		Auth::login(new User(array(
			'flags' => User::CONTRIBUTOR,
		)));
		>assertView($homeController->addUrl(), "HomeController->addUrl()");
		>assertStatus($userController->listAll(), 302, "HomeController->listAll()");
		>assertStatus($homeController->delete($validId), 302, "HomeController->delete()");
		Auth::logout();
		>assertStatus($homeController->addUrl(), 302, "HomeController->addUrl()");
		Auth::login(new User(array(
			'flags' => User::MODERATOR,
		)));
		>assertThrowNotFoundHttpException($homeController, 'delete', array($invalidId));

		>assertView($homeController->delete($validId), "HomeController->delete()");
		>assertStatus($homeController->addUrl(), 302, "HomeController->addUrl()");
		>assertStatus($userController->listAll(), 302, "HomeController->listAll()");
		Auth::logout();
		>assertStatus($homeController->delete($validId), 302, "HomeController->delete()");
		Auth::login(new User(array(
			'flags' => User::ADMINISTRATOR,
		)));
		>assertView($userController->listAll(), "HomeController->listAll()");
		>assertStatus($homeController->addUrl(), 302, "HomeController->addUrl()");
		>assertStatus($homeController->delete($validId), 302, "HomeController->delete()");
		Auth::logout();
		>assertStatus($userController->listAll(), 302, "HomeController->listAll()");
		>assertView($homeController->mostPopular(1), "HomeController->mostPopular()");
		>assertView($homeController->history(1), "HomeController->history()");


	/**
	 * Vérifie que la classe DevController fonctionne
	 *
	 * @return void
	 */
	+ testDevController

		should method_exists('DevController', 'specs'), "DevController->specs() devrait exister";

		$devController = new DevController;
		>assertView($devController->specs(), "DevController->specs()");
