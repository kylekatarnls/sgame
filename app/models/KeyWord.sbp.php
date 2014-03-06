<?

/**
 * Mot clé associé aux CrawledContent
 */
KeyWord:Model

	* $collection = 'key_word';
	* $fillable = array('word');

	+ crawledContents
		<>belongsToMany('CrawledContent');

