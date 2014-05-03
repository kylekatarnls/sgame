<?php

namespace Hologame;

class Html°Collection extends Object
{
	protected $childNodes = [];

	const AUTOCLOSE = 'area br hr img input link meta param';
	const URL_ATTRIBUTES = 'href src action';

	public function __construct($args)
	{
		$args = get_args(func_get_args());
		if(count($args) === 1 && (is_array($args[0]) || is_string($args[0])))
		{
			$args = [new Html($args[0])];
		}
		else if(count($args) === 2 && is_string($args[0]) && is_array($args[1]))
		{
			$args = [new Html($args[0], $args[1])];
		}
		foreach($args as $arg)
		{
			$this->append($arg);
		}
	}
	public function append($child)
	{
		if(is_string($child) || is_a($child, 'Html'))
		{
			$this->childNodes[] = $child;
			return true;
		}
		return false;
	}
	public function __toString()
	{
		return implode('', $this->childNodes);
	}
}

?>