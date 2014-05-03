<?php

namespace Hologame;

class Url°Get extends ArrayObjectAccessor
{
	public function uriParams($subParam = null)
	{
		if(is_null($subParam))
		{
			$params = [];
			foreach($this as $key => $value)
			{
				if(is_object($value) && method_exists($value, 'uriParams'))
				{
					$params[] = urlencode($key) . '=' . $value->uriParams($key);
				}
				else
				{
					$params[] = urlencode($key) . '=' . urlencode($value);
				}
			}
			return $params === [] ? '' : '?' . implode('&', $params);
		}
		else
		{
			$params = [];
			foreach($this as $key => $value)
			{
				$key = $subParam.'['.$key.']';
				if(is_object($value) && method_exists($value, 'uriParams'))
				{
					$value = $value->uriParams($key);
				}
				$params[] = urlencode($key) . '=' . urlencode($value);
			}
			return implode('&', $params);
		}
	}
	public function __toString()
	{
		return $this->uriParams();
	}
}

?>