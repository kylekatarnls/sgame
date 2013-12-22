<?php

/*
 * Contenu récupéré par le crawler
 */
class CrawledContent extends Eloquent {

	protected $collection = 'crawled_content';

	static public function search($value = '')
	{
		$pattern = '%'.addcslashes($value, '_%').'%';
		return self::where('content', 'like', $pattern)
					->orWhere('title', 'like', $pattern)
					->orWhere('url', 'like', $pattern);
	}
}

?>