<?php

namespace Hologame;

class Storage°Mem
{
	use Trait°Storage;
	protected $memcache = null,
		$ttl = 604800;
	public function __construct()
	{
		$this->memcache = new Memcache;
		$this->memcache->connect('localhost', 11211);
	}
	public function ttl($time = null)
	{
		if($time === null)
		{
			return $this->ttl;
		}
		$this->ttl = $time;
	}
	public function exists($name, &$get = null)
	{
		$value = $this->memcache->get($name);
		$exists = (empty($value) === false);
		if($exists && func_num_args() > 1)
		{
			$get = unserialize($value);
		}
		return $exists;
	}
	public function write($method, $name, $value = null, $ttl = null)
	{
		$value = serialize($value);
		return $this->memcache->$method(
			$name,
			$value,
			strlen($value) > 512 ? MEMCACHE_COMPRESSED : false,
			$ttl === null ? $this->ttl : $ttl
		);
	}
	public function delete($name)
	{
		$gName = (array) $name;
		$return = true;
		foreach($gName as $name)
		{
			if(!$this->memcache->delete($name))
			{
				$return = false;
			}
		}
		return $return;
	}
}

?>