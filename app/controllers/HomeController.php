<?php

class HomeController extends BaseController {

	public function searchBar()
	{
		return View::make('home');
	}

	public function searchResult($page = 1, $q = null, $resultsPerPage = null)
	{
		$q = is_null($q) ? Request::get('q', $page) : urldecode($q);
		$data = CrawledContent::getSearchResult($q)
			->paginatedData($page, $resultsPerPage, array(
				'q' => $q,
				'pageUrl' => '/%d/'.urlencode($q).'{keepResultsPerPage}',
				'resultsPerPageUrl' => '/'.$page.'/'.urlencode($q).'/%d'
			));
		LogSearch::log($q, $data['nbResults']);
		return View::make('result')->with($data);
	}

	public function goOut($search_query, $id)
	{
		$result = CrawledContent::find($id);
		if(!$result)
		{
			App::abort(404);
		}

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
		return View::make('result')->with(
			CrawledContent::popular()
				->select(
					'crawled_contents.id',
					'url', 'title', 'content', 'language',
					DB::raw('COUNT(log_outgoing_links.id) AS count')
				)
				->orderBy('count', 'desc')
				->paginatedData($page, $resultsPerPage, array(
					'q' => '',
					'pageUrl' => '/most-popular/%d{keepResultsPerPage}',
					'resultsPerPageUrl' => '/most-popular/'.$page.'/%d'
				))
			);
	}

	public function history($page, $resultsPerPage = null)
	{
		return View::make('history')->with(
			LogSearch::mine()
				->paginatedData($page, $resultsPerPage, array(
					'q' => '',
					'pageUrl' => '/history/%d{keepResultsPerPage}',
					'resultsPerPageUrl' => '/history/'.$page.'/%d'
				))
			);
	}

}