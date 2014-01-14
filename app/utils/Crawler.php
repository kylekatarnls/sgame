<?php

/**
 * Contenu récupéré par le crawler
 */
class Crawler {

	const RECURSION_LIMIT = 6;
	const ADDED = 0x01;
	const UPDATED = 0x02;
	const DUPLICATED = 0x03;
	const NOT_FOUND = 0xff;

	static protected $links = array();
	static protected $log = '';

    /**
     *  Retourne et vide le contenu des logs (permet de différer l'affichage des logs ou de les passer sous silence)
     *  @Return static::$log
     */
	static public function getLog()
	{
		$log = static::$log;
		static::$log = '';
		return $log;
	}

    /**
     *  Transforme une URL relative en URL complete
     *  @Return url complete
     *  ex: realLink("http://www.google.com/contact", "/home")="http://www.google.com/home"
     */
	static protected function realLink($from, $to)
	{
		switch(substr($to, 0, 1))
		{
			case '':
			case '#':
				return $from;
			case '/':
				return preg_replace('#^([a-z0-9]+://[^/]+)(/.*)$#i', '$1', $from).$to;
			case ':':
				return preg_replace('#^([a-z0-9]+):.*$#i', '$1', $from).$to;
			case '?':
				return preg_replace('#^([^\?]+)(\?.*)$#i', '$1', $from).$to;
			default:
				if(preg_match('#^[a-z0-9]+:#', $to))
				{
					return $to;
				}
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

    /**
     *  Recupere les informations utiles d'une page
     */
	static public function getDataFromUrl($url, $followLinks = false, $recursions = 0)
	{
		self::$links[] = $url;
		try
		{
			$fileGetContents = file_get_contents($url);
		}
		catch(ErrorException $e)
		{
			return null;
		}
		if(stripos($fileGetContents, '<html') === false)
		{
			return null;
		}
		$title = preg_match('#<title.*>(.+)</title>#isU', $fileGetContents, $match) ?
			trim(strip_tags($match[1])) :
			e($url);
		$language = preg_match('#(?<![a-z-])lang\s*=\s*([a-z-]+|"[^"]+"|\'[^\']+\')#is', $fileGetContents, $match) ?
			trim(strip_tags($match[1]), '\'"') :
			null;
		if($recursions > self::RECURSION_LIMIT)
		{
			$content = '';
		}
		else
		{
			if($followLinks)
			{
				preg_match_all('#<a[^>]*href\s*=\s*[\'"](.+)[\'"]#isU', $fileGetContents, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
				foreach($matches[1] as $couple)
				{
					list($link, $offset) = $couple;
					$link = self::realLink($url, $link);
					self::$log .= $link . " : " . $offset . "\n";
					if(!in_array($link, self::$links))
					{
						if(self::scanUrl($link, true, $recursions + 1) === self::NOT_FOUND)
						{
							self::$log .= "Lien mort: " . $link . " page: " . $url . " ligne: " . (substr_count($fileGetContents, "\n", 0, $offset) + 1) . "\n";
						}
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
			$self = __CLASS__;
			$fileGetContents = preg_replace_callback(
				'#<i?frame[^>]*src\s*=\s*[\'"](.+)[\'"][^>]*>.+</i?frame>#isU',
				function ($match) use($recursions, $self)
				{
					$data = $self::getDataFromUrl($match[1], false, $recursions + 1);
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
			'content' => $content,
			'language' => $language
		);
	}

    /**
     *  Methode d'entrée du crawler
     *  @Return etat de la page (ex: not found)
     */
	static public function scanUrl($url, $followLinks = false, $recursions = 0)
	{
		$data = self::getDataFromUrl($url, $followLinks, $recursions);
		if(is_null($data))
		{
			return self::NOT_FOUND;
		}
		if(!mb_check_encoding($data['content'], 'UTF-8'))
		{
			$data['title'] = utf8_encode($data['title']);
			$data['content'] = utf8_encode($data['content']);
		}
		if($crawledContent = CrawledContent::where('url', $url)->first())
		{
			$title = $data['title'];
			$content = $data['content'];
			$crawledContent->title = $title;
			$crawledContent->content = $content;
			$crawledContent->language = $data['language'];
			$crawledContent->save();
			Cache::put('CrawledContent-'.$crawledContent->id.'-title', $title, CrawledContent::REMEMBER);
			Cache::put('CrawledContent-'.$crawledContent->id.'-content', $content, CrawledContent::REMEMBER);
			return self::UPDATED;
		}
		elseif(CrawledContent::where('content', $data['content'])->where('title', $data['title'])->exists())
		{
			return self::DUPLICATED;
		}
		else
		{
			CrawledContent::create($data);
			return self::ADDED;
		}
	}

    /**
     * @Return nombre de liens scannés depuis le lancement du script
     */
	static public function countLinks()
	{
		return count(self::$links);
	}
}

?>