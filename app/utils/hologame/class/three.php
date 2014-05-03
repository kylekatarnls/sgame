<?php

namespace Hologame;

class Three
{
	protected $data = [];
	public function __get($name)
	{
		if(!isset($this->data[$name]))
		{
			$this->data[$name] = new Three;
		}
		return $this->data[$name];
	}
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	public function out()
	{
		$data = $this->data;
		foreach($data as &$d)
		{
			if(is_a($d, 'Three'))
			{
				$d = $d->out();
			}
		}
		return $data;
	}
}

?>