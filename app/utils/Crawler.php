<?php

/**
 * Contenu récupéré par le crawler
 */
class Crawler {

	const RECURSION_LIMIT = 6;

	static public function getDataFromUrl($url, $recursions = 0, $followLinks = false)
	{
		$fileGetContents = file_get_contents($url);
		$title = preg_match('#<title.*>(.+)</title>#isU', $fileGetContents, $match) ?
			trim(strip_tags($match[1])) :
			e($url);
		if($recursions > self::RECURSION_LIMIT)
		{
			$content = '';
		}
		else
		{
			if($followLinks)
			{
				preg_match_all('#<a[^>]*href\s*=\s*[\'"](.+)[\'"]#isU', $fileGetContents, $matches);
				foreach($matches[1] as $link)
				{
					self::scanUrl($link, $recursions + 1, true);
				}
			}
			$fileGetContents = preg_replace('#(<\/?)(h[1-6]|b|em)[^>]*>#', '$1strong>', $fileGetContents);
			$fileGetContents = str_replace('><', '> <', $fileGetContents);
			$fileGetContents = preg_replace('#<img\s.*alt\s*=\s*[\'"](.+)[\'"].*>#isU', '$1', $fileGetContents);
			$fileGetContents = preg_replace('#<script[^>]*>.+</script>#isU', '', $fileGetContents);
			$fileGetContents = preg_replace('#<style[^>]*>.+</style>#isU', '', $fileGetContents);
			$fileGetContents = preg_replace_callback(
				'#<i?frame[^>]*src\s*=\s*[\'"](.+)[\'"][^>]*>.+</i?frame>#isU',
				function ($match) use($recursions)
				{
					return array_get(self::getDataFromUrl($match[1], $recursions + 1), 'content');
				},
				$fileGetContents
			);
			$content = trim(strip_tags(
				preg_match('#<body.*>(.+)</body>#isU', $fileGetContents, $match) ?
					$match[1] :
					$fileGetContents,
				'<strong>'
			));
			$content = preg_replace('#\s{2,}#', ' ', str_replace('&nbsp;', ' ', $content));
		}
		return array(
			'url' => $url,
			'title' => $title,
			'content' => $content
		);
	}

	static public function scanUrl($url, $followLinks = false)
	{
		$data = self::getDataFromUrl($url, 0, $followLinks);
		$crawledContent = CrawledContent::where('url', $url)->first();
		if($crawledContent)
		{
			$title = $data['title'];
			$content = $data['content'];
			$crawledContent->title = $title;
			$crawledContent->content = $content;
			$crawledContent->save();
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
}

?>