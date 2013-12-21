<?php

class HomeController extends BaseController {

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

	public function searchResult($page = 1, $q = null)
	{
		$q = is_null($q) ? Request::get('q') : urldecode($q);
		return View::make('result')->with([
			'q' => $q,
			'nbPages' => 4,
			'currentPage' => $page,
			'pageUrl' => '/%d/'.urlencode($q),
			'results' => CrawledContent::search($q)->get()
		]);
	}

}