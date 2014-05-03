<?php

namespace Hologame;

class Html°Attributes extends ArrayObjectAccessor
{
	public function __toString()
	{
		return Html::htmlAttributes($this);
	}
}

?>