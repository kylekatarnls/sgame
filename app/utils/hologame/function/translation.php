<?php

function set_text_language($lan = H_LANGUAGE)
{
	$GLOBALS['text_language'] = $lan;
}

function text_with_replacements($text, array $replacements = [])
{
	static $db = [];
	$lan = array_value($GLOBALS, 'text_language', H_LANGUAGE);
	if(!isset($db[$lan]))
	{
		$db[$lan] = [];
	}
	$trace = (new Exception)->getTrace();
	for($i=1; $trace[$i]['file'] === __FILE__; $i++);
	$file = $trace[$i]['file'];
	if(!isset($db[$lan][$file]))
	{
		$inCore = (strpos($file, get_host_dir())!==0);
		$db[$lan][$file] = prop('fM'.($inCore ? 'C' : '').'Translate°'.$lan)->{$file};
		if(!is_array($db[$lan][$file]))
		{
			$db[$lan][$file] = [];
		}
	}
	if(isset($db[$lan][$file][$text]))
	{
		$text = $db[$lan][$file][$text];
	}
	foreach($replacements as $key => $value)
	{
		$text = str_replace('{'.$key.'}', $value, $text);
	}
	return $text;
}

function s()
{
	$gArg = func_get_args();
	if($gArg === [])
	{
		return '';
	}
	if(is_string($gArg[0]))
	{
		return text_with_replacements($gArg[0], array_value($gArg, 1, [], 'array'));
	}
	else
	{
		if(is_int($gArg[0]))
		{
			$gArg[0] = dec2b64($gArg[0], 6);
		}
		$text = load_text($gArg[2], $gArg[0], $gArg[1]);
		if(!is_string($text))
		{
			$text = Translation°Text::text($gArg[3], $gArg[2], $gArg[0], $gArg[1]);
		}
		return text_with_replacements($text, array_value($gArg, 4, [], 'array'));
	}
}

function p()
{
	$gArg = func_get_args();
	if($gArg === [])
	{
		return '';
	}
	if(is_string($gArg[0]))
	{
		list($singular, $plural, $number) = $gArg;
		$data = array_value($gArg, 3, [], 'array');
		if(!isset($data['number']))
		{
			$data['number'] = $number;
		}
		return text_with_replacements($number < 2 ? $singular : $plural, $data);
	}
	else
	{
		list($group, $id, $version, $singular, $plural, $number) = $gArg;
		$data = array_value($gArg, 6, [], 'array');
		if(!isset($data['number']))
		{
			$data['number'] = $number;
		}
		$gText = load_text('global-'.$version, $code, $id);
		if(is_array($gText))
		{
			list($singular, $plural) = $gText;
		}
		return text_with_replacements($number < 2 ? $singular : $plural, $data);
	}
}

?>