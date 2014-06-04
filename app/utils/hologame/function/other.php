<?php

function make_constants($prefix, $gConstant = null)
{
	static $i=0;
	if($gConstant === null)
	{
		define($prefix, ++$i);
	}
	else
	{
		foreach($gConstant as $constant)
		{
			define($prefix.$constant, ++$i);
		}
	}
}
function make_flags($prefix, $gFlag)
{
	$i=0;
	foreach($gFlag as $flag)
	{
		default_define($prefix.$flag, 1<<($i++));
	}
}
function default_define($name, $value, $ci = false)
{
	if(!defined($name))
	{
		define($name, $value, $ci);
	}
}
function get_constant($name)
{
	return (defined($name) ? constant($name) : null);
}
function module($name, $enabled = true)
{
	default_define('MODULE_'.strtoupper(str_replace('°', '_', $name)), $enabled);
}
function get_module($name)
{
	return (get_constant('MODULE_'.strtoupper(str_replace('°', '_', $name))) === true);
}
function £($string, $data = null, $file = 'page/index')
{
	if(is_string($data))
	{
		$file = $data;
		$data = null;
	}
	if($data === null)
	{
		$data = (new Object)->data;
	}
	if(strpos($string,":html\n")===0)
	{
		$body = £(substr($string, 6), $data);
		if(is_object($data))
		{
			$data->body = $body;
		}
		else
		{
			$data['body'] = $body;
		}
		$string = new \Hologame\Template('html');
	}
	$gBlocFile = array_value($data, 'gBlocFile', [], 'array');
	require_once(path('RRIVATE', 'CORE').'twig/vendor/autoload.php');
	Twig_Autoloader::register();
	$loader = new Twig_Loader_Array(array_merge($gBlocFile, [
		$file => $string,
	]));
	$twig = new Twig_Environment($loader, [
		'cache' => path('STORAGE', 'CORE').'cache/template',
	]);
	try
	{
		$string = $twig->render($file, $data);
	}
	catch(Exception $e)
	{
		$storage=path('STORAGE', 'CORE');
		if(!file_exists($storage))
		{
			exit('<div class="error">'.s("Le dossier de stockage n'existe pas : ").$storage.'</div>'); // no-debug
		}
		if(!is_writable($storage))
		{
			exit('<div class="error">'.s("Le dossier de stockage n'est pas accessible en écriture : ").$storage.'</div>'); // no-debug
		}
		throw $e;
	}
	foreach(explode(',', FILTER_HTML) as $filter)
	{
		foreach(get_class_methods($filter) as $method)
		{
			$string = call_user_func([$filter, $method], $string, $data);
		}
	}
	return $string;
}
function µ()
{
	$gArg = func_get_args();
	if(in_array(func_num_args(), [1, 2]))
	{
		if($gArg[0] instanceof \Hologame\Jquery\Call || $gArg[0] instanceof \Hologame\Jquery || $gArg[0] instanceof \Hologame\Javascript)
		{
			$cJavascript = prop('js');
			$cJavascript->delay(strval($gArg[0]), array_value($gArg, 1, 0, 'int'));
			return $cJavascript;
		}
	}
	return new \Hologame\Html°Collection(func_get_args());
}
function prop($prop, $method = null, $args = [])
{
	return is_null($method) ?
		(new \Hologame\Object)->$prop:
		call_user_func_array([(new \Hologame\Object)->$prop, $method], $args);
}
function get_inline_script()
{
	return prop('cJavascript', 'html');
}
function get_args($gArg)
{
	$gArg = (array) $gArg;
	while(!empty($gArg) && is_num_array($gArg) && is_num_array($gArg[0]))
	{
		$gArg = $gArg[0];
	}
	return $gArg;
}
function post($name, array $attr = [])
{
	return array_merge([
		'id' => $name,
		'name' => $name,
		'value' => get_post($name)
	], $attr);
}
function alias($old, $new)
{
	if(class_exists($old, false) && !class_exists($new, false))
	{
		eval('class '.$new.' extends '.$old.' {}'); // no-debug
		return true;
	}
	else if(trait_exists($old, false) && !trait_exists($new, false))
	{
		eval('trait '.$new.' { use '.$old.'; }'); // no-debug
		return true;
	}
	elseif(class_exists('\\Hologame\\' . $old, false) && !class_exists('\\Hologame\\' . $new, false))
	{
		eval('class \\Hologame\\'.$new.' extends \\Hologame\\'.$old.' {}'); // no-debug
		return true;
	}
	else if(trait_exists('\\Hologame\\' . $old, false) && !trait_exists('\\Hologame\\' . $new, false))
	{
		eval('trait \\Hologame\\'.$new.' { use \\Hologame\\'.$old.'; }'); // no-debug
		return true;
	}
	return false;
}
function exists($name, $autoload = true)
{
	return class_exists($name, $autoload) || trait_exists($name, $autoload);
}
function data2html($data)
{
	return '<span class="data">'.json_encode($data).'</span>';
}

?>