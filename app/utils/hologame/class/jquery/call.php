<?php

namespace Hologame;

class Jquery°Call extends Object
{
	protected $js = '';
	public function __construct($params)
	{
		list($selector) = $params;
		$this->js = '$('.get_string_or_raw('json_encode', $selector).')';
	}
	public function __destruct()
	{
		if(!empty($this->js))
		{
			$this->cJavascript->raw($this->js());
		}
	}
	public function __call($function, array $params)
	{
		$this->js .= '.'.$function.Javascript::params($params);
		return $this;
	}
	public function __toString()
	{
		return $this->js();
	}
	public function js()
	{
		$js = $this->js.';';
		$this->js = '';
		return $js;
	}
}

?>