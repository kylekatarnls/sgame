<?php

function selfbuild_link($match)
{
	list(, $avant, $dedans, $url, $ligne) = $match;
	return $avant.'<a href="http://webftp.selfbuild.fr/?session_courante=shina%40localhost&url='.
		urlencode($url).'#ligne='.$ligne.'" style="color: blue; text-decoration: none; border-bottom: 1px dotted blue;">'.$dedans.'</a>';
}
function error_block($content = '', $e = null)
{
	if(get_constant('DEVMODE'))
	{
		if($e === null)
		{
			$e = new Exception;
		}
		echo '<pre style="
			border: 5px solid red;
			background: white;
			color: black;
			padding: 10px;
			z-index: 99999;
			position: relative;
			overflow: auto;
			text-align: left;
			font-family: monospace;
			line-height: 20px;
		">'.
			$content."\n".
			preg_replace_callback(
				'`#([0-9]+\s)(/var/www/holowar(.+)\(([0-9]+)\):)`isU',
				'selfbuild_link',
				encode($e->getTraceAsString())
			)."\n".
		'</pre>';
	}
}
function var_dump_return_each($var, $tab=0)
{
	if($tab > 6)
	{
		return "{{Trop profond}}\n";
	}
	$r='';
	if(is_object($var))
	{
		$r .= 'object#'.get_class($var).' (';
		$var = (array) $var;
		$r .= count($var).") {\n";
		foreach($var as $key => $value)
		{
			$r .= str_repeat("\t", $tab+1).'['.trim(var_dump_return_each($key)).'] => '.var_dump_return_each($value, $tab+1);
		}
		$r .= str_repeat("\t", $tab)."}\n";
	}
	else if(is_array($var))
	{
		$r .= 'array ('.count($var).") {\n";
		foreach($var as $key => $value)
		{
			$r .= str_repeat("\t", $tab+1).'['.trim(var_dump_return_each($key)).'] => '.var_dump_return_each($value, $tab+1);
		}
		$r .= str_repeat("\t", $tab)."}\n";
	}
	else
	{
		$r .= '('.gettype($var).') '.(is_resource($var) ? strval($var) : (is_string($var) ? '"'.$var.'"' : json_encode($var)));
	}
	return $r."\n";
}
function var_dump_return()
{
	$r='';
	foreach(func_get_args() as $var)
	{
		$r.=var_dump_return_each($var);
	}
	return $r;
}
function debug()
{
	$vars = func_get_args();
	$var = $vars[0];
	if(is_a($var, 'Exception') && !get_constant('DEVMODE'))
	{
		echo '<div class="error">Erreur interne.</div>';
		return null;
	}
	if(!empty($var) && is_object($var))
	{
		if(method_exists($var,'getMessage') && method_exists($var,'getTraceAsString'))
		{
			$e = $var;
			$file = $e->getFile();
			$line = $e->getLine();
			$error = '<b>'.get_class($e).'</b> : '.$e->getMessage().
				"\nCode : ".$e->getCode().
				"\nFichier : ".selfbuild_link(array(
					'',
					'',
					$file,
					preg_replace('#^(/media/Barracuda/serveur|/var)/www/holowar/#', '', $file),
					$line
				)).
				"\nLigne : <b>".$line.'</b>';
			unset($vars[0]);
		}
		else if(method_exists($var, 'errorInfo'))
		{
			$error = 'ErrorInfo : '.print_r($var->errorInfo(), 1);
		}
		if(is_a($var, 'PDOException'))
		{
			$error .= "\nRequête : ".prop('sql', 'remainQuery');
		}
	}
	if(!isset($e))
	{
		$e = new Exception;
	}
	if(!empty($vars))
	{
		$error = 'Vars :<hr />';
		foreach($vars as $var)
		{
			$error .= encode(var_dump_return_each($var)).'<hr />';
		}
	}
	error_block($error, $e);
}
function php_error($errno, $errstr, $errfile, $errline)
{
	if(!get_constant('DEVMODE'))
	{
		if($errno & (E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE | E_RECOVERABLE_ERROR))
		{
			echo '<div class="error">Erreur interne.</div>';
		}
		return null;
	}
	$colors = [
		'#DD337A' => [
			(E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR),
			'Fatal run-time error'
		],
		'#F27641' => [
			(E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING),
			'Warning error'
		],
		'#8FC647' => [
			(E_NOTICE | E_USER_NOTICE),
			'Notice error'
		],
		'#D3B437' => [
			(E_DEPRECATED | E_USER_DEPRECATED),
			'Depreciated'
		],
		'red' => [
			E_PARSE,
			'Parse error'
		],
		'#375BD3' => [
			E_STRICT,
			'Strict standard error'
		],
		'#D6BF59' => [
			E_RECOVERABLE_ERROR,
			'Catchable fatal error'
		],
	];
	$e_user = (E_USER_DEPRECATED | E_USER_WARNING | E_USER_NOTICE | E_USER_ERROR);
	$e_core = (E_CORE_ERROR | E_CORE_WARNING);
	$e_compile = (E_COMPILE_ERROR | E_COMPILE_WARNING);

	$error = '';
	foreach($colors as $color => $data)
	{
		list($errnos, $name) = $data;
		if($errno & $errnos)
		{
			$error = '<b style="color: '.$color.';">'.$name.'</b> : ';
			break;
		}
	}
	if($error === '')
	{
		$error = '<b>Unknown error</b> : ';
	}

	if($errno & $e_user)
	{
		$error = '[USER] '.$error;
	}
	else if($errno & $e_core)
	{
		$error = '[CORE] '.$error;
	}
	else if($errno & $e_compile)
	{
		$error = '[COMPILE] '.$error;
	}
	$error .= encode($errstr).
		"\nCode : ".$errno.
		"\nFichier : ".selfbuild_link(array(
			'',
			'',
			$errfile,
			preg_replace('#^(/media/Barracuda/serveur|/var)/www/holowar/#', '', $errfile),
			$errline
		)).
		"\nLigne : <b>".$errline.'</b>';

	error_block($error);
}
function should_be_array_or_traversable($value)
{
	if(is_array($value) or $value instanceof Traversable)
	{
		return true;
	}
	throw new \InvalidArgumentException("Must be an array or a Traversable object, you passed : " . var_export($value, true), 1);
	return false;
}

?>