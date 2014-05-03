<?php

namespace Hologame;

class Storage°File extends Object
{
	static public function getFile(&$file, $defaultPath = 'HOST')
	{
		$dir = path('STORAGE', $defaultPath);
		if(!start($file, ROOT_DIR))
		{
			$file = $dir.$file;
		}
	}
	static public function getContent($file, $default = '', $defaultPath = 'HOST')
	{
		if(!self::exists($file, $defaultPath))
		{
			return $default;
		}
		return file_get_contents($file);
	}
	static public function getMatch($regex, $file, $m = 0, $default = null, $defaultPath = 'HOST')
	{
		if(!self::exists($file, $defaultPath))
		{
			return $default;
		}
		preg_match($regex, file_get_contents($file), $match);
		return array_value($match, $m, $default);
	}
	static public function putContent($file, $content = '')
	{
		$storage = rtrim($file, '/');
		self::getFile($storage);
		if(Dir::make($storage, true) === false)
		{
			return false;
		}
		if(touch($storage) === false)
		{
			throw new Exception("Impossible de créer le fichier ".$file, 1);
			return false;
		}
		if(chmod($storage, 0777) === false)
		{
			throw new Exception("Impossible de changer le CHMOD du fichier ".$file, 1);
			return false;
		}
		if(file_put_contents($storage, $content) === false)
		{
			throw new Exception("Impossible d'écrire dans le fichier ".$file, 1);
			return false;
		}
		return true;
	}
	static public function exists(&$file, $defaultPath = 'HOST')
	{
		self::getFile($file, $defaultPath);
		return (file_exists($file) && is_file($file));
	}
	static public function unlink(&$file, $defaultPath = 'HOST')
	{
		self::getFile($file, $defaultPath);
		return (file_exists($file) && is_file($file) && unlink($file));
	}
	static public function isWritable($file, $defaultPath = 'HOST')
	{
		return (self::exists($file, $defaultPath) && is_writable($file));
	}
	static public function mTime($file, $time = null, $defaultPath = 'HOST')
	{
		if($time !== null)
		{
			return self::touch($file, $defaultPath, $time);
		}
		return (self::exists($file, $defaultPath) ? filemtime($file) : false);
	}
	static public function touch($file, $time = null, $defaultPath = 'HOST')
	{
		$file = rtrim($file, '/');
		self::getFile($file, $defaultPath);
		if(Dir::make($file, true) === false)
		{
			return false;
		}
		return touch($file, $time === null ? time() : $time);
	}
}

?>