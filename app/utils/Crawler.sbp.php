<?php

/**
 * Contenu récupéré par le crawler
 */
Crawler

	RECURSION_LIMIT = 6;
	ADDED = 0x01;
	UPDATED = 0x02;
	DUPLICATED = 0x03;
	NOT_FOUND = 0xff;

	s* $links = array();
	s* $log = '';

	/**
	 *  Retourne et vide le contenu des logs (permet de différer l'affichage des logs ou de les passer sous silence)
	 *  @return static::$log
	 */
	s+ getLog
		$log = static::$log;
		static::$log = '';
		< $log;

	/**
	 *  Transforme une URL relative en URL complete
	 *  @return url complete
	 *  ex: realLink("http://www.google.com/contact", "/home")="http://www.google.com/home"
	 */
	s* realLink $from, $to
		$slashDomain = f° $url
			if substr_count($url, '/') < 3
				$url .= '/';
			< $url;
		;
		substr($to, 0, 1) :=
			'' ::
			'#' ::
				< $from;
			'/' ::
				$from = preg_replace('#\?.*$#', '', $from);
				$from = preg_replace('#\#.*$#', '', $from);
				< preg_replace('#^([a-z0-9]+://[^/]+)(/.*)$#i', '$1', $from).$to;
			':' ::
				< preg_replace('#^([a-z0-9]+):.*$#i', '$1', $from).$to;
			'?' ::
				$from = preg_replace('#\#.*$#', '', $from);
				< $slashDomain(preg_replace('#^([^\?]+)(\?.*)$#i', '$1', $from)).$to;
			d:
				if preg_match('#^[a-z0-9]+:#', $to)
					< $to;
				$to = $slashDomain(preg_replace('#[^/]+$#i', '', $from)).$to;
				$to = explode('?', $to);
				$to[0] = str_replace('/.', '', $to[0]);
				$to[0] = preg_replace('#(?<![^/]/)[^/]+/\.\.#', '', $to[0]);
				$to = implode('?', $to);
				< $to;

	/**
	 *  Recupere les informations utiles d'une page
	 */
	s+ getDataFromUrl $url, $followLinks = false, $recursions = 0
		self::$links[] = $url;
		if filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED) === false
			< null;
		try
			$fileContent = file_get_contents($url);
		catch ErrorException $e
			< null;
		if stripos($fileContent, '<html') === false
			< null;
		$title = preg_match('#<title.*>(.+)</title>#isU', $fileContent, $match) ?
			trim(strip_tags($match[1])) :
			e($url);
		$language = preg_match('#(?<![a-z-])lang\s*=\s*([a-z-]+|"[^"]+"|\'[^\']+\')#is', $fileContent, $match) ?
			trim(strip_tags($match[1]), '\'"') :
			null;
		if $recursions > :RECURSION_LIMIT
			$content = '';
		else
			if $followLinks
				preg_match_all('#<a[^>]*href\s*=\s*[\'"](.+)[\'"]#isU', $fileContent, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
				foreach $matches[1] as $couple
					list($link, $offset) = $couple;
					$link = self::realLink($url, $link);
					if !in_array($link, self::$links)
						$s = self::scanUrl($link, true, $recursions + 1);
						if $s === :NOT_FOUND
							self::$log .= "Lien mort: " . $link .
								" page: " . $url .
								" ligne: " . (substr_count($fileContent, "\n", 0, $offset) + 1) .
								" colonne : " . ($offset - strrpos(substr($fileContent,0,$offset),"\n") - 1) .
								"\n";
			$self = __CLASS__;
			replace(**$fileContent,
				array(
					'#(<\/?)(h[1-6]|b|em|strong)([^a-z0-9][^>]*)?>#isU' => ' $1strong> ',
					// Remplace les balises importantes (h1...h6, em, b, strong) avec ou sans paramètres par une balise <strong> sans paramètres
					// On rajoute au passage des espaces autour de la balise pour que l'index FULLTEXT repère correctement les séparation de mots
					'><' => '> <',
					'#<img\s.*alt\s*=\s*[\'"](.+)[\'"].*>#isU' => '$1',
					'#<script[^>]*>.+</script>|<style[^>]*>.+</style>#isU' => '',
					'#<style[^>]*>.+</style>#isU' => '',
					'#<i?frame[^>]*src\s*=\s*[\'"](.+)[\'"][^>]*>.+</i?frame>#isU' => f° $match use $recursions, $self
						$data = $self::getDataFromUrl($match[1], false, $recursions + 1);
						< is_null($data) ? '' : array_get($data, 'content');
					,
				)
			);
			$content = trim(strip_tags(
				preg_match('#<body[^>]*>(.+)</body>#isU', $fileContent, $match) ?
					$match[1] :
					$fileContent,
				'<strong>'
			));
			replace(**$content,
				array(
					'&nbsp;' => ' ',
					'#\s{2,}#' => ' ',
				)
			);
		< array(
			'url' => $url,
			'title' => $title,
			'content' => $content,
			'language' => $language
		);

	/**
	 *  Methode d'entrée du crawler
	 *  @return etat de la page (ex: not found)
	 */
	s+ scanUrl $url, $followLinks = false, $recursions = 0
		$data = self::getDataFromUrl($url, $followLinks, $recursions);
		if is_null($data)
			< :NOT_FOUND;
		foreach $data as &$string
			if !mb_check_encoding($string, 'UTF-8') and mb_check_encoding(utf8_encode($string), 'UTF-8')
				utf8_encode(**$string);
			if !mb_check_encoding($string, 'UTF-8') and mb_check_encoding(utf8_decode($string), 'UTF-8')
				utf8_decode(**$string);
		if $crawledContent = CrawledContent::where('url', $url)->first()
			$title = $data['title'];
			$content = $data['content'];
			$crawledContent->title = $title;
			$crawledContent->content = $content;
			$crawledContent->language = $data['language'];
			$crawledContent->save();
			Cache::put('CrawledContent-'.$crawledContent->id.'-title', $title, ModelBuilder::REMEMBER);
			Cache::put('CrawledContent-'.$crawledContent->id.'-content', $content, ModelBuilder::REMEMBER);
			< :UPDATED;
		else if CrawledContent::where('content', $data['content'])->where('title', $data['title'])->exists()
			< :DUPLICATED;
		else
			CrawledContent::create($data);
			< :ADDED;

	/**
	 * @return nombre de liens scannés depuis le lancement du script
	 */
	s+ countLinks
		< count(self::$links);

?>