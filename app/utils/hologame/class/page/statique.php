<?php

namespace Hologame;

class Page°Statique extends Page
{
	public function main()
	{
		$this->setData('title', £('Statique - {{ main_title }}'));
	}
}

?>