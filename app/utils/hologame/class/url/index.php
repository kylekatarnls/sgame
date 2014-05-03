<?php

namespace Hologame;

class Url
{
	protected $full = false, $protocole = 'http', $domaine = '', $directory = '', $file = '', $get, $hash = '';
	public function __construct($url = null)
	{
		$this->domaine = $_SERVER['HTTP_HOST'];
		$this->protocole = (!empty($_SERVER['HTTPS']) ? 'https' : 'http');
		$this->get = new Url°Get;
		if($url !== null)
		{
			$this->change($url);
		}
	}
	public function change($url, $full = null)
	{
		if(start($url, '/'))
		{
			$url = HTTP_ROOT . substr($url, 1);
		}
		else if(preg_match('#^([a-z]+)://([^/]+)((/.*)?)$#i', $url, $match))
		{
			list(, $this->protocole, $this->domaine, $url) = $match;
			// $_SERVER['REQUEST_URI'] /truc/machin?bazar
			// $_SERVER['REDIRECT_URL'] /truc/machin
		}
		else if(preg_match('#^://([^/]+)((/.*)?)$#i', $url, $match))
		{
			list(, $this->domaine, $url) = $match;
		}
		if($full === true)
		{
			$this->full = true;
		}
		if(!empty($url))
		{
			$this->hash = '';
			$diese = strpos($url, '#');
			if($diese !== false)
			{
				$this->hash = substr($url, $diese);
				$url = substr($url, 0, $diese);
			}
			if(!empty($url))
			{
				$this->get = new Url°Get;
				$intdot = strpos($url, '?');
				if($intdot !== false)
				{
					$get = substr($url, $intdot+1);
					$url = substr($url, 0, $intdot);
					foreach(explode('&', $get) as $var)
					{
						$pieces = explode('=', $var);
						if(isset($pieces[1]))
						{
							$this->get[$pieces[0]] = urldecode($pieces[1]);
						}
						else
						{
							$this->get[$pieces[0]] = null;
						}
					}
				}
				if(!empty($url))
				{
					$slash = strrpos($url, '/');
					if($slash !== false)
					{
						$this->file = substr($url, $slash+1);
						$this->directory = substr($url, 0, $slash+1);
					}
					else
					{
						$this->file = $url;
					}
				}
			}
		}
	}
	public function &__get($name)
	{
		$value = isset($this->$name)? $this->$name : '';
		return $value;
	}
	public function __set($name, $value)
	{
		if(isset($this->$name))
		{
			settype($value, gettype($this->$name));
			$this->$name = $value;
		}
	}
	public function __toString()
	{
		return $this->href();
	}
	public function href()
	{
		$href = '';
		$protocole = (empty($_SERVER['HTTPS']) ? 'http' : 'https');
		$domaine = $_SERVER['HTTP_HOST'];
		if($this->full || $this->protocole !== $protocole || $this->domaine !== $domaine)
		{
			$href .= $this->protocole.'://'.$this->domaine;
		}
		$href .= $this->directory.$this->file.$this->get;
		/*
		$get = (array) $this->get;
		$first = true;
		foreach($get as $name => $value)
		{
			if($first)
			{
				$href .= '?';
				$first = false;
			}
			else
			{
				$href .= '&';
			}
			$href .= $name;
			if($value !== null)
			{
				$href .= '='.urlencode($value);
			}
		}
		*/
		if(start($this->hash, '#'))
		{
			$href .= $this->hash;
		}
		else if(!empty($this->hash))
		{
			$href .= '#'.$this->hash;
		}
		return $href;
	}
}
?>