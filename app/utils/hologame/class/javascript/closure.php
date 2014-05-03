<?php

namespace Hologame;

use Closure;

class Jquery°Closure extends Object
{
	protected $insctructions;
	protected $name;
	public function __construct(&$insctructions, $name)
	{
		$this->insctructions = &$insctructions;
		$this->name = $name;
	}
	public function raw($js)
	{
		$this->insctructions .= $js;
	}
	public function __call($method, array $params)
	{
		$this->raw($this->name . '.' . call_user_func_array([new Javascript, $method], $params));
	}
	public function getName()
	{
		return $this->name;
	}
	static public function execClosure($closure, array $params)
	{
		$insctructions = '';
		$params = array_map(
			function ($param) use(&$insctructions)
			{
				return new static($insctructions, raw($param));
			},
			$params
		);
		call_user_func_array(
			Closure::bind($closure, new static($insctructions, raw('this')), __CLASS__),
			$params
		);
		return $insctructions;
	}
}

?>