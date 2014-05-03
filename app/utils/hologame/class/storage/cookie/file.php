<?php

namespace Hologame;

class Storage°Cookie°File extends Storage°Cookie
{
	protected $dir = '', $base = '', $ttl = 720000;
	const PATH = 'cache/cookie/';
	public function __construct($key = null)
	{
		if($key === null)
		{
			$key = 'f';
		}
		parent::__construct($key);
		$this->base = path('STORAGE', 'HOST').self::PATH;
		try
		{
			Dir::each([$this, 'cleanFile'], $this->base, true);
			foreach(Dir::gDir($this->base) as $dir)
			{
				if(Dir::isEmpty($this->base.$dir))
				{
					rmdir($this->base.$dir);
				}
			}
		}
		catch(DirException $e)
		{
			if($e->getCode() === 2)
			{
				Dir::make($this->base);
			}
			else
			{
				throw $e;
			}
		}
		if(!parent::exists('id', $id))
		{
			do
			{
				$id = random();
			}
			while(Dir::exists($this->base.$this->key.$id));
		}
		parent::write('set', 'id', $id);
		$this->dir = $this->base.$this->key.$id.'/';
		Dir::make($this->dir);
	}
	public function cleanFile($file)
	{
		$time = Storage°File::mTime($this->base.$file);
		if($time !== false && $time < time())
		{
			unlink($this->base.$file);
		}
	}
	public function exists($name, &$get = null)
	{
		$hasGet = (func_num_args() > 1);
		$exists = Storage°File::exists($this->dir.$this->key.$name);
		if($exists && $hasGet)
		{
			$get = unserialize(uncompress(Storage°File::getContent($this->dir.$this->key.$name)));
		}
		return $exists;
	}
	public function write($method, $name, $value = null)
	{
		if($method !== 'set' && Storage°File::exists($this->dir.$this->key.$name) !== ($method === 'replace'))
		{
			return false;
		}
		Storage°File::putContent($this->dir.$this->key.$name, compress(serialize($value), 9));
		Storage°File::mTime($this->dir.$this->key.$name, time()+$this->ttl*60);
		return true;
	}
	public function delete($name)
	{
		$gName = (array) $name;
		$return = true;
		foreach($gName as $name)
		{
			if(Storage°File::exists($this->dir.$this->key.$name))
			{
				unlink($this->dir.$this->key.$name);
			}
			else
			{
				$return = false;
			}
		}
		return $return;
	}
}

?>