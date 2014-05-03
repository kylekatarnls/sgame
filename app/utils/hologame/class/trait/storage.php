<?php

namespace Hologame;

trait Trait°Storage
{
	public function __isset($name)
	{
		return $this->exists($name);
	}
	public function get($name)
	{
		return ($this->exists($name, $value) ? $value : null);
	}
	public function __get($name)
	{
		return $this->get($name);
	}
	public function traitWrite($method, $name, $value = null, $ttl = null)
	{
		if($ttl !== null)
		{
			$saveTtl = $this->ttl();
			$this->ttl($ttl);
		}
		$write = $this->write($method, $name, $value, $ttl);
		if($ttl !== null)
		{
			$this->ttl($saveTtl);
		}
		return $write;
	}
	public function set($name, $value = null, $ttl = null)
	{
		return $this->traitWrite('set', $name, $value, $ttl);
	}
	public function add($name, $value = null, $ttl = null)
	{
		return $this->traitWrite('add', $name, $value, $ttl);
	}
	public function replace($name, $value = null, $ttl = null)
	{
		return $this->traitWrite('replace', $name, $value, $ttl);
	}
	public function inc($name, $inc = 1, $ttl = null)
	{
		return $this->traitWrite('set', $name, $this->get($name)+$inc, $ttl);
	}
	public function dec($name, $dec = 1, $ttl = null)
	{
		return $this->traitWrite('set', $name, $this->get($name)-$dec, $ttl);
	}
	public function push($name, $value, $ttl = null)
	{
		$array = $this->get($name);
		if(empty($array))
		{
			$array = [];
		}
		if(!is_array($array))
		{
			$array = (array) $array;
		}
		$array[] = $value;
		$this->traitWrite('set', $name, $array, $ttl);
	}
	public function arraySet($name, $key, $value, $ttl = null)
	{
		$array = $this->get($name);
		if(empty($array))
		{
			$array = [];
		}
		if(!is_array($array))
		{
			$array = (array) $array;
		}
		$array[$key] = $value;
		$this->traitWrite('set', $name, $array, $ttl);
	}
	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}
	public function __unset($name)
	{
		return $this->delete($name);
	}
	public function call(array $params)
	{
		return (count($params) === 1 ? $this->get($params[0]) : $this->set($params[0], $params[1]));
	}
}

?>