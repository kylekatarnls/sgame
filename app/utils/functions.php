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