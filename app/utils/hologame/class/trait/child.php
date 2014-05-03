<?php

namespace Hologame;

trait Trait°Child
{
	protected $gChild = [], $rootClass, $childPath = false, $gPath = false;
	public function loadChild($child)
	{
		$className = get_class($this);
		for(
			$newChild = $child, $root = $class = $className;
			in_string('°', $class) && method_exists($class, 'loadChild');
			list($class, $newChild) = end_separator('°', $root = $class)
		)
		{
			$gChild[] = $newChild;
		}
		$this->rootClass = $root;
		$this->gChild = array_reverse(array_slice($gChild, 1));
		if(start($root, 'Page°'))
		{
			$query = substr(str_replace('°', '/', strtolower($root)), 5);
			if(start(QUERY_STRING, $query) && QUERY_STRING !== $query)
			{
				$this->childPath = ltrim(substr(QUERY_STRING, strlen($query)), '/');
				$this->gPath = explode('/', $this->childPath);
			}
		}
		$childMatch = get_constant($className.'::CHILD_MATCH');
		if($childMatch !== null)
		{
			$this->checkChild($childMatch, (get_constant($className.'::ALLOW_EMPTY_CHILD') !== false));
		}
		return true;
	}
	protected function checkChild($regExp = null, $allowEmpty = true)
	{
		if(empty($this->childPath))
		{
			if(!$allowEmpty)
			{
				$this->error404();
			}
		}
		else if($regExp !== null && !preg_match('#^'.$regExp.'?$#', $this->childPath))
		{
			$this->error404();
		}
	}
}

?>