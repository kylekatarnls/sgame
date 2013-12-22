<?php

class HomeController extends BaseController {

	const DEFAULT_RESULLTS_PER_PAGE = 10;
	const ENUM_RESULLTS_PER_PAGE = '10,50,100,500';
	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| 
	|
	*/

	public function searchBar()
	{
		return View::make('home');
	}

	public function searchResult($page = 1, $resultsPerPage = null, $q = null)
	{
		$q = is_null($q) ? Request::get('q', '') : urldecode($q);
		$resultsPerPage = is_null($resultsPerPage) ? Request::get('resultsPerPage', self::DEFAULT_RESULLTS_PER_PAGE) : $resultsPerPage;
		$choice = array_map('intval', explode(',', self::ENUM_RESULLTS_PER_PAGE));
		if(!in_array($resultsPerPage, $choice))
		{
			$resultsPerPage = self::ENUM_RESULLTS_PER_PAGE;
		}
		$results = CrawledContent::search($q)->get();
		$nbResults = count($results);
		$nbPages = ceil($nbResults / $resultsPerPage);
		$keepResultsPerPage = $resultsPerPage == self::ENUM_RESULLTS_PER_PAGE ? '' : $resultsPerPage.'/';

		return View::make('result')->with([
			'q' => $q,
			'nbPages' => (int) $nbPages,
			'currentPage' => (int) $page,
			'pageUrl' => '/%d/'.$keepResultsPerPage.urlencode($q),
			'resultsPerPageUrl' => '/'.$page.'/%d/'.urlencode($q),
			'results' => $results,
			'nbResults' => (int) $nbResults,
			'resultsPerPage' => (int) $resultsPerPage,
			'choiceResultsPerPage' => $choice
		]);
	}

}