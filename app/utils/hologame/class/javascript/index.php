<?php

namespace Hologame;

use Closure, ReflectionClass;

class Javascript extends Object
{
	protected $js = '', $delayJs = '';
	const DELAY_OPEN = '(function ($){var f=function (){';
	const DELAY_CLOSE = '};typeof($document)==="undefined"?$(f):$document.one("pagechange", f);})(jQuery);';
	static public function javascriptEncore($value)
	{
		if($value instanceof Closure)
		{
			ob_start();
			var_dump($value);
			$ct = ob_get_contents();
			ob_end_clean();
			preg_match_all(
				'#\["\$([^"]+)"\]#',
				preg_replace('#^(?:\n|.)*\["parameter"\]=>\s*array\([1-9][0-9]*\)\s*(\{((?>[^\{\}]+)|(?-2))*\})(?:\n|.)*$#', '$1', $ct),
				$matches
			);
			$params = $matches[1];
			unset($matches, $ct);
			return 'function (' . implode(', ', $params) . ') { ' . Jquery°Closure::execClosure($value, $params) . ' }';
		}
		return json_encode($value);
	}
	static public function params(array $params)
	{
		$params = get_array_or_raw([__CLASS__, 'javascriptEncore'], $params);
		return '('.implode(', ', $params).')';
	}
	static public function openRegex($code = '(.*)')
	{
		return '#'.preg_quote(self::DELAY_OPEN).$code.preg_quote(self::DELAY_CLOSE).'#sU';
	}
	public function __set($name, $value)
	{
		$this->raw('window.'.$name.'='.json_encode($value).';');
	}
	public function __call($function, array $params)
	{
		$this->js .= $function.self::params($params).';'."\n";
		return $this;
	}
	public function raw($js)
	{
		$reg = self::openRegex();
		if(preg_match($reg, $js, $match))
		{
			$js = preg_replace($reg, '', $js);
			$this->delayJs .= $match[1]."\n";
		}
		$this->js .= $js."\n";
		return $this;
	}
	public function delay($js, $delay = 0)
	{
		if($delay > 0)
		{
			$this->js .= 'setTimeout(function (){'.$js.'}, '.$delay.');'."\n";
		}
		else
		{
			$this->delayJs .= preg_replace(self::openRegex(), '$1;', $js)."\n";
		}
		return $this;
	}
	public function getContent()
	{
		return trim((empty($this->delayJs)?
			'':
			self::DELAY_OPEN.trim($this->delayJs,"\n").self::DELAY_CLOSE
		).$this->js, "\n");
	}
	public function out()
	{
		$js = $this->getContent();
		$this->js = '';
		$this->delayJs = '';
		return $js;
	}
	public function html()
	{
		$js = $this->out();
		if($js === '')
		{
			return '';
		}
		return new Html('script', [
			'content' => "\n".$js."\n",
			'type' => 'text/javascript'
		]);
	}
	public function __toString()
	{
		return $this->out();
	}
}

?>