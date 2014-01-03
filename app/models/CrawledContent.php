<?php

/**
 * Contenu récupéré par le crawler
 */
class CrawledContent extends Searchable {

	protected $collection = 'crawled_content';
	protected $softDelete = true;
	protected $fillable = array('url', 'title', 'content', 'language');

	const SAME_LANGUAGE = 8;
	const SAME_PRIMARY_LANGUAGE = 4;

	static public function getSearchResult($query, $page = null, $resultsPerPage = null)
	{
		$result = self::search($query, $values) // $values contient les mots contenus dans la chaîne $query sous forme d'array
			->select(
				'crawled_contents.id',
				'url', 'title', 'content', 'language',
				DB::raw('COUNT(log_outgoing_links.id) AS count'),
				DB::raw('
					(CASE language WHEN \'' . Lang::locale() . '\' THEN ' . self::SAME_LANGUAGE . ' ELSE 0 END) +
					(CASE SUBSTR(language, 0, 3) WHEN \'' . substr(Lang::locale(), 0, 2) . '\' THEN ' . self::SAME_PRIMARY_LANGUAGE . ' ELSE 0 END) +
					COUNT(DISTINCT key_words.id) * ' . self::KEY_WORD_SCORE . ' +
					1 * ' . self::COMPLETE_QUERY_SCORE . ' +
					1 * ' . self::ONE_WORD_SCORE . '
					AS score
				')
				/*-
				 * Ici, il reste à complérer le calcul du score en y incluant :
				 *  - la détection de la recherche complète (si la recherche contient plusieurs mots)
				 *  - la détection de chaque mot
				 * Le tout devra si possible être compatible avec PostgreSQL, MySQL, SQLite et Oracle
				 */
			)
			->leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
			->leftJoin('crawled_content_key_word', 'crawled_content_key_word.crawled_content_id', '=', 'crawled_contents.id')
			->leftJoin('key_words', 'crawled_content_key_word.key_word_id', '=', 'key_words.id')
			->whereIn('key_words.word', array_maps('normalize,strtolower', $values))
        	->groupBy('crawled_contents.id')
			->orderBy('score', 'desc');
		if(!is_null($resultsPerPage))
		{
			$result = $result->forPage($page, $resultsPerPage);
		}
		return $result->get();
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
		$words = array_unique(explode(' ', preg_replace('#\s+#', ' ', trim(implode(' ', $matches[1])))));
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