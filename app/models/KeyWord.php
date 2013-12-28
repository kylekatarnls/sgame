<?php

/**
 * Mot clé associé aux CrawledContent
 */
class KeyWord extends Eloquent {

	protected $collection = 'key_word';
	protected $fillable = array('word');

	public function crawledContents()
	{
		return $this->belongsToMany('CrawledContent');
	}

}

?>