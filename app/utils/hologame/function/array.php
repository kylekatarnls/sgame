<?php

function tpl_array($array, $keyName = 'key', $valueName = 'value')
{
	$return = [];
	foreach((array) $array as $key => $value)
	{
		$return[] = [
			$keyName => $key,
			$valueName => $value
		];
	}
	return $return;
}
function object_merge($array1, $array2)
{
	return (object) array_merge((array) $array1, (array) $array2);
}
function array_value($array, $key, $default = null, $type = null, $nullIfEmpty = false)
{
	if(is_object($array)
	&& property_exists($array,$key) === false
	&& method_exists($array, $methode = 'get'.ucfirst($key)))
	{
		$array = array(
			$key => $array->$methode()
		);
	}
	$array = (array) $array;
	$isNull = $nullIfEmpty ? empty($array[$key]) : isset($array[$key]) === false;
	if($isNull)
	{
		$array[$key] = $default;
	}
	else if($type !== null)
	{
		settype($array[$key], $type);
	}
	return $array[$key];
}
function array_g_value($array, $key, $default = null, $type = null, $nullIfEmpty = false)
{
	if(is_object($key) || is_array($key))
	{
		$gKey = (array) $key;
		$data = [];
		foreach($gKey as  $key)
		{
			$data[$key] = array_value($array, $key, $default, $type, $nullIfEmpty);
		}
		return $data;
	}
	else
	{
		return array_value($array, $key, $default, $type, $nullIfEmpty);
	}
}
function array_put(&$array, $value)
{
	if(empty($array) || !is_array($array))
	{
		$array = [];
	}
	$array = array_merge($array, (array) $value);
}
function is_num_array($array)
{
	return (is_array($array) && array_keys($array) === range(0, count($array)-1));
}
function is_sub_array($array)
{
	return (is_array($array) && is_array(reset($array)));
}
function scanarray($needle, array $haystack, $i = 0)
{
	static $index = 0;
	if($i > 0)
	{
		$index = intval($i);
	}
	foreach($haystack as $key => $value)
	{
		if($value === $needle && --$index < 0)
		{
			return [$key];
		}
		else if(is_array($value))
		{
			$return = scanarray($needle, $value);
			if($return !== false)
			{
				return array_merge([$key], $return);
			}
		}
	}
	return false;
}
function is_traversable($value)
{
	return is_array($value) or $value instanceof Traversable;
}

?>