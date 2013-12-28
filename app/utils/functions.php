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

function scanUrl($url, $followLinks = false)
{
	return Crawler::scanUrl($url, $followLinks);
}

?>