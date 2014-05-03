<?php

function get_get($key, $default = null, $type = 'string')
{
	$mixed = array_g_value($_GET, $key, $default, $type);
	$mixed = (is_array($mixed) ? array_map('urldecode', $mixed) : urldecode($mixed));
	return $mixed;
}
function get_post($key, $default = null, $type = 'string')
{
	return array_g_value($_POST, $key, $default, $type);
}
function get_request($key, $default = null, $type = 'string')
{
	return array_g_value($_REQUEST, $key, $default, $type);
}
function get_cookie($key, $default = null, $type = 'string')
{
	return array_g_value($_COOKIE, $key, $default, $type);
}
function get_files($key, $default = null, $type = 'string')
{
	return array_g_value($_FILES, $key, $default, $type);
}
function get_server($key, $default = null, $type = 'string')
{
	return array_g_value($_SERVER, $key, $default, $type);
}
function is_ajax()
{
	return (get_server('HTTP_X_REQUESTED_WITH','')!=='');
}

?>