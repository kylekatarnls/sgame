<?php

namespace Hologame;

class Jquery°Call extends Object
{
	protected $js = '', $closure = null;
	public function __construct($params)
	{
		if($params instanceof Jquery°Closure)
		{
			$this->closure = $params;
			$selector = $params->getName();
		}
		else
		{
			list($selector) = (array) $params;
		}
		$this->js = '$('.get_string_or_raw('json_encode', $selector).')';
	}
	public function __destruct()
	{
		if(!is_null($this->closure))
		{
			$this->closure->raw($this->js());
		}
		elseif(!empty($this->js))
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