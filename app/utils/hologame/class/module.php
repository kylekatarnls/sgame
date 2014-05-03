<?php

namespace Hologame;

class Module extends Object
{
	protected $module = '';
	public function __construct()
	{
		for(
			$module = $class = get_class($this);
			($class = get_parent_class($class)) != 'Module';
			$module = $class
		);
		$this->module = $module;
		if(!get_module($module))
		{
			if(get_constant('DEVMODE'))
			{
				throw new ModuleException("You must enable $module module in adding the line below in a file in the host configuration directory\nmodule(".var_export($module, true).");", 1);
			}
			else
			{
				$this->error404();
			}
		}
	}
}

class ModuleException extends Exception {}

?>