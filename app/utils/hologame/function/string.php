<?php

function raw($string)
{
	return new \Hologame\Raw($string);
}
function get_string_or_raw($callback, $string)
{
	if($string instanceof \Hologame\Raw)
	{
		return strval($string);
	}
	return call_user_func($callback, $string);
}
function get_array_or_raw($callback, $array)
{
	foreach($array as &$string)
	{
		$string = get_string_or_raw($callback, $string);
	}
	return $array;
}
function encode($html)
{
	return htmlspecialchars($html, ENT_QUOTES);
}
function decode($html)
{
	return htmlspecialchars_decode($html, ENT_QUOTES);
}
function is_upper($string)
{
	return ($string === strtoupper($string));
}
function is_lower($string)
{
	return ($string === strtolower($string));
}
function unix_path($path)
{
	return strtr($path, '\\', '/');
}
function windows_path($path)
{
	return strtr($path, '/', '\\');
}
function sp_path($path, $separator = DIRECTORY_SEPARATOR)
{
	return str_replace(array('/', '\\'), $separator, $path);
}
function end_separator($delimiter, $string, $keep_delimiter = false)
{
	if(is_array($delimiter))
	{
		$position = false;
		foreach($delimiter as $d)
		{
			$p = strrpos($string, $d);
			if($p !== false && ($position === false || $p > $position))
			{
				$position = $p;
			}
		}
	}
	else
	{
		$position = strrpos($string, $delimiter);
	}
	if($position !== false)
	{
		$end = substr($string, $position+($keep_delimiter ? 0 : strlen($delimiter)));
		$string = substr($string, 0, $position);
	}
	else
	{
		$end = '';
	}
	return [$string, $end];
}
function is_in($needle, $haystack)
{
	if(is_object($haystack))
	{
		$haystack = (array) $haystack;
	}
	$function = (is_array($haystack) ? 'in_array' : 'in_string');
	return $function($needle, $haystack);
}
function in_string($needle, $haystack)
{
	return (strpos($haystack, $needle) !== false);
}
function iin_string($needle, $haystack)
{
	return (stripos($haystack, $needle) !== false);
}
function start($haystack, $needle)
{
	return (strpos($haystack, $needle) === 0);
}
function istart($haystack, $needle)
{
	return (stripos($haystack, $needle) === 0);
}
function finish($haystack, $needle)
{
	return (substr($haystack, -strlen($needle)) === $needle);
}
function ifinish($haystack, $needle)
{
	return (strtolower(substr($haystack, -strlen($needle))) === strtolower($needle));
}
function char_at($chaine, $pos = 0)
{
	return substr($chaine, $pos, 1);
}
function random($length = 40, $alphabet = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890')
{
	$max = strlen($alphabet) - 1;
	for($string = '', $i = 0; $i < $length; $i++)
	{
		$string .= char_at($alphabet, mt_rand(0, $max));
	}
	return $string;
}
function compress($string)
{
	$compress = gzcompress($string, 9);
	if(char_at($compress) === '_' || char_at($compress) === '-')
	{
		$compress = '_'.$compress;
	}
	return (strlen($compress) < strlen($string) ? $compress : '-'.$string);
}
function uncompress($string)
{
	if(char_at($string) === '-')
	{
		return substr($string, 1);
	}
	if(char_at($string) === '_')
	{
		$string = substr($string, 1);
	}
	return gzuncompress($string);
}
function hex2text($hex)
{
	$alpha = 'abcdefghijklmnop';
	$len = strlen($hex);
	$text = '';
	for($i = 0; $i < $len; $i++)
	{
		$text .= char_at($alpha, hexdec(char_at($hex, $i)));
	}
	return $text;
}
function hex2b64($hex, $len = null)
{
	$b64 = '';
	$alpha = '0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN_-';
	for($i = strlen($hex); $i>0; $i-=3)
	{
		$dec = hexdec(substr($hex, max($i-3, 0), 3));
		$c1 = $dec;
		$c2 = $dec>>6;
		$c1 &= 0x3f;
		$c2 &= 0x3f;
		$b64 = char_at($alpha, $c2).char_at($alpha, $c1).$b64;
	}
	if(is_null($len))
	{
		return $b64;
	}
	return substr(str_pad($b64, $len, '0', STR_PAD_LEFT), 0, $len);
}
function dec2b64($dec, $len = null)
{
	return  hex2b64(dechex($dec), $len);
}

?>