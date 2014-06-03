<?php
define('EXPIRATION',120); // Durée de validité d'une image dans le cache du client (en jours)

error_reporting(-1);

function av($array, $key)
{
	return is_array($array) && isset($array[$key]) ? $array[$key] : null;
}

function g($key)
{
	return av($_GET, $key);
}

if(! function_exists('unix_path'))
{
	function unix_path($path)
	{
		return strtr($path, '\\', '/');
	}
}

function get_extension($path)
{
	$pos = strrpos($path, '.');
	return $pos === false ? '' : substr($path, $pos + 1);
}

define('RACINE',unix_path(realpath(__DIR__)).'/img/');

$file = RACINE.g('fichier');
if(!file_exists($file) || strpos($file = unix_path(realpath($file)), RACINE) !== 0)
{
	exit();
}
define('FICHIER',$file);
unset($file);
define('EXTENSION',preg_replace('#[^a-z0-9]#i','',get_extension(FICHIER)));
define('TYPE',strtolower(EXTENSION)==='gif'? 'gif':(strtolower(EXTENSION)==='png'? 'png':'jpeg'));
define('DOSSIER_CACHE',realpath(__DIR__.'/../app/storage/eimg'));
if(!file_exists(DOSSIER_CACHE))
{
	mkdir(DOSSIER_CACHE, 0777, true);
}
$params=explode('_',trim(strtolower(preg_replace('#[^a-z0-9_.-]#i','',g('params'))),'_'));
sort($params);
define('PARAMS',implode('_',$params));

define('FICHIER_CACHE',DOSSIER_CACHE.'/'.substr(FICHIER, strrpos(FICHIER, '/') + 1).'--'.sha1(FICHIER).'-'.PARAMS.'.'.EXTENSION);
define('FICHIER_SOURCE',FICHIER);

if(!file_exists(FICHIER_CACHE))
{
	$rac=array(
		 's'=>'saturation'
		,'l'=>'luminosite'
		,'t'=>'teinte'
		,'tr'=>'transparence'
		,'a'=>'applique'
		,'g'=>'gaussien'
		,'c'=>'contraste'
		,'li'=>'limite'
		,'rx'=>'redimx'
		,'ry'=>'redimy'
		,'sy'=>'symetrie'
	);
	$rac_str=array(
		 'a'=>true
		,'li'=>true
	);
	include_once(__DIR__.'/../app/utils/picturesFunctions.php');
	$p=array();
	foreach($params as $par)
	{
		if(preg_match('#^([a-z]+)(-?[0-9v]+)?$#',$par,$m))
		{
			if(isset($rac[$m[1]]))
				$p[$rac[$m[1]]]=(av($rac_str,$m[1])? str_replace('v','.',$m[2]):floatval(str_replace('v','.',$m[2])));
			else
				$p[$m[1]]=floatval(str_replace('v','.',$m[2]));
		}
	}

	if(empty($p))
	{
		header("Expires: " . gmdate("D, d M Y H:i:s",time()+EXPIRATION*86400) . " GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s",filemtime(FICHIER_SOURCE)) . " GMT");
		header('Content-type: image/'.TYPE);
		readfile(FICHIER_SOURCE);
	}
	$f1='imagecreatefrom'.TYPE;
	$f2='image'.TYPE;
	if(av($p,'redimx')>0 || av($p,'redimy')>0)
	{
		$source=imageretouche($f1(FICHIER_SOURCE),$p);
		$sx=imagesx($source);
		$sy=imagesy($source);
		if(av($p,'redimx')<1)
		{
			$p['redimx']=round(av($p,'redimy')*$sx/$sy);
		}
		elseif(av($p,'redimy')<1)
		{
			$p['redimy']=round(av($p,'redimx')*$sy/$sx);
		}
		$image=redimensionne($source,intval($p['redimx']),intval($p['redimy']),$sx,$sy);
		imagedestroy($source);
		unset($source,$sx,$sy);
	}
	else
	{
		$image=imageretouche($f1(FICHIER_SOURCE),$p);
	}
	$f2($image,FICHIER_CACHE);
	imagedestroy($image);
	unset($image);
}

// Erreur
if(!file_exists(FICHIER_CACHE))
{
	exit();
}

header("Expires: " . gmdate("D, d M Y H:i:s",time()+EXPIRATION*86400) . " GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s",filemtime(FICHIER_CACHE)) . " GMT");
header('Content-type: image/'.TYPE);
readfile(FICHIER_CACHE);
?>