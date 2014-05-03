<?php

namespace Hologame;

abstract class Html°Util extends Html
{
	protected $utilData;
	public function __construct($data = [])
	{
		while(is_num_array($data) && is_array($data[0]))
		{
			$data = $data[0];
		}
		$this->utilData = (array) $data;
	}
	public function __call($name, array $gArg)
	{
		if(count($gArg) === 0)
		{
			return array_value($this->utilData, $name);
		}
		return $this->set($name, $gArg[0]);
	}
	public function set($name, $value)
	{
		$this->utilData[$name] = $value;
		return $this;
	}
	public function checkTagName()
	{
		if(!empty($this->tagName))
		{
			return true;
		}
		list(, $end) = end_separator('Html°', preg_replace('#°Call$#', '', get_class($this)));
		$html = £($this->getTemplate('util/'.str_replace('°', '/', strtolower($end))), $this->utilData);
		parent::__construct($html);
		return false;
	}
	public function __toString()
	{
		$this->checkTagName();
		return parent::__toString();
	}
}

?>