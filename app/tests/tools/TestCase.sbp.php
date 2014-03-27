<?php

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\View\View as BaseView;
use Illuminate\Http\Response as BaseResponse;

a TestCase:Illuminate\Foundation\Testing\TestCase

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	+ createApplication
		$unitTesting = true;
		$testEnvironment = 'testing';
		< require __DIR.'/../../../bootstrap/start.php';

	* tryRequest $method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true
		try
			<>client->request($method, $url, $parameters, $files, $server, $content, $changeHistory);
		catch NotFoundHttpException $e
			< false;

	* tryResponse $method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true
		>tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory);
		<>client->getResponse();

	* assertFound $method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true
		>assertTrue($this->tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory) !== false, "Cette requête ne devrait pas renvoyer une erreur 404 Not Found");

	* assertNotFound $method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true
		>assertTrue($this->tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory) === false, "Cette requête devrait renvoyer une erreur 404 Not Found");

	* assertJsonResponse $method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true
		>assertJson($this->tryResponse($method, $url, $parameters, $files, $server, $content, $changeHistory)->getContent(), "Cette requête devrait renvoyer un résultat au format JSON");

	* assertFilter $method, $url, $filter, $count = 1, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true
		$crawler = >tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory);
		>assertCount($count, $crawler->filter($filter));

	* assertView $response, $name
		>assertTrue($response instanceof View || $response instanceof BaseView || $response instanceof BaseResponse, $name . " devrait retourner une vue (View)");

	* assertThrowNotFoundHttpException $object, $method, $parameters = array()
		try
			call_user_func_array(array($object, $method), $parameters);
			>assertTrue(false, "Devrait retourner une erreur 404");
		catch NotFoundHttpException $e
			>assertTrue(true, "Devrait retourner une erreur 404");

	* assertStatus $response, $status, $text = 'La méthode'
		>assertEquals($response->getStatusCode(), $status, $text . " devrait renvoyer un status 302 (" . SymfonyResponse::$statusTexts[$status] . ")");