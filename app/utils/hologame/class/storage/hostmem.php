<?php

namespace Hologame;

class Storage°Hostmem
{
	use Trait°Storage;

	protected $namespace;

	public function __construct($namespace = null)
	{
		$this->namespace = (!empty($namespace) && is_string($namespace) ?
			$namespace:
			H_HOST
		);
	}
	public function ttl($time = null)
	{
		return prop('mem')->ttl($time);
	}
	public function exists($name, &$get = null)
	{
		return prop('mem')->exists($this->namespace.$name, $get);
	}
	public function write($method, $name, $value = null, $ttl = null)
	{
		return prop('mem')->write($method, $this->namespace.$name, $value, $ttl);
	}
	public function delete($name)
	{
		return prop('mem')->delete($this->namespace.$name);
	}
}

?>