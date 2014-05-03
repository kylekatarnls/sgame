<?php

namespace Hologame;

class Storage°Cookie
{
	use Trait°Storage;
	protected $ttl = -1, $path = '/', $key = 'c';
	public function __construct($key = null)
	{
		if(is_string($key))
		{
			$this->key = $key;
		}
	}
	public function ttl($time = null)
	{
		if($time === null)
		{
			return $this->ttl;
		}
		$this->ttl = $time*60;
	}
	public function path($path = null)
	{
		if($path === null)
		{
			return $this->path;
		}
		$this->path = $path;
	}
	protected function unserializeCookie($name, $method, $value = null)
	{
		static $unserializeCookie = null;
		if($unserializeCookie === null)
		{
			$unserializeCookie = [];
		}
		switch($method)
		{
			case 'exists':
				return isset($unserializeCookie[$name]);
			case 'get':
				return $unserializeCookie[$name];
			case 'set':
				$unserializeCookie[$name] = $value;
				break;
			case 'unset':
				unset($unserializeCookie[$name]);
				break;
		}
	}
	public function exists($name, &$get = null)
	{
		$hasGet = (func_num_args() > 1);
		$exists = $this->unserializeCookie($name, 'exists');
		if($exists)
		{
			if($hasGet)
			{
				$get = $this->unserializeCookie($name, 'get');
			}
		}
		else
		{
			$exists = isset($_COOKIE[$this->key.$name]);
			if($exists && $hasGet)
			{
				$get = unserialize($_COOKIE[$this->key.$name]);
				$this->unserializeCookie($name, 'set', $get);
			}
		}
		return $exists;
	}
	public function write($method, $name, $value = null)
	{
		if($method !== 'set' && isset($_COOKIE[$this->key.$name]) !== ($method === 'replace'))
		{
			return false;
		}
		$this->unserializeCookie($name, 'set', $value);
		setcookie($this->key.$name, serialize($value), $this->ttl < 0 ? 0 : $this->ttl + time(), $this->path);
		return true;
	}
	public function delete($name, $force = false)
	{
		$gName = (array) $name;
		$return = true;
		foreach($gName as $name)
		{
			if(isset($_COOKIE[$this->key.$name]) || $force)
			{
				unset($_COOKIE[$this->key.$name]);
				$this->unserializeCookie($name, 'unset');
				setcookie($this->key.$name, '', time() - 3600, $this->path);
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