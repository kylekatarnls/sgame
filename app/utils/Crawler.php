<?php

/**
 * Contenu récupéré par le crawler
 */
class Crawler {

	const RECURSION_LIMIT = 6;
	const ADDED = 0x01;
	const UPDATED = 0x02;
	const NOT_FOUND = 0xff;

	static protected $links = array();

	static protected function realLink($from, $to)
	{
		switch(substr($to, 0, 1))
		{
			case '':
			case '#':
				return $from;
			case '/':
				return preg_replace('#^([a-z0-9]+://[^/]+)(/.*)$#i', '$1', $from).$to;
			case '?':
				return preg_replace('#^([^\?]+)(\?.*)$#i', '$1', $from).$to;
			default:
				if(substr_count($from, '/') < 3)
				{
					$from .= '/';
				}
				$to = preg_replace('#[^/]+$#i', '', $from).$to;
				$to = explode('?', $to);
				$to[0] = str_replace('/.', '', $to[0]);
				$to[0] = preg_replace('#(?<![^/]/)[^/]+/\.\.#', '', $to[0]);
				$to = implode('?', $to);
				return $to;
		}
	}

	static public function getDataFromUrl($url, $recursions = 0, $followLinks = false)
	{
		try
		{
			$fileGetContents = file_get_contents($url);
		}
		catch(ErrorException $e)
		{
			return null;
		}
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
					$link = self::realLink($url, $link);
					if(!in_array($link, self::$links))
					{
						self::$links[] = $link;
						self::scanUrl($link, $recursions + 1, true);
					}
				}
			}
			$fileGetContents = preg_replace('#(<\/?)(h[1-6]|b|em|strong)([^a-z0-9][^>]*)?>#isU', ' $1strong> ', $fileGetContents);
			// Remplace les balises importantes (h1...h6, em, b, strong) avec ou sans paramètres par une balise <strong> sans paramètres
			// On rajoute au passage des espaces autour de la balise pour que l'index FULLTEXT repère correctement les séparation de mots
			$fileGetContents = str_replace('><', '> <', $fileGetContents);
			$fileGetContents = preg_replace('#<img\s.*alt\s*=\s*[\'"](.+)[\'"].*>#isU', '$1', $fileGetContents);
			$fileGetContents = preg_replace('#<script[^>]*>.+</script>#isU', '', $fileGetContents);
			$fileGetContents = preg_replace('#<style[^>]*>.+</style>#isU', '', $fileGetContents);
			$fileGetContents = preg_replace_callback(
				'#<i?frame[^>]*src\s*=\s*[\'"](.+)[\'"][^>]*>.+</i?frame>#isU',
				function ($match) use($recursions)
				{
					$data = self::getDataFromUrl($match[1], $recursions + 1);
					return is_null($data) ? '' : array_get($data, 'content');
				},
				$fileGetContents
			);
			$content = trim(strip_tags(
				preg_match('#<body[^>]*>(.+)</body>#isU', $fileGetContents, $match) ?
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
		if(is_null($data))
		{
			return self::NOT_FOUND;
		}
		if(!mb_check_encoding($data['content'], 'UTF-8'))
		{
			$data['title'] = utf8_encode($data['title']);
			$data['content'] = utf8_encode($data['content']);
		}
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
			return self::UPDATED;
		}
		else
		{
			CrawledContent::create($data);
			return self::ADDED;
		}
	}

	static public function countLinks()
	{
		return count(self::$links);
	}
}

?>