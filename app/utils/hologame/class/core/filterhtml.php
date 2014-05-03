<?php

namespace Hologame;

class Core°FilterHtml
{
	static private function filterImgCallback($match)
	{
		$quote = char_at($match[2], 0);
		$src = trim($match[2], $quote);
		if(preg_match('#^((https?)?://|/s[0-9]+/|/c/)#i', $src))
		{
			return $match[0];
		}
		$url = ressource_href($src);
		return $match[1].$quote.($url ?: $src).$quote.$match[3];
	}
	static public function filterImg($input)
	{
		return preg_replace_callback('#(<img.*\ssrc=)("[^"]*"|\'[^\']*\')(.*>)#iU', [get_class(), 'filterImgCallback'], $input);
	}
}

?>