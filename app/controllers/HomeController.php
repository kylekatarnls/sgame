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

	public function searchResult($page = 1, $resultsPerPage = null, $q = null)
	{
		$q = is_null($q) ? Request::get('q', '') : urldecode($q);
		$resultsPerPage = self::getResultsPerPage($resultsPerPage);
		$choice = self::getChoiceResultsPerPage();
		if(!in_array($resultsPerPage, $choice))
		{
			$resultsPerPage = self::ENUM_RESULLTS_PER_PAGE;
		}
		$nbResults = CrawledContent::search($q)->count();
		$nbPages = ceil($nbResults / $resultsPerPage);
		$keepResultsPerPage = $resultsPerPage == self::ENUM_RESULLTS_PER_PAGE ? '' : $resultsPerPage.'/';
		$results = CrawledContent::search($q)
			->select('crawled_contents.id', 'url', 'title', 'content', DB::raw('COUNT(log_outgoing_links.id) AS count'))
			->leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
        	->groupBy('crawled_contents.id')
			->forPage($page, $resultsPerPage)
			->get();

		return View::make('result')->with(array(
			'q' => $q,
			'nbPages' => (int) $nbPages,
			'currentPage' => (int) $page,
			'pageUrl' => '/%d/'.$keepResultsPerPage.urlencode($q),
			'resultsPerPageUrl' => '/'.$page.'/%d/'.urlencode($q),
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
		$added = scanUrl($url);
		return View::make('home')->with(array(
			'url' => $url,
			'added' => $added,
			'resultsPerPageUrl' => '#',
			'resultsPerPage' => self::getResultsPerPage(),
			'choiceResultsPerPage' => self::getChoiceResultsPerPage()
		));
	}

}