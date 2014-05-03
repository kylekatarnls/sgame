<?php

namespace Hologame;

class Security°Field
{
	protected $name = 'username', $field;
	public function __construct($name = null)
	{
		if($name !== null)
		{
			$this->name = $name;
		}
		$this->field = new Html('input', [
			'type' => 'text',
			'name' => $this->name,
			'autocomplete' => 'off'
		]);
	}
	public function name($name)
	{
		$this->name = $name;
		$this->field->name = $name;
	}
	public function check($name = null, $cleanPostIfFilled = true, $method = 'request')
	{
		if(is_bool($name))
		{
			$cleanPostIfFilled = $name;
			$name = null;
		}
		if($name === null)
		{
			$name = $this->name;
		}
		if(function_exists('get_'.$method))
		{
			$method = 'get_'.$method;
		}
		else
		{
			$method = 'get_request';
		}
		if($method($name) === '')
		{
			return true;
		}
		if($cleanPostIfFilled)
		{
			$_POST = [];
		}
		return false;
	}
	public function html()
	{
		return (new Html([
			'class' => 'sliced'
		]))->append($this->field);
	}
	public function __toString()
	{
		return strval($this->html());
	}
}

?>