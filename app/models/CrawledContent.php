<?php

/**
 * Contenu récupéré par le crawler
 */
class CrawledContent extends Eloquent {

	const REMEMBER = 10;

	protected $collection = 'crawled_content';
	protected $softDelete = true;
	protected $fillable = array('url', 'title', 'content');

	static protected $lastQuerySearch = '';

	static public function pgSearch($value)
	{
		if($value === '')
		{
			return self::whereRaw('1 = 0');
		}
		self::$lastQuerySearch = urlencode($value);
		$values = preg_split('#\s+#', $value);
		foreach($values as $value)
		{
			$where = "searchtext @@ to_tsquery(" . DB::getPdo()->quote($value) . ")";
			$result = isset($result) ?
				$result->orWhereRaw($where) :
				self::whereRaw($where);
		}
		// Insérer ici le tri par pertinence et la jointure avec la table key_words
		return $result
			//->orderBy('score', 'desc')
			->remember(self::REMEMBER);
	}

	static protected function eachLike(&$result = null, $value='')
	{
		$like = 'LIKE ' . DB::getPdo()->quote('%' . addcslashes(strtolower($value), '_%') . '%');
		$self = new self;
		foreach($self->fillable as $column)
		{
			$result = is_null($result) ?
				self::whereRaw('LOWER(' . $column . ')' . $like) :
				$result->orWhereRaw('LOWER(' . $column . ')' . $like);	
		}
	}

	static public function likeSearch($value)
	{
		if($value === '')
		{
			return self::whereRaw('1 = 0');
		}
		self::$lastQuerySearch = urlencode($value);
		$values = preg_split('#\s+#', $value);
		foreach($values as $value)
		{
			self::eachLike($result, $value);
		}
		return $result
			->remember(self::REMEMBER);
	}

	static public function search($value = '')
	{
		return Config::get('database.default') === 'pgsql' ?
			self::pgSearch($value) :
			self::likeSearch($value);
	}

	static public function getSearchResult($query, $page = null, $resultsPerPage = null)
	{
		$result = self::search($query)
			->select('crawled_contents.id', 'url', 'title', 'content', DB::raw('COUNT(log_outgoing_links.id) AS count'))
			->leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
			//->orderBy('score', 'desc')
        	->groupBy('crawled_contents.id');
		// Insérer ici le tri par pertinence et la jointure avec la table key_words
		if(!is_null($resultsPerPage))
		{
			$result = $result->forPage($page, $resultsPerPage);
		}
		return $result->get();
	}

	static public function searchCount($query)
	{
		return self::search($query)->count();
	}

	public function keyWords()
	{
		return $this->belongsToMany('KeyWord');
	}

	public function scan()
	{
		scanUrl($this->attributes['url']);
	}

	public function getOutgoingLinkAttribute()
	{
		return '/out/'. self::$lastQuerySearch . '/' . $this->id;
	}

	public function getCountAttribute()
	{
		return Cache::get('crawled_content_id:'.$this->id.'_log_outgoing_link_count', $this->attributes['count']);
	}

	public function getContentAttribute()
	{
		$content = trim(Cache::get('CrawledContent-'.$this->id.'-content', $this->attributes['content']));
		if(strlen($content) > 800)
		{
			$content = substr($content, 0, 800);
			$content = substr($content, 0, strrpos($content, ' ')).'...';
		}
		return $content;
	}

	public function getTitleAttribute()
	{
		return Cache::get('CrawledContent-'.$this->id.'-title', $this->attributes['title']);
	}

}

/**
 * Observateur des contenus enregistrés
 */
class CrawledContentObserver {

	public function saved($contentCrawled)
	{
		preg_match_all('#<strong>(.+)</strong>#sU', $contentCrawled->content, $matches);
		$words = explode(' ', preg_replace('#\s+#', ' ', implode(' ', $matches[1])));
		$ids = array();
		foreach($words as $word)
		{
			$word = preg_replace('#[^a-z0-9_-]#', '', normalize($word, true));
			if($word !== '')
			{
				$keyWord = KeyWord::firstOrCreate(array(
					'word' => $word
				));
				$ids[] = $keyWord->id;
			}
		}
		$contentCrawled->keyWords()->sync($ids);
	}

}

CrawledContent::observe(new CrawledContentObserver);

?>