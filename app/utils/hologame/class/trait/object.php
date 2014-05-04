<?php

namespace Hologame;

trait Trait°Object
{
	private function shortcut($key)
	{
		$shortcuts = [
			'mem' => 'Storage°Mem',
			'lmem' => 'Storage°Hostmem',
			'session' => 'Storage°Session',
			'cookie' => 'Storage°Cookie',
			'cfile' => 'Storage°Cookie°File',
			'mem°Call' => 'Storage°Mem°Call',
			'sql' => 'Storage°Sql',
			'js' => 'Javascript',
			'§' => 'Jquery',
			'§°Call' => 'Jquery°Call',
			'µ' => 'Jquery',
			'µ°Call' => 'Jquery°Call',
			'$' => 'Jquery',
			'$°Call' => 'Jquery°Call',
			'button°Call' => 'Html°Button',
			'form' => 'Html°Form'
		];
		return array_value($shortcuts, $key, false);
	}
	protected function getClassName($name)
	{
		// Raccourcis
		switch($name)
		{
			case '§this':
			case 'µthis':
			case '$this':
				return $this->§(raw('this'));
		}
		$shortcut = $this->shortcut($name);
		if($shortcut !== false)
		{
			return $shortcut;
		}
		// Si la deuxième lettre est une majuscule, la première lettre définit le type de l'attribut
		if(is_upper(char_at($name, 1)))
		{
			switch(char_at($name))
			{
				case 'c':
					return HOLOGAME_NAMESPACE . '\\' . substr($name, 1);
				case 'p':
					return HOLOGAME_NAMESPACE . '\\Page°' . substr($name, 1);
				case 'h':
					return HOLOGAME_NAMESPACE . '\\Html°' . substr($name, 1);
				case 's':
					$sql = new Storage°Sql;
					return $sql->selectTable(lcfirst(substr($name, 1)));
				case 'f':
					return new Storage°File°Serialize(substr($name, 1));
			}
		}
		return false;
	}
	protected function getTemplate($file = null)
	{
		if($file === null)
		{
			$class = (isset($this->rootClass) ? $this->rootClass : get_class($this));
			$file = str_replace('°', '/', strtolower($class));
		}
		return new Template($file);
	}
	public function __call($function, array $gParam)
	{
		$className = $this->getClassName($function.'°Call');
		if($className !== false && class_exists($className))
		{
			return new $className($gParam);
		}
		$object = $this->{$function};
		if(!empty($object) && is_object($object))
		{
			if(method_exists($object, 'call'))
			{
				return $object->call($gParam);
			}
			if(method_exists($object, 'cCall'))
			{
				return call_user_func_array([$object, 'cCall'], $gParam);
			}
		}
		if(count($gParam) === 1)
		{
			return $this->setData($function, $gParam[0]);
		}
		throw new ObjectException("$function function and $className class name not found", 2);
		return false;
	}
	private function data($get, $value = null)
	{
		global $__DATA__;
		if(!isset($__DATA__) || !is_array($__DATA__))
		{
			$__DATA__ = [];
		}
		if($get)
		{
			return $__DATA__;
		}
		else
		{
			$__DATA__ = $value;
		}
	}
	public function getData($name, $default = null, $type = null, $nullIfEmpty = false)
	{
		return array_value($this->data(true), $name, $default, $type, $nullIfEmpty);
	}
	public function setData($name, $value)
	{
		$data = $this->data(true);
		$data[$name] = $value;
		return $this->data(false, $data);
	}
	public function removeData($name)
	{
		$data = $this->data(true);
		if(!isset($data[$name]))
		{
			return false;
		}
		unset($data[$name]);
		return $this->data(false, $data);
	}
	public function removeAllData()
	{
		return $this->data(false, []);
	}
	public function callData($name, $function)
	{
		$data = $this->data(true);
		if(isset($data[$name]) && is_callable($function))
		{
			$data[$name] = $function($data[$name]);
			return $this->data(false, $data);
		}
		return false;
	}
	public function mergeDataIfNot($name)
	{
		$data = $this->data(true);
		$data[$name] = array_value($data, $name, [], 'array');
		for($i=1; $i<func_num_args(); $i++)
		{
			$data[$name] = array_merge(func_get_arg($i), (array) $data[$name]);
		}
		return $this->data(false, $data);
	}
	public function mergeData($name)
	{
		$data = $this->data(true);
		$data[$name] = array_value($data, $name, [], 'array');
		for($i=1; $i<func_num_args(); $i++)
		{
			$data[$name] = array_merge($data[$name], (array) func_get_arg($i));
		}
		return $this->data(false, $data);
	}
	public function pushData($name)
	{
		$data = $this->data(true);
		$data[$name] = array_value($data, $name, [], 'array');
		for($i=1; $i<func_num_args(); $i++)
		{
			$data[$name][] = func_get_arg($i);
		}
		return $this->data(false, $data);
	}
	public function addData($name, $value)
	{
		$data = $this->data(true);
		if(is_array($value))
		{
			$data[$name] = array_value($data, $name, [], 'array')+$value;
		}
		else
		{
			$data[$name] = array_value($data, $name, '').$value;
		}
		return $this->data(false, $data);
	}
	public function setDataIfNot($name, $value)
	{
		$data = $this->data(true);
		if(isset($data[$name]))
		{
			return false;
		}
		$data[$name] = $value;
		return $this->data(false, $data);
	}
	public function __set($name, $value)
	{
		switch($name)
		{
			case 'style':
				return $this->setData('style', $value);
			case 'data':
				$this->data(false, $value);
		}
	}
	public function __get($name)
	{
		switch($name)
		{
			case 'style':
				$style = $this->getData('style');
				if(is_null($style))
				{
					$style = new Style;
					$this->setData('style', $style);
				}
				return $style;
		}
		global $attributes;
		if(!is_array($attributes))
		{
			$attributes = [];
		}
		if($name === 'data')
		{
			return $this->data(true);
		}
		$child = null;
		if(isset($attributes[$name]))
		{
			$class = $attributes[$name];
		}
		else
		{
			$className = $this->getClassName($name);
			if($className !== false)
			{
				if(is_object($className))
				{
					$class = $className;
				}
				else if(isset($attributes[$className]))
				{
					$class = $attributes[$className];
				}
				else if(class_exists($className))
				{
					$class = new $className;
				}
				else
				{
					$error = true;
					if(in_string('°', $className))
					{
						list($parent, $child) = end_separator('°', $className);
						if(class_exists($parent) && method_exists($parent, 'loadChild'))
						{
							alias($parent, $className);
							$class = new $parent;
							$error = false;
						}
					}
					if($error)
					{
						throw new ObjectException("La classe ".$className." n'existe pas", 3);
						return null;
					}
				}
			}
		}
		if(isset($class))
		{
			if(method_exists($class, 'loadChild'))
			{
				if($child === null)
				{
					global $classChild;
					if(!isset($className))
					{
						$className = $this->getClassName($name);
					}
					if(isset($classChild[$className]))
					{
						$child = $classChild[$className];
					}
				}
				$class->loadChild($child);
			}
			if(!method_exists($class, 'renew') || !$class->renew())
			{
				if(isset($className) && is_string($className))
				{
					$name = $className;
				}
				$attributes[$name] = $class;
			}
			return $class;
		}
		else
		{
			return isset($this->$name) ? $this->$name : null;
		}
	}
	public function addRessource($url, $type, $directory, $path, $extra = null, $once = true)
	{
		$methods = [
			'js' => 'addScript',
			'css' => 'addStyle',
		];
		if(!isset($methods[$type]))
		{
			throw new ObjectException("Le type de ressource $type n'est pas reconnu.", 4);
			return false;
		}

		$url = ressource_href($url, $type, null, [$directory => $path]);

		if($url === false)
		{
			throw new ObjectException("La ressource ".$directory.'public/'.$type.'/'.$url.'.'.$type." est introuvable.", 5);
			return false;
		}

		$method = $methods[$type];
		return $this->$method($url, $extra, $once);
	}
	public function addScript($url, $place = null, $once = true)
	{
		if(is_ajax())
		{
			$place = 'body';
		}
		else if(is_null($place))
		{
			$place = 'head';
		}
		if($once)
		{
			global $onceScript;
			if(!is_array($onceScript))
			{
				$onceScript = [];
			}
			if(in_array($url, $onceScript))
			{
				return false;
			}
			else
			{
				$onceScript[] = $url;
			}
		}
		if(!is_object($url) || !is_a($url, 'Url'))
		{
			$url = new Url($url);
		}
		$scripts = $this->getData('scripts', [], 'array');
		if(!isset($scripts['head']))
		{
			$scripts['head'] = [];
		}
		if(!isset($scripts['body']))
		{
			$scripts['body'] = [];
		}
		if(isset($scripts[$place]))
		{
			$scripts[$place][] = $url;
			$this->setData('scripts', $scripts);
			return true;
		}
		else
		{
			throw new ObjectException("L'emplacement ".$place." n'existe pas", 6);
			return false;
		}
	}
	public function addCoreScript($url, $place = null, $once = true)
	{
		return $this->addRessource($url, 'js', CORE_DIR, 'c%', $place, $once);
	}
	public function addHostScript($url, $place = null, $once = true)
	{
		return $this->addRessource($url, 'js', HOST_DIR, 's%/'.H_HOST, $place, $once);
	}
	public function addStyle($url, $media = null, $once = true)
	{
		$place = is_ajax() ? 'body' : 'head';
		if(is_null($media))
		{
			$media = 'screen';
		}
		if($once)
		{
			global $onceStyle;
			if(!is_array($onceStyle))
			{
				$onceStyle = [];
			}
			if(in_array($url, $onceStyle))
			{
				return false;
			}
			else
			{
				$onceStyle[] = $url;
			}
		}
		if(!is_object($url) || !is_a($url, 'Url'))
		{
			$url = new Url($url);
		}
		$styles = $this->getData('styles', [], 'array');
		if(!isset($styles['head']))
		{
			$styles['head'] = [];
		}
		if(!isset($styles['body']))
		{
			$styles['body'] = [];
		}
		$styles[$place][] = [
			'href' => $url,
			'media' => $media
		];
		$this->setData('styles', $styles);
		return true;
	}
	public function googleFont($font, $subset = 'latin,latin-ext')
	{
		return $this->addStyle('http://fonts.googleapis.com/css?family='.$font.'&amp;subset='.$subset);
	}
	public function addCoreStyle($url, $media = null, $once = true)
	{
		return $this->addRessource($url, 'css', CORE_DIR, 'c%', $media, $once);
	}
	public function addHostStyle($url, $media = null, $once = true)
	{
		return $this->addRessource($url, 'css', HOST_DIR, 's%/'.H_HOST, $media, $once);
	}
	public function addMeta()
	{
		$metas = $this->getData('metas', [], 'array');
		foreach(func_get_args() as $arg)
		{
			$meta = [];
			foreach((array) $arg as $name => $value)
			{
				$meta[] = [
					'name' => $name,
					'value' => $value
				];
			}
			$metas[] = $meta;
		}
		$this->setData('metas', $metas);
	}
	public function favicon($place = 'headContent')
	{
		$this->addData($place, new Html('link', [
			'rel' => 'shortcut icon',
			'type' => 'image/'.$this->cUtil°Favicon->format,
			'href' => 'favicon.ico'
		]));
	}
	public function mobile($width = 'device-width', $ini = '1.0', $max = null, $min = null, $scalable = 'no', $app = 'yes')
	{
		if(is_null($max))
		{
			$max = $ini;
		}
		if(is_null($min))
		{
			$min = $ini;
		}
		$this->addMeta([
			'name' => 'viewport',
			'content' => 'width='.$width.', initial-scale='.$ini.', maximum-scale='.$max.', minimum-scale='.$min.', user-scalable='.$scalable.', target-densitydpi=device-dpi',
		],[
			'apple-mobile-web-app-capable' => $app
		],[
			'format-detection' => 'telephone=no'
		]);
	}
	public function bootstrap()
	{
		$this->addCoreScript('core', 'body');
		$this->addScript('http://bootstrap.selfbuild.fr/js/.js', 'body');
		$this->addStyle('http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css');
		$this->addData('headContent', '
		<!--[if lt IE 9]>
		<script src="http://bootstrap.selfbuild.fr/js/ie8.js"></script>
		<![endif]-->');
	}
	public function setBloc($bloc, $content)
	{
		$gBlocFile = $this->getData('gBlocFile', [], 'array');
		$gBlocFile['bloc/'.$bloc] = strval($content);
		$this->setData('gBlocFile', $gBlocFile);
	}
	public function forward($url)
	{
		header('Location: '.$url);
		exit;
	}
	public function showPage($page)
	{
		if($this->cPage->exists($page))
		{
			$this->{'p'.$page}->show();
		}
		else
		{
			$this->error404();
		}
		exit;
	}
	public function error404()
	{
		if($this->cPage->exists('Error404'))
		{
			$this->pError404->show();
		}
		else
		{
			throw new ObjectException("Erreur 404 : Page introuvable", 7);
		}
		exit;
	}
}

class ObjectException extends Exception {}

?>