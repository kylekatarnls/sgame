<?php

namespace Hologame;

class Html°Form extends Html°Util
{
	protected $autocomplete = true;
	public function __construct()
	{
		parent::__construct('form');
		$this->utilData['content'] = '';
	}
	public function __call($method, array $gArg)
	{
		switch($method)
		{
			case 'autocomplete':
				$this->autocomplete = array_value($gArg, 0, true, 'bool');
				return $this;
			case 'secure':
				$secure = prop('cSecurity°Field');
				if(isset($gArg[0]))
				{
					$secure->name($gArg[0]);
				}
				$this->utilData['content'] .= $secure;
				return $this;
			case 'raw':
				foreach($gArg as $arg)
				{
					$this->utilData['content'] .= $arg;
				}
				return $this;
		}
		try
		{
			$template = $this->getTemplate('util/form/'.$method);
			foreach($gArg as $arg)
			{
				if(array_value($arg, 'autocomplete', $this->autocomplete, 'bool') === false)
				{
					$arg['field'] = array_value($arg, 'field', '').' autocomplete="off"';
				}
				$this->utilData['content'] .= £($template, $arg);
			}
			return $this;
		}
		catch(TemplateException $e)
		{
			if($e->getCode() === 1)
			{
				return parent::__call($method, $gArg);
			}
			throw $e;
		}
		return false;
	}
}

?>