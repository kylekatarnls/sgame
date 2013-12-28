<?php

function §()
{
	$args = func_get_args();
	if(isset($args[1]) && is_numeric($args[1]))
	{
		return call_user_func_array(array('Lang', 'choice'), $args);
	}
	return call_user_func_array(array('Lang', 'get'), $args);
}

function addUrl($url)
{
	$fileGetContents = file_get_contents($url);
	$title = preg_match('#<title.*>(.+)</title>#isU', $fileGetContents, $match) ?
		trim(strip_tags($match[1])) :
		e($url);
	$fileGetContents = str_replace('><', '> <', $fileGetContents);
	$fileGetContents = preg_replace('#<img\s.*alt\s*=\s*[\'"](.+)[\'"].*>#isU', '$1', $fileGetContents);
	$fileGetContents = preg_replace('#<script[^>]*>.+</script>#isU', '', $fileGetContents);
	$fileGetContents = preg_replace('#<style[^>]*>.+</style>#isU', '', $fileGetContents);
	$content = trim(strip_tags(
		preg_match('#<body.*>(.+)</body>#isU', $fileGetContents, $match) ?
			$match[1] :
			$fileGetContents
	));
	$content = preg_replace('#\s{2,}#', ' ', str_replace('&nbsp;', ' ', $content));
	$crawledContent = CrawledContent::where('url', $url)->first();
	if($crawledContent)
	{
		$crawledContent->title = $title;
		$crawledContent->content = $content;
		Cache::put('CrawledContent-'.$crawledContent->id.'-title', $title, CrawledContent::REMEMBER);
		Cache::put('CrawledContent-'.$crawledContent->id.'-content', $content, CrawledContent::REMEMBER);
		return false;
	}
	else
	{
		CrawledContent::create(array(
			'url' => $url,
			'title' => $title,
			'content' => $content
		));
		return true;
	}
}

if(!class_exists('Memcached') && class_exists('Memcache'))
{
	include_once __DIR__ . DIRECTORY_SEPARATOR . 'EmulateMemcachedWithMemcache.php';
}

?>