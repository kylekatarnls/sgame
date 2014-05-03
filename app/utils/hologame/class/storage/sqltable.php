<?php

namespace Hologame;

class Storage°SqlTable extends Object
{
	protected $gColumn = [];
	public function __construct($gColumn = [])
	{
		$gColumn = (array) $gColumn;
		foreach($gColumn as $name => $data)
		{
			
		}
		$this->gColumn = $gColumn;
	}
	private function getCData($column, $num)
	{
		if(!isset($this->gColumn[$column]))
		{
			throw new SqlTableException("Column ".$column." not found", 2);
			return null;
		}
		return $this->gColumn[$column][$num];
	}
	public function getType($column)
	{
		return $this->getCData($column, 0);
	}
	public function getValue($column, $value)
	{
		
	}
}

class SqlTableException extends ObjectException {}

?>