<?php

class HomeController extends BaseController {

	const DEFAULT_RESULLTS_PER_PAGE = 10;
	const ENUM_RESULLTS_PER_PAGE = '10,50,100,500';

	/*
	|--------------------------------------------------------------------------
	| PROTECTED
	|--------------------------------------------------------------------------
	*/

	protected function getChoiceResultsPerPage()
	{
		return array_map('intval', explode(',', self::ENUM_RESULLTS_PER_PAGE));
	}

	protected function getResultsPerPage($resultsPerPage = null)
	{
		$defaultResultsPerPage = (int) Cookie::get('resultsPerPage', self::DEFAULT_RESULLTS_PER_PAGE);
		$resultsPerPage = (int) Input::get(
			'resultsPerPage',
			is_null($resultsPerPage) || $resultsPerPage < 1 ?
				Request::get('resultsPerPage', $defaultResultsPerPage) :
				$resultsPerPage
		);
		if($resultsPerPage !== $defaultResultsPerPage)
		{
			Cookie::queue('resultsPerPage', $resultsPerPage, 60);
		}
		return $resultsPerPage;
	}

	/*
	|--------------------------------------------------------------------------
	| PUBLIC
	|--------------------------------------------------------------------------
	*/

	public function searchBar()
	{
		return View::make('home')->with(array(
			'resultsPerPageUrl' => '#',
			'resultsPerPage' => self::getResultsPerPage(),
			'choiceResultsPerPage' => self::getChoiceResultsPerPage()
		));
	}

	public function searchResult($page = 1, $q = null, $resultsPerPage = null)
	{
		$q = is_null($q) ? Request::get('q', $page) : urldecode($q);
		$page = (int) max(1, $page);
		$resultsPerPage = self::getResultsPerPage($resultsPerPage);
		$choice = self::getChoiceResultsPerPage();
		if(!in_array($resultsPerPage, $choice))
		{
			$resultsPerPage = self::ENUM_RESULLTS_PER_PAGE;
		}
		$nbResults = CrawledContent::searchCount($q);
		$nbPages = ceil($nbResults / $resultsPerPage);
		if($page > $nbPages)
		{
			$page = 1;
		}
		$keepResultsPerPage = $resultsPerPage == self::ENUM_RESULLTS_PER_PAGE ? '' : '/' . $resultsPerPage;
		$results = CrawledContent::getSearchResult($q, $page, $resultsPerPage);

		return View::make('result')->with(array(
			'q' => $q,
			'nbPages' => (int) $nbPages,
			'currentPage' => (int) $page,
			'pageUrl' => '/%d/'.urlencode($q).$keepResultsPerPage,
			'resultsPerPageUrl' => '/'.$page.'/'.urlencode($q).'/%d',
			'results' => $results,
			'nbResults' => (int) $nbResults,
			'resultsPerPage' => (int) $resultsPerPage,
			'choiceResultsPerPage' => $choice
		));
	}

	public function goOut($search_query = '', $id = 1)
	{
		$result = CrawledContent::find($id);
		if(!$result)
		{
			App::abort(404);
		}
		try
		{
			LogOutgoingLink::create(array(
				'search_query' => $search_query,
				'crawled_content_id' => $id
			));
			$count = Cache::get('crawled_content_id:'.$id.'_log_outgoing_link_count');
			if($count)
			{
				$count++;
			}
			else
			{
				$count = LogOutgoingLink::where('crawled_content_id', $id)->count();
			}
			Cache::put('crawled_content_id:'.$id.'_log_outgoing_link_count', $count, CrawledContent::REMEMBER);
		}
		catch(Exception $e)
		{
			throw $e;
		}
		return Redirect::to($result->url);
	}

	public function addUrl()
	{
		$url = Input::get('url');
		$state = scanUrl($url);
		return View::make('home')->with(array(
			'url' => $url,
			'state' => $state,
			'resultsPerPageUrl' => '#',
			'resultsPerPage' => self::getResultsPerPage(),
			'choiceResultsPerPage' => self::getChoiceResultsPerPage()
		));
	}

}