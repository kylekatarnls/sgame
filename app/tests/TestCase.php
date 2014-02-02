<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\View\View as BaseView;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	protected function tryRequest($method, $url)
	{
		try
		{
			return $this->client->request($method, $url);
		}
		catch(NotFoundHttpException $e)
		{
			return false;
		}
	}

	protected function tryResponse($method, $url)
	{
		$this->tryRequest($method, $url);
		return $this->client->getResponse();
	}

	protected function assertFound($method, $url)
	{
		$this->assertTrue($this->tryRequest($method, $url) !== false);
	}

	protected function assertNotFound($method, $url)
	{
		$this->assertTrue($this->tryRequest($method, $url) === false);
	}

	protected function assertJsonResponse($method, $url)
	{
		$this->assertJson($this->tryResponse($method, $url)->getContent());
	}

	protected function assertFilter($method, $url, $filter, $count = 1)
	{
		$crawler = $this->tryRequest($method, $url);
		$this->assertCount($count, $crawler->filter($filter));
	}

	protected function assertView($response, $name)
	{
		$this->assertTrue($response instanceof View || $response instanceof BaseView, $name . " devrait retourner une vue (View)");
	}

}