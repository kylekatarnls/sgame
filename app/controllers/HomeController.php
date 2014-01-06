<?php

class HomeController extends BaseController {

	public function searchBar()
	{
		return View::make('home');
	}

	public function searchResult($page = 1, $q = null, $resultsPerPage = null)
	{
		$q = is_null($q) ? Request::get('q', $page) : urldecode($q);
		$page = (int) max(1, $page);
		$resultsPerPage = ResultsPerPage::getChoice($resultsPerPage);
		$choice = ResultsPerPage::getChoices();
		if(!in_array($resultsPerPage, $choice))
		{
			$resultsPerPage = ResultsPerPage::DEFAULT_CHOICE;
		}
		$nbResults = CrawledContent::searchCount($q);
		$nbPages = ceil($nbResults / $resultsPerPage);
		if($page > $nbPages)
		{
			$page = 1;
		}
		$keepResultsPerPage = $resultsPerPage == ResultsPerPage::DEFAULT_CHOICE ? '' : '/' . $resultsPerPage;
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

}