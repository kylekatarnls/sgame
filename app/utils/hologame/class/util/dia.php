<?php

namespace Hologame;

class Util°Dia
{
	protected $data = [];
	public function __construct($source = null)
	{
		if(is_array($source))
		{
			$this->fromArray($source);
		}
		else if(is_string($source))
		{
			$this->fromString($source);
		}
	}
	public function __get($name)
	{
		return $this->fromArray($this->data[
			start($name, 'i') && is_numeric($i = substr($name, 1)) ?
				$i :
				'DIV:'.strtoupper($name)
		]);
	}
	public function getData()
	{
		return $this->data;
	}
	public function fromArray(array $array)
	{
		if(isset($array['DIA:DIAGRAM']))
		{
			$array = $array['DIA:DIAGRAM'][0]['child']['DIA:LAYER'][0]['child']['DIA:OBJECT'];
		}
		if(isset($array[0], $array[0]['child']))
		{
			foreach($array as &$value)
			{
				$value = $value['child'];
			}
		}
		$this->data = $array;
		return $this;
	}
	public function fromString($string)
	{
		if(!start($string, '<'.'?xml'))
		{
			$string = gzdecode($string);
		}
		list($encoding) = end_separator("\n", $string);
		if(preg_match('#encoding\s*=\s*[\'"]([^\'"]+)[\'"]#', $encoding, $match))
		{
			$encoding = $match[1];
		}
		else
		{
			$encoding = 'UTF-8';
		}
		return $this->fromArray((new Util°Xml)->parse($string, $encoding));
	}
	public function fromFile($file)
	{
		$string = file_get_contents($file);
		return $this->fromString($string);
	}
}

?>