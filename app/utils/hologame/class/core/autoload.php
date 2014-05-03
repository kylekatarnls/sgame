<?php

namespace Hologame;

class Core°Autoload
{
	public function __construct()
	{
		spl_autoload_register(array($this, 'autoload'));
	}
	public function autoload($className)
	{
		$path = strtolower(str_replace('°', '/', $className));
		$directories = [
			'Core§' => CORE_DIR,
			'Host§' => HOST_DIR
		];
		foreach($directories as $prefix => $directory)
		{
			if(istart($path, $prefix))
			{
				$alias = substr($className, strlen($prefix));
				$file = $directory.CLASS_REL_DIR.substr($path, strlen($prefix)).'.php';
				if(!file_exists($file))
				{
					$file = false;
				}
				break;
			}
		}
		if(!isset($file))
		{
			$file = host_or_core(CLASS_REL_DIR.$path, '.php', true);
		}
		if($file !== false)
		{
			include_once($file);
			if(isset($alias))
			{
				if(!alias($alias, $className))
				{
					throw new ClassException('class/trait '.$className.' extends '.$alias.' {}'."\n".$alias.'does not exist', 1);
				}
			}
			else if(!exists($className, false))
			{
				foreach($directories as $prefix => $dir)
				{
					if(exists($prefix.$className))
					{
						alias($prefix.$className, $className);
					}
				}
			}
				
		}
		if(in_string('°', $className))
		{
			list($parent, $child) = end_separator('°', $className);
			if(exists($parent)
			&& method_exists($parent, 'loadChild'))
			{
				global $classChild;
				if(empty($classChild))
				{
					$classChild = [];
				}
				$classChild[$className] = $child;
				$object = new $parent;
				if($object->loadChild($child))
				{
					alias($parent, $className);
				}
			}
		}
	}
}

class ClassException extends Exception {}

class Core§Core°Autoload extends Core°Autoload {}

?>