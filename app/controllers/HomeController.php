<?php

class HomeController extends BaseController {

	protected function paginateResults(&$page, $resultsPerPage, $nbResults, $results)
	{
		$page = (int) max(1, $page);
		$resultsPerPage = ResultsPerPage::getChoice($resultsPerPage);
		$choice = ResultsPerPage::getChoices();
		if(!in_array($resultsPerPage, $choice))
		{
			$resultsPerPage = ResultsPerPage::DEFAULT_CHOICE;
		}
		$nbPages = ceil($nbResults / $resultsPerPage);
		if($page > $nbPages)
		{
			$page = 1;
		}
		$keepResultsPerPage = $resultsPerPage == ResultsPerPage::DEFAULT_CHOICE ? '' : '/' . $resultsPerPage;
		if(is_callable($results))
		{
			$results = call_user_func($results, $page, $resultsPerPage);
		}
		return array(
			'nbPages' => (int) $nbPages,
			'currentPage' => (int) $page,
			'keepResultsPerPage' => $keepResultsPerPage,
			'results' => $results,
			'nbResults' => (int) $nbResults,
			'resultsPerPage' => (int) $resultsPerPage,
			'choiceResultsPerPage' => $choice
		);
	}

	public function searchBar()
	{
		return View::make('home');
	}

	public function searchResult($page = 1, $q = null, $resultsPerPage = null)
	{
		$q = is_null($q) ? Request::get('q', $page) : urldecode($q);
		$data = self::paginateResults(
			$page,
			$resultsPerPage,
			CrawledContent::searchCount($q),
			function ($page, $resultsPerPage) use($q)
			{
				return CrawledContent::getSearchResult($q, $page, $resultsPerPage);
			}
		);
		LogSearch::log($q, $data['nbResults']);
		return View::make('result')->with(array_merge(
			$data,
			array(
				'q' => $q,
				'pageUrl' => '/%d/'.urlencode($q).$data['keepResultsPerPage'],
				'resultsPerPageUrl' => '/'.$page.'/'.urlencode($q).'/%d'
			)
		));
	}

	public function goOut($search_query, $id)
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
			'state' => $state
		));
	}

	public function mostPopular($page, $resultsPerPage = null)
	{
		$data = self::paginateResults(
			$page,
			$resultsPerPage,
			CrawledContent::leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
				->whereNotNull('log_outgoing_links.id')
				->count(DB::raw('DISTINCT crawled_contents.id')),
			function ($page, $resultsPerPage)
			{
				return CrawledContent::select(
						'crawled_contents.id',
						'url', 'title', 'content', 'language',
						DB::raw('COUNT(log_outgoing_links.id) AS count')
					)
					->leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
					->whereNotNull('log_outgoing_links.id')
					->groupBy('crawled_contents.id')
					->orderBy('count', 'desc')
					->forPage($page, $resultsPerPage)
					->get();
			}
		);
		return View::make('result')->with(array_merge(
			$data,
			array(
				'q' => '',
				'pageUrl' => '/history/%d'.$data['keepResultsPerPage'],
				'resultsPerPageUrl' => '/history/'.$page.'/%d'
			)
		));
	}

	public function history($page, $resultsPerPage = null)
	{
		$data = self::paginateResults(
			$page,
			$resultsPerPage,
			LogSearch::mine()->count(),
			function ($page, $resultsPerPage)
			{
				return LogSearch::mine()
					->forPage($page, $resultsPerPage)
					->get();
			}
		);
		return View::make('history')->with(array_merge(
			$data,
			array(
				'q' => '',
				'pageUrl' => '/history/%d'.$data['keepResultsPerPage'],
				'resultsPerPageUrl' => '/history/'.$page.'/%d'
			)
		));
	}

}