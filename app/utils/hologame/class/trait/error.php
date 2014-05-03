<?php

namespace Hologame;

Trait Trait°Error
{
	protected $errorInfo = [];
	public function errorInfo($name = null)
	{
		$errorInfo = (object) $this->errorInfo;
		if($name !== null)
		{
			$errorInfo = array_value($errorInfo, $name);
		}
		return $errorInfo;
	}
	public function cleanError($name, $value)
	{
		$this->errorInfo = [];
	}
	public function error($name, $value)
	{
		if(empty($this->errorInfo[$name]))
		{
			$this->errorInfo[$name] = $value;
		}
		else
		{
			$this->errorInfo[$name] = (array) $this->errorInfo[$name];
			$this->errorInfo[$name][] = $value;
		}
	}
	public function isError($name, $value = null)
	{
		$array = array_value($this->errorInfo, $name, [], 'array');
		if($value === null)
		{
			return ($array !== []);
		}
		return in_array($value, $array);
	}
}

?>