<?php

namespace Hologame;

class Raw
{
	protected $string = '';
	public function __construct($string='')
	{
		$this->string = strval($string);
	}
	public function __toString()
	{
		return $this->string;
	}
}

?>