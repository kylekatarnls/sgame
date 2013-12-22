<?php

/*
 * Contenu récupéré par le crawler
 */
class CrawledContent extends Eloquent {

	protected $collection = 'crawled_content';
	protected $softDelete = true;
	protected $fillable = ['url', 'title', 'content'];

	static protected $lastQuerySearch = '';

	static public function search($value = '')
	{
		self::$lastQuerySearch = urlencode($value);
		$like = 'LIKE '.DB::getPdo()->quote('%'.addcslashes(strtolower($value), '_%').'%');
		return self::whereRaw('LOWER(content)'.$like)
					->orWhereRaw('LOWER(title)'.$like)
					->orWhereRaw('LOWER(url)'.$like);
	}

	public function getOutgoingLink()
	{
		return '/out/'. self::lastQuerySearch . '/' . $this->id;
	}
}

?>