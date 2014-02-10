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

function normalize($string, $lowerCase = true)
{
	$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	$string = utf8_decode($string);
	$string = strtr($string, utf8_decode($a), $b);
	if($lowerCase)
	{
		$string = strtolower($string);
	}
	$string = utf8_encode($string);
	return $string;
}

function array_maps($maps, array $array)
{
	if(!is_array($maps))
	{
		$maps = explode(',', $maps);
	}
	foreach($maps as $map)
	{
		$array = array_map($map, $array);
	}
	return $array;
}

function scanUrl($url, $followLinks = false, $recursions = 0)
{
	return Crawler::scanUrl($url, $followLinks, $recursions);
}

function ip2bin($ip = null)
{
	return bin2hex(inet_pton(is_null($ip) ? Request::getClientIp() : $ip));
}

function replace($replacement, $to, $string = null)
{
	if(is_null($string))
	{
		if(!is_array($replacement))
		{
			throw new InvalidArgumentException("Signatures possibles : string, string, string / array, string / array, string, string");
			return false;
		}
		$string = $to;
		$to = null;
	}
	if(!is_null($to))
	{
		$replacement = (array) $replacement;
		$to = (array) $to;
		$count = count($replacement);
		$countTo = count($to);
		if($count < $countTo)
		{
			$to = array_slice($to, 0, $count);
		}
		else if($count > $countTo)
		{
			$last = last($to);
			for($i = $countTo; $i < $count; $i++)
			{
				array_push($to, $last);
			}
		}
		$replacement = array_combine((array) $replacement, (array) $to);
	}
	foreach($replacement as $from => $to)
	{
		if(is_callable($to))
		{
			$string = preg_replace_callback($from, $to, $string);
		}
		else
		{
			try
			{
				// Si possible, on utilise les RegExep
				$string = preg_replace($from, $to, $string);
			}
			catch(ErrorException $e)
			{
				// Sinon on rempalcement simplement la chaîne
				$string = str_replace($from, $to, $string);
			}
		}
	}
	return $string;
}

function accents2entities($string)
{
	return strtr($string, array(
		'é' => '&eacute;',
		'è' => '&egrave;',
		'ê' => '&ecirc;',
		'ë' => '&euml;',
		'à' => '&agrave;',
		'ä' => '&auml;',
		'ù' => '&ugrave;',
		'û' => '&ucirc;',
		'ü' => '&uuml;',
		'ô' => '&ocirc;',
		'ò' => '&ograve;',
		'ö' => '&ouml;',
		'ï' => '&iuml;',
		'ç' => '&ccedil;',
		'ñ' => '&ntild;',
		'É' => '&Eacute;',
	));
}

function utf8($string)
{
	$string = str_replace('Ã ', '&agrave; ', $string);
	if(strpos($string, 'Ã') !== false and strpos(utf8_decode($string), 'Ã') === false)
	{
		$string = utf8_decode(accents2entities($string));
	}
	if(!mb_check_encoding($string, 'UTF-8') and mb_check_encoding(utf8_encode($string), 'UTF-8'))
	{
		$string = utf8_encode(accents2entities($string));
	}
	return $string;
}

if(!function_exists('http_negotiate_language'))
{
	function http_negotiate_language($available_languages, &$result = null)
	{
		$http_accept_language = Request::server('HTTP_ACCEPT_LANGUAGE', '');
		preg_match_all(
			"/([[:alpha:]]{1,8})(-([[:alpha:]|-]{1,8}))?" .
			"(\s*;\s*q\s*=\s*(1\.0{0,3}|0\.\d{0,3}))?\s*(,|$)/i",
			$http_accept_language,
			$hits,
			PREG_SET_ORDER
		);
		$bestlang = $available_languages[0];
		$bestqval = 0;
		foreach($hits as $arr)
		{
			$langprefix = strtolower($arr[1]);
			if(!empty($arr[3]))
			{
				$langrange = strtolower($arr[3]);
				$language = $langprefix . "-" . $langrange;
			}
			else
			{
				$language = $langprefix;
			}
			$qvalue = 1.0;
			if(!empty($arr[5]))
			{
				$qvalue = floatval($arr[5]);
			}
			if(in_array($language, $available_languages) && ($qvalue > $bestqval))
			{
				$bestlang = $language;
				$bestqval = $qvalue;
			}
			else if(in_array($langprefix, $available_languages) && (($qvalue*0.9) > $bestqval))
			{
				$bestlang = $langprefix;
				$bestqval = $qvalue*0.9;
			}
		}
		return $bestlang;
	}
}

?>