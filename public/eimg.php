<?php
define('EXPIRATION',120); // Durée de validité d'une image dans le cache du client (en jours)

error_reporting(-1);

function av($array, $key)
{
	return is_array($array) && isset($array[$key]) ? $array[$key] : null;
}

function open_sesssion()
{
	session_name('simg');
	session_start();
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
$fichier=g('fichier');
define('IMAGE_COMMIT', strpos($fichier, 'simg/commit/') === 0);
if(IMAGE_COMMIT)
{
	$fichier = 'simg/' . substr($fichier, strlen('simg/commit/'));
}
define('CHEMIN',$fichier);
unset($fichier);
define('IMAGE_SESSION',strpos(CHEMIN, 'simg/') === 0);

$file = RACINE.CHEMIN;
if(IMAGE_SESSION)
{
	define('SESSION_KEY',substr(CHEMIN,5));
}
elseif(!file_exists($file) || strpos($file = unix_path(realpath($file)), RACINE) !== 0)
{
	exit; // no-debug
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
define('DEJA_EN_CACHE',IMAGE_SESSION ? isset($_SESSION['simg-cache'][SESSION_KEY]) : file_exists(FICHIER_CACHE));

if(!DEJA_EN_CACHE)
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
		if(IMAGE_SESSION)
		{
			header("Expires: " . gmdate("D, d M Y H:i:s",time()+EXPIRATION*86400) . " GMT");
			header('Content-type: image/'.TYPE);
			if(IMAGE_COMMIT)
			{
				chdir(__DIR__ . '/..');
				passthru('git show ' . preg_replace('#/#', ':public/img/', SESSION_KEY, 1));
			}
			else
			{
				open_sesssion();
				echo $_SESSION['simg'][SESSION_KEY]; // no-debug
			}
		}
		else
		{
			header("Expires: " . gmdate("D, d M Y H:i:s",time()+EXPIRATION*86400) . " GMT");
			//header("Last-Modified: " . gmdate("D, d M Y H:i:s",filemtime(FICHIER_SOURCE)) . " GMT");
			header('Content-type: image/'.TYPE);
			readfile(FICHIER_SOURCE);
		}
		exit;
	}
	$f1='imagecreatefrom'.TYPE;
	$f2='image'.TYPE;
	if(IMAGE_SESSION)
	{
		open_sesssion();
		$source=imageretouche(imagecreatefromstring($_SESSION['simg'][SESSION_KEY]),$p);
	}
	else
	{
		$source=imageretouche($f1(FICHIER_SOURCE),$p);
	}
	if(av($p,'redimx')>0 || av($p,'redimy')>0)
	{
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
		$image=&$source;
	}
	if(IMAGE_SESSION)
	{
		ob_start();
		$f2($image);
		if(empty($_SESSION['simg-cache']))
		{
			$_SESSION['simg-cache'] = array();
		}
		$_SESSION['simg-cache'][SESSION_KEY] = ob_get_contents();
		ob_end_clean();
	}
	else
	{
		$f2($image,FICHIER_CACHE);
	}
	imagedestroy($image);
	unset($image);
}

// Erreur
if(!IMAGE_SESSION && !file_exists(FICHIER_CACHE))
{
	exit; // no-debug
}


if(IMAGE_SESSION)
{
	header("Expires: " . gmdate("D, d M Y H:i:s",time()+EXPIRATION*86400) . " GMT");
	header('Content-type: image/'.TYPE);
	echo $_SESSION['simg-cache'][SESSION_KEY]; // no-debug
}
else
{
	header("Expires: " . gmdate("D, d M Y H:i:s",time()+EXPIRATION*86400) . " GMT");
	//header("Last-Modified: " . gmdate("D, d M Y H:i:s",filemtime(FICHIER_CACHE)) . " GMT");
	header('Content-type: image/'.TYPE);
	readfile(FICHIER_CACHE);
}
?>