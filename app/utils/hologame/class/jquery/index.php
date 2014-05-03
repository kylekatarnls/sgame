<?php

namespace Hologame;

class Jquery extends Object
{
	public function __call($function, array $params)
	{
		$function = '$.'.$function;
		return $this->cJavascript->$function($params);
	}
	public function raw($js)
	{
		return $this->cJavascript->raw('$.'.$js); // ajoute
	}
	public function __toString()
	{
		return $this->cJavascript->out();
	}
}

?>