<?php

/**
 * Contenu récupéré par le crawler
 */
class CrawledContent extends Model {

	protected $collection = 'crawled_content';
	protected $softDelete = true;
	protected $fillable = array('url', 'title', 'content', 'language');

	const SAME_LANGUAGE = 8;
	const SAME_PRIMARY_LANGUAGE = 4;

	/**
	 * Retourne les résultats d'une recherche
	 *
	 * @param string $query : l'expression à rechercher
	 *
	 * @return CrawledContent $resultsContainigQuery
	 */
	static public function getSearchResult($query)
	{
		$calledClass = get_called_class();
		return self::search($query, $values) // $values contient les mots contenus dans la chaîne $query sous forme d'array
			->select(
				'crawled_contents.id',
				'url', 'title', 'content', 'language', 'deleted_at',
				DB::raw('COUNT(log_outgoing_links.id) AS count'),
				DB::raw(
					self::caseWhen(DB::raw('language'), array(
						Lang::locale() => self::SAME_LANGUAGE
					), 0) . ' + ' .
					self::caseWhen(self::substr(DB::raw('language'), 1, 2), array(
						substr(Lang::locale(), 0, 2) => self::SAME_PRIMARY_LANGUAGE
					), 0) . ' +
					COUNT(DISTINCT key_words.id) * ' . self::KEY_WORD_SCORE . ' + ' .
					self::findAndCount(DB::raw('content'), $query).' * ' . self::COMPLETE_QUERY_SCORE . ' + '.
					self::findAndCount(DB::raw('content'), $values).' * ' . self::ONE_WORD_SCORE . '
					AS score
				')
			)
			->leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
			->leftJoin('crawled_content_key_word', 'crawled_content_key_word.crawled_content_id', '=', 'crawled_contents.id')
			->leftJoin('key_words', function ($join) use($calledClass, $values)
			{
				$join->on('crawled_content_key_word.key_word_id', '=', 'key_words.id')
					->on('key_words.word', 'in', DB::raw('(' . implode(', ', array_maps(array('normalize', 'strtolower', array($calledClass, 'quote')), $values)) . ')'));
			})
        	->groupBy('crawled_contents.id')
			->orderBy('score', 'desc');
	}

	/**
	 * Retourne les résultats sur lesquels quelqu'un a déjà cliqué au moins une fois (lié à 1 ou plusieurs LogOutgoingLink)
	 *
	 * @return CrawledContent $popularResults
	 */
	static public function popular()
	{
		return static::leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
			->whereNotNull('log_outgoing_links.id')
			->groupBy('crawled_contents.id');
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

	public function getUrlAndLanguageAttribute()
	{
		return $this->url . (empty($this->language) ? '' : '(' . $this->language . ')');
	}

	public function link($label, array $attributes = array())
	{
		return HTML::link($this->outgoingLink, $label, $attributes);
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
		$closeStrongTag = substr_count($content, '<strong>') - substr_count($content, '</strong>');
		$content .= str_repeat('</strong>', $closeStrongTag);
		return utf8($content);
	}

	public function getTitleAttribute()
	{
		return utf8(Cache::get('CrawledContent-'.$this->id.'-title', $this->attributes['title']));
	}

}


?>