<?php

function §()
{
	$args = func_get_args();
	if(isset($args[1]) && is_numeric($args[1]))
	{
		return call_user_func_array('trans_choice', $args);
	}
	return call_user_func_array('trans', $args);
}

function normalize($string, $lowerCase = false)
{
	$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = utf8_decode($string);
	$string = strtr($string, utf8_decode($a), $b);
	$string = strtolower($string);
	if($lowerCase)
	{
		$string = strtolower($string);
	}
	$string = utf8_encode($string);
	return $string;
} 

function getDataFromUrl($url, $recursions = 0, $followLinks = false)
{
	$fileGetContents = file_get_contents($url);
	$title = preg_match('#<title.*>(.+)</title>#isU', $fileGetContents, $match) ?
		trim(strip_tags($match[1])) :
		e($url);
	if($followLinks)
	{

	}
	if($recursions > 10)
	{
		$content = '';
	}
	else
	{
		$fileGetContents = str_replace('><', '> <', $fileGetContents);
		$fileGetContents = preg_replace('#<img\s.*alt\s*=\s*[\'"](.+)[\'"].*>#isU', '$1', $fileGetContents);
		$fileGetContents = preg_replace('#<script[^>]*>.+</script>#isU', '', $fileGetContents);
		$fileGetContents = preg_replace('#<style[^>]*>.+</style>#isU', '', $fileGetContents);
		$fileGetContents = preg_replace_callback(
			'#<i?frame[^>]*src\s*=\s*[\'"](.+)[\'"][^>]*>.+</i?frame>#isU',
			function ($match) use($recursions)
			{
				return array_get(getDataFromUrl($match[1], $recursions + 1), 'content');
			},
			$fileGetContents
		);
		$content = trim(strip_tags(
			preg_match('#<body.*>(.+)</body>#isU', $fileGetContents, $match) ?
				$match[1] :
				$fileGetContents
		));
		$content = preg_replace('#\s{2,}#', ' ', str_replace('&nbsp;', ' ', $content));
	}
	return array(
		'url' => $url,
		'title' => $title,
		'content' => $content
	);
}

function scanUrl($url, $followLinks = false)
{
	$data = getDataFromUrl($url, 0, $followLinks);
	$crawledContent = CrawledContent::where('url', $url)->first();
	if($crawledContent)
	{
		$title = $data['title'];
		$content = $data['content'];
		$crawledContent->title = $title;
		$crawledContent->content = $content;
		Cache::put('CrawledContent-'.$crawledContent->id.'-title', $title, CrawledContent::REMEMBER);
		Cache::put('CrawledContent-'.$crawledContent->id.'-content', $content, CrawledContent::REMEMBER);
		return false;
	}
	else
	{
		CrawledContent::create($data);
		return true;
	}
}

?>