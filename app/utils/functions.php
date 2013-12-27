<?php

function _()
{
	$args = func_get_args();
	if(isset($args[1]) && is_numeric($args[1]))
	{
		return call_user_func_array(array('Lang', 'choice'), $args);
	}
	return call_user_func_array(array('Lang', 'get'), $args);
}

if(!class_exists('Memcached') && class_exists('Memcache'))
{
	include_once __DIR__ . DIRECTORY_SEPARATOR . 'EmulateMemcachedWithMemcache.php';
}

?>