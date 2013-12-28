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

	static public function search($value = '')
	{
		if($value === '')
		{
			return self::whereRaw('1=0');
		}
		self::$lastQuerySearch = urlencode($value);
		$like = 'LIKE '.DB::getPdo()->quote('%'.addcslashes(strtolower($value), '_%').'%');
		$values = preg_split('#\s+#', $value);
		$result = self::whereRaw('LOWER(content)'.$like)
					->orWhereRaw('LOWER(title)'.$like)
					->orWhereRaw('LOWER(url)'.$like);

		// Si la recherche contient plusieurs mots
		if(count($values) > 1)
		{
		    foreach($values as $value)
		    {
		        $like = 'LIKE '.DB::getPdo()->quote('%'.addcslashes(strtolower($value), '_%').'%');
	        	$result
	        	    ->orWhereRaw('LOWER(content)'.$like)
    				->orWhereRaw('LOWER(title)'.$like)
    				->orWhereRaw('LOWER(url)'.$like);
		    }
		}
		// Insérer ici le tri par pertinence et la jointure avec la table key_words
		return $result
			//->orderBy('score', 'desc')
			->remember(self::REMEMBER);
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