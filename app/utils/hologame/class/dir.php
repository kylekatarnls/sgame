<?php

namespace Hologame;

class Dir extends Object
{
	static public function getDir($dir = null)
	{
		if($dir === null)
		{
			$dir = ROOT_DIR;
		}
		return rtrim($dir, '/\\');
	}
	static public function exists($dir = null)
	{
		$dir = self::getDir($dir);
		return (file_exists($dir) && is_dir($dir));
	}
	static public function isWritable($dir = null)
	{
		$dir = self::getDir($dir);
		return (self::exists($dir) && is_writable($dir));
	}
	static public function make($dir = null, $isFile = true)
	{
		$dir = self::getDir($dir);
		if($isFile)
		{
			list($dir) = end_separator(array('/', '\\'), $dir);
		}
		if(empty($dir))
		{
			return false;
		}
		if(self::exists($dir))
		{
			return $dir;
		}
		if(!mkdir($dir, 0777, true))
		{
			throw new DirException("Impossible de créer le dossier ".$path, 1);
			return false;
		}
		return true;
	}
	static public function isEmpty($dir = null)
	{
		$dir = self::getDir($dir);
		if(!self::exists($dir))
		{
			return true;
		}
		return in_array(scandir($dir), [
			['.', '..'],
			[]
		]);
	}
	static public function gDir($dir = null, $dirRoot = null)
	{
		$dir = self::getDir($dir);
		$dir = realpath($dir);
		if($dirRoot === null)
		{
			$dirRoot = $dir;
		}
		$list = [];
		foreach(scandir($dir) as $file) if(!in_array($file, ['.', '..']))
		{
			$path = $dir.DIRECTORY_SEPARATOR.$file;
			if(is_dir($path))
			{
				$list[] = substr($path, start($dir, $dirRoot) ? strlen($dirRoot) : 0);
			}
		}
		ksort($list);
		return $list;
	}
	static public function getList($dirList = null, $onlyFiles = true, $dirRootList = null, $basename = true, $separator = DIRECTORY_SEPARATOR)
	{
		if(! is_traversable($dirList)) {
			$dirList = array($dirList);
		}
		$list = [];
		foreach ($dirList as $index => $dir) {
			$dir = self::getDir($dir);
			$dir = realpath($dir);
			$dirRoot = is_traversable($dirRootList) ? $dirRootList[$index] : $dirRootList;
			if($dirRoot === null)
			{
				$dirRoot = $dir;
			}
			if(file_exists($dir) === false || is_dir($dir) === false)
			{
				throw new DirException("Dossier introuvable", 2);
				return false;
			}
			foreach(scandir($dir) as $file) if(!in_array($file, ['.', '..']))
			{
				$path = $dir.DIRECTORY_SEPARATOR.$file;
				$listPath = sp_path($basename ?
					substr($path, start($dir, $dirRoot) ? strlen($dirRoot) : 0):
					$path
				, $separator);
				if(is_dir($path))
				{
					if($onlyFiles === false)
					{
						$list[] = $listPath;
					}
					$list = array_merge($list, self::getList($path, $onlyFiles, $dirRoot, true, $separator));
				}
				else
				{
					$list[] = $listPath;
				}
			}
		}
		ksort($list);
		return $list;
	}
	static public function each($callback, $dir = null, $onlyFiles = true, $separator = DIRECTORY_SEPARATOR, $dirRootList = null, $basename = true)
	{
		$result = [];
		if(is_callable($callback) === false)
		{
			throw new DirException("Callback invalide", 3);
			return false;
		}
		foreach(self::getList($dir, $onlyFiles, $dirRootList, $basename, $separator) as $file)
		{
			$result[$file] = call_user_func($callback, $file);
		}
		return $result;
	}
	static public function size($dir = null)
	{
		return dirsize(self::getDir($dir));
	}
}

class DirException extends Exception {}

?>