<?php

namespace Hologame;

class Html°Hbutton extends Html
{
	public function __construct()
	{
		$gArg = get_args(func_get_args());
		$content = array_value($gArg, 0, 'Button', 'string');
		$onclick = array_value($gArg, 1);
		$attributes = array_value($gArg, 2, [], 'array');
		if($onclick !== null)
		{
			$attributes['onclick'] = $onclick;
		}
		$attributes['content'] = $content;
		parent::__construct('a', $attributes);
		$this->addClass('h-button');
	}
}

?>