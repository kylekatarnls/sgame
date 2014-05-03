<?php

namespace Hologame;

class Style extends Three
{
	protected $html;
	public function __construct($html = true)
	{
		$this->html = !!$html;
	}
	public function __toString()
	{
		$data = $this->out();
		if(empty($data))
		{
			return '';
		}
		$return = '';
		foreach($data as $sel => $gProp)
		{
			foreach($gProp as $prop => &$value)
			{
				$value = $prop.':'.$value;
			}
			$return .= $sel.'{'.implode(';', $gProp).'}';
		}
		return $this->html ? '<style type="text/css">'.$return.'</style>' : $return;
	}
}

?>