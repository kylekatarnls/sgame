<?php

namespace Hologame;

use ArrayObject;

class Html extends Object
{
	use Trait°Renew;
	protected $tagName, $inLineStyle,
		$className = [],
		$attributes = [],
		$childNodes = [],
		$autoClose = false;

	const AUTOCLOSE = 'area br hr img input link meta param';
	const URL_ATTRIBUTES = 'href src action';
	const TAG_REG_EXP = '[a-z][a-z0-9:-]*';

	public function __construct($tagName, $attributes = null)
	{
		should_be_array_or_traversable($attributes);
		$this->className = new ArrayObject;
		$this->attributes = new Html°Attributes;
		$this->childNodes = new ArrayObject;
		if(is_array($tagName))
		{
			$attributes = $tagName;
			$tagName = 'div';
		}
		if(is_null($attributes))
		{
			$attributes = [];
		}
		if(isset($attributes['childNodes']))
		{
			$this->childNodes = $attributes['childNodes'];
			if(!is_array($this->childNodes))
			{
				$this->childNodes = [$this->childNodes];
			}
			unset($attributes['childNodes']);
		}
		else if(isset($attributes['content']))
		{
			$this->childNodes = $attributes['content'];
			if(!is_array($this->childNodes))
			{
				$this->childNodes = [$this->childNodes];
			}
			unset($attributes['content']);
		}
		$this->inLineStyle = new ArrayObject;
		$autoClose = null;
		$tagName = trim(strtr($tagName, "\n\r\t", '   '));
		if(preg_match('#^\<('.self::TAG_REG_EXP.')([^a-z0-9:-][^\>]*)?\>((.*)\</('.self::TAG_REG_EXP.')\>)?$#i', $tagName, $match))
		{
			$startTag = strtolower($match[1]);
			$this->tagName = $startTag;
			if(isset($match[2]))
			{
				if(finish($match[2], '/'))
				{
					$autoClose = true;
				}
				$this->addRawAttributes($match[2]);
			}
			if(isset($match[5]))
			{
				$endTag = strtolower($match[5]);
				if($startTag === $endTag)
				{
					$autoClose = false;
				}
				else
				{
					trigger_error('Only one parent child HTML node can be passed.', E_USER_NOTICE);
				}
				$this->childNodes = [$match[4]];
			}
			else
			{
				trigger_error('Only one parent child HTML node can be passed.', E_USER_NOTICE);
			}
		}
		else
		{
			$this->tagName = strtolower($tagName);
		}
		$this->autoClose($autoClose);
		if(!empty($attributes))
		{
			$this->attributes($attributes);
		}
	}
	public function attributes($attributes)
	{
		should_be_array_or_traversable($attributes);
		$this->checkTagName();
		foreach($attributes as $name => $value)
		{
			$this->__set($name, $value);
		}
		return $this;
	}
	public function attr($attributes)
	{
		return $this->attributes($attributes);
	}
	public function autoClose($state = null)
	{
		$this->checkTagName();
		if($state !== null)
		{
			$this->autoClose = $state;
		}
		else
		{
			$this->autoClose = in_array($this->tagName, explode(' ', self::AUTOCLOSE));
		}
		return $this;
	}
	public function compiledStyle($input = null)
	{
		$this->checkTagName();
		if($input === null)
		{
			$input = $this->inLineStyle;
		}
		$style = [];
		$input = (array) $input;
		foreach($input as $property => $value)
		{
			$style[] = strtolower(preg_replace('`[A-Z]`', '-$0', $property)).': '.$value.';';
		}
		return implode(' ', $style);
	}
	public function rawStyle($input = null, $cast = 'object')
	{
		$this->checkTagName();
		if($input === null)
		{
			return $this->inLineStyle;
		}
		$raw = [];
		$style = array_map('trim', explode(';', $input));
		foreach($style as $line)
		{
			$line = explode(':', $line, 2);
			if(count($line) === 2)
			{
				list($property, $value) = array_map('trim', $line);
				$raw[$property] = $value;
			}
		}
		settype($raw, $cast);
		return $raw;
	}
	public function style($properties)
	{
		$this->checkTagName();
		$this->inLineStyle = object_merge($this->inLineStyle, $properties);
		return $this;
	}
	public function addRawStyle($input = null)
	{
		return $this->style($this->rawStyle($input, 'array'));
	}
	public function compiledAttributes($input = null)
	{
		$this->checkTagName();
		if($input === null)
		{
			$input = $this->attributes;
		}
		return self::htmlAttributes($input);
	}
	static public function htmlAttributes($input)
	{
		should_be_array_or_traversable($input);
		$attributes = '';
		foreach($input as $name => $value)
		{
			$attributes .= ' '.$name.'="'.encode($value).'"';
		}
		return $attributes;
	}
	public function rawAttributes($input = null, $cast = 'object')
	{
		$this->checkTagName();
		if($input === null)
		{
			return $this->attributes;
		}
		$attributes = [];
		preg_match_all('#\s'.'('.self::TAG_REG_EXP.')\s*=\s*("[^"]*"|\'[^\']*\'|[0-9a-z]+)\s#i', ' '.preg_replace('#\s+#', '  ', $input).' ', $gMatch, PREG_SET_ORDER);
		foreach($gMatch as $attr)
		{
			$value = $attr[2];
			if(start($value, '"') || start($value, '\''))
			{
				$value = substr($value, 1, -1);
			}
			$attributes[$attr[1]] = decode($value);
		}
		settype($attributes, $cast);
		return $attributes;
	}
	public function addRawAttributes($input = null)
	{
		$this->checkTagName();
		$attributes = $this->rawAttributes($input, 'array');
		foreach($attributes as $name => $value)
		{
			$this->__set($name, $value);
		}
		return $this;
	}
	public function getClass()
	{
		$this->checkTagName();
		return implode(' ', array_keys((array) $this->className));
	}
	public function addClass($name)
	{
		$this->checkTagName();
		$this->className[$name] = true;
		return $this;
	}
	public function removeClass($name)
	{
		$this->checkTagName();
		unset($this->className[$name]);
		return $this;
	}
	public function setAttribute($name, $value)
	{
		$this->checkTagName();
		$urlAttributes = explode(' ', self::URL_ATTRIBUTES);
		if(in_array($name, $urlAttributes))
		{
			$this->attributes[$name] = new Url($value);
		}
		else
		{
			$this->attributes[$name] = $value;
		}
	}
	public function __set($name, $value)
	{
		$this->checkTagName();
		switch($name)
		{
			case 'childNodes':
				$this->childNodes = $value;
				break;
			case 'tagName':
				$this->tagName = strtolower($value);
				$this->autoClose();
				break;
			case 'style':
				$this->inLineStyle = is_array($value) ? (object) $value : $this->rawStyle($value);
				break;
			case 'class':
			case 'className':
				$this->className = [$value => true];
				break;
			default;
				$this->setAttribute($name, $value);
		}
	}
	public function __get($name)
	{
		$this->checkTagName();
		switch($name)
		{
			case 'childNodes':
			case 'tagName':
				return $this->$name;
			case 'style':
				return $this->inLineStyle;
				//return $this->compiledStyle();
			case 'class':
			case 'className':
				return $this->getClass();
			default:
				$urlAttributes = explode(' ', self::URL_ATTRIBUTES);
				if(isset($this->attributes->$name) && in_array($name, $urlAttributes))
				{
					return $this->attributes->$name;
				}
				else
				{
					return array_value($this->attributes, $name);
				}
		}
	}
	public function __isset($name)
	{
		$this->checkTagName();
		return isset($this->attributes[$name]);
	}
	public function __unset($name)
	{
		$this->checkTagName();
		unset($this->attributes[$name]);
	}
	public function append($child)
	{
		$this->checkTagName();
		$this->childNodes[] = $child;
		return $this;
	}
	public function appendTo($parent)
	{
		$this->checkTagName();
		if(!is_a($parent, 'Html'))
		{
			$parent = new Html($parent);
		}
		$parent->append($this);
		return $this;
	}
	public function __toString()
	{
		$class = $this->getClass();
		if(!empty($class))
		{
			$this->attributes['class'] = $class;
		}
		$style = $this->compiledStyle();
		if(!empty($style))
		{
			$this->attributes['style'] = $style;
		}
		$html = '<'.$this->tagName.$this->compiledAttributes();
		if($this->autoClose)
		{
			$html .= ' />';
		}
		else
		{
			$html .= '>'.implode('', $this->childNodes).
			'</'.$this->tagName.'>';
		}
		return $html;
	}
	public function checkTagName()
	{
		return true;
	}
}

?>