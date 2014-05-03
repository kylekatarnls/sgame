<?php

namespace Hologame;

class Chainer
{
	private $object;
	public function __construct($object)
	{
		$this->object = $object;
	}
	public function __call($name, array $args)
	{
		call_user_func_array([$this->object, $name], $args);
		return $this;
	}
}

?>