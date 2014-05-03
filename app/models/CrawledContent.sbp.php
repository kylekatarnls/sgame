<?

/**
 * Contenu récupéré par le crawler
 */
CrawledContent:Model

	* $collection = 'crawled_content';
	* $softDelete = true;
	* $fillable = array('url', 'title', 'content', 'language');

	SAME_LANGUAGE = 8;
	SAME_PRIMARY_LANGUAGE = 4;

	/**
	 * Retourne les résultats d'une recherche
	 *
	 * @param string $query : l'expression à rechercher
	 *
	 * @return CrawledContent $resultsContainigQuery
	 */
	s+ getSearchResult $query
		$calledClass = get_called_class();
		<self::search($query, $values) // $values contient les mots contenus dans la chaîne $query sous forme d'array
			->select(
				'crawled_contents.id',
				'url', 'title', 'content', 'language', 'deleted_at',
				DB::raw('COUNT(log_outgoing_links.id) AS count'),
				DB::raw(
					self::caseWhen(DB::raw('language'), array(
						Lang::locale() => :SAME_LANGUAGE
					), 0) . ' + ' .
					self::caseWhen(self::substr(DB::raw('language'), 1, 2), array(
						substr(Lang::locale(), 0, 2) => :SAME_PRIMARY_LANGUAGE
					), 0) . ' +
					COUNT(DISTINCT key_words.id) * ' . :KEY_WORD_SCORE . ' + ' .
					self::findAndCount(DB::raw('content'), $query).' * ' . :COMPLETE_QUERY_SCORE . ' + '.
					self::findAndCount(DB::raw('content'), $values).' * ' . :ONE_WORD_SCORE . '
					AS score
				')
			)
			->leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
			->leftJoin('crawled_content_key_word', 'crawled_content_key_word.crawled_content_id', '=', 'crawled_contents.id')
			->leftJoin('key_words', f° $join use $calledClass, $values
				$join->on('crawled_content_key_word.key_word_id', '=', 'key_words.id')
					->on('key_words.word', 'in', DB::raw('(' . implode(', ', array_maps(array('normalize', 'strtolower', array($calledClass, 'quote')), $values)) . ')'));
			)
			->groupBy('crawled_contents.id')
			->orderBy('score', 'desc');

	/**
	 * Retourne les résultats sur lesquels quelqu'un a déjà cliqué au moins une fois (lié à 1 ou plusieurs LogOutgoingLink)
	 *
	 * @return CrawledContent $popularResults
	 */
	s+ popular
		<static::leftJoin('log_outgoing_links', 'log_outgoing_links.crawled_content_id', '=', 'crawled_contents.id')
			->whereNotNull('log_outgoing_links.id')
			->groupBy('crawled_contents.id');

	+ keyWords
		<>belongsToMany('KeyWord');

	+ scan
		scanUrl(>attributes['url']);

	+ getOutgoingLinkAttribute
		<'/out/'. (empty(self::$lastQuerySearch) ? '-' : self::$lastQuerySearch) . '/' . $this->id;

	+ getUrlAndLanguageAttribute
		<>url . (empty($this->language) ? '' : '(' . $this->language . ')');

	+ link $label, array $attributes = array()
		<HTML::link($this->outgoingLink, $label, $attributes);

	+ getCountAttribute
		<Cache::get('crawled_content_id:' . $this->id . '_log_outgoing_link_count', array_get($this->attributes, 'count', 0));

	+ resume $length = 800
		$content = trim(Cache::get('CrawledContent-' . $this->id . '-content', array_get($this->attributes, 'content', '')));
		if strlen($content) > $length
			substr(**$content, 0, $length);
			substr(**$content, 0, strrpos($content, ' ')) . '...';
		$closeStrongTag = substr_count($content, '<strong>') - substr_count($content, '</strong>');
		$content .= str_repeat('</strong>', $closeStrongTag);
		<utf8($content);

	+ getContentAttribute
		<>resume();

	+ getTitleAttribute
		<utf8(Cache::get('CrawledContent-' . $this->id . '-title', array_get($this->attributes, 'title', '')));

