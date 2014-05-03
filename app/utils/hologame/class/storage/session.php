<?php

namespace Hologame;

class Storage°Session
{
	use Trait°Storage;
	protected $key = 'global';
	const NAME = 'hsid';
	public function __construct($key = null)
	{
		if($key === null)
		{
			$key = array_value($GLOBALS, 'H_HOST', H_HOST);
		}
		$this->key = $key;
	}
	public function ttl($ttl = null)
	{
		$this->start();
		$cookie = (isset($_COOKIE[self::NAME]) ? $_COOKIE[self::NAME] : session_id());
		setcookie(self::NAME, $cookie, time() + $ttl*60, '/');
	}
	public function start()
	{
		session_name(self::NAME);
		if(!session_id())
		{
			if(isset($_COOKIE[self::NAME]))
			{
				session_id($_COOKIE[self::NAME]);
			}
			session_start();
		}
	}
	public function getObject()
	{
		$this->start();
		return array_value($_SESSION, $this->key, new stdClass, 'object');
	}
	public function createObject()
	{
		$this->start();
		if(is_array($_SESSION))
		{
			if(!isset($_SESSION[$this->key]))
			{
				$_SESSION[$this->key] = new stdClass;
			}
			return $_SESSION[$this->key];
		}
		else
		{
			if(!isset($_SESSION->{$this->key}))
			{
				$_SESSION->{$this->key} = new stdClass;
			}
			return $_SESSION->{$this->key};
		}
	}
	public function exists($name, &$get = null)
	{
		$object = $this->getObject();
		$exists = isset($object->$name);
		if($exists && func_num_args() > 1)
		{
			$get = $object->$name;
		}
		return $exists;
	}
	public function write($method, $name, $value = null)
	{
		$object = $this->createObject();
		if($method !== 'set' && isset($object->$name) !== ($method === 'replace'))
		{
			return false;
		}
		$object->$name = $value;
		return true;
	}
	public function delete($name)
	{
		$object = $this->createObject();
		$gName = (array) $name;
		$return = true;
		foreach($gName as $name)
		{
			if(isset($object->$name))
			{
				unset($object->$name);
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