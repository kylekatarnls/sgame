<?php

/**
 * Mot clé associé aux CrawledContent
 */
class KeyWord extends Eloquent {

	public function crawledContents()
	{
		return $this->belongsToMany('CrawledContent');
	}

}

?>