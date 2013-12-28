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
		self::$lastQuerySearch = urlencode($value);
		$like = 'LIKE '.DB::getPdo()->quote('%'.addcslashes(strtolower($value), '_%').'%');
		return self::whereRaw('LOWER(content)'.$like)
					->orWhereRaw('LOWER(title)'.$like)
					->orWhereRaw('LOWER(url)'.$like)
					->remember(self::REMEMBER);
	}

	public function keyWords()
	{
		return $this->belongsToMany('KeyWord');
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

?>