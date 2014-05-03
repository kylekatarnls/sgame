<?php

namespace Hologame;

trait Trait°Output
{
	use Trait°Object;
	protected $charset = 'UTF-8';
	public function exists($page)
	{
		$page = ltrim($page, '°');
		if(class_exists('Page°'.$page))
		{
			$page = 'p'.$page;
			$pPage = $this->$page;
			if(method_exists($pPage, 'show'))
			{
				return true;
			}
		}
		return false;
	}
	public function headers()
	{
		switch($this->type)
		{
			case TYPE_PAGE:
				$this->mergeDataIfNot('headers', ['Content-type' => 'text/html; charset='.$this->charset ]);
				break;
			case TYPE_AJAX:
				$this->mergeDataIfNot('headers', ['Content-type' => 'application/json; charset='.$this->charset ]);
				break;
		}
		foreach($this->getData('headers', [], 'array') as $name => $value)
		{
			header($name.': '.$value);
		}
		$this->removeData('headers');
	}
	abstract public function show();
	//abstract public function main();
}

?>