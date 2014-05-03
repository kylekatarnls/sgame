<?php

namespace Hologame;

use ArrayObject;

class ArrayObjectAccessor extends ArrayObject
{
	public function &__get($name)
	{
		if(!isset($this[$name]))
		{
			$this[$name] = new static;
		}
		$value = &$this[$name];
		return $value;
	}
	public function __isset($name)
	{
		return isset($this[$name]);
	}
	public function __set($name, $value)
	{
		$this[$name] = $value;
	}
	public function __unset($name)
	{
		unset($this[$name]);
	}
	public function toArray()
	{
		return parent::getArrayCopy();
	}
	public function __toString()
	{
		return json_encode($this->toArray());
	}
}

?>