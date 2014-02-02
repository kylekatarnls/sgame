<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

	protected function tryRequest($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		try
		{
			return $this->client->request($method, $url, $parameters, $files, $server, $content, $changeHistory);
		}
		catch(NotFoundHttpException $e)
		{
			return false;
		}
	}

	protected function tryResponse($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$this->tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory);
		return $this->client->getResponse();
	}

	protected function assertFound($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$this->assertTrue($this->tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory) !== false, "Cette requête ne devrait pas renvoyer une erreur 404 Not Found");
	}

	protected function assertNotFound($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$this->assertTrue($this->tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory) === false, "Cette requête devrait renvoyer une erreur 404 Not Found");
	}

	protected function assertJsonResponse($method, $url, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$this->assertJson($this->tryResponse($method, $url, $parameters, $files, $server, $content, $changeHistory)->getContent(), "Cette requête devrait renvoyer un résultat au format JSON");
	}

	protected function assertFilter($method, $url, $filter, $count = 1, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
	{
		$crawler = $this->tryRequest($method, $url, $parameters, $files, $server, $content, $changeHistory);
		$this->assertCount($count, $crawler->filter($filter));
	}

}