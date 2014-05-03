<?php

namespace Hologame;

class Html°Header extends Html°Util
{
	public function __construct()
	{
		$this->utilData = [
			'label' => $this->getData('main_title')
		];
	}
}

?>