<?php

namespace Hologame;

class Security°Antibruteforce extends Object
{
	protected $gParam = [
		'key' => 'global',
		'ip' => '',
		'byIp' => 40,
		'ipTtl' => 120,
		'byKey' => 12,
		'keyTtl' => 30,
		'byBoth' => 5,
		'bothTtl' => 5
	];
	public function __construct($key = null, $ip = null)
	{
		$this->param('ip', ($ip !== null ? $ip : get_server('REMOTE_ADDR')));
		if($key !== null)
		{
			if(is_array($key))
			{
				$this->param($key);
			}
			else
			{
				$this->param('key', strval($key));
			}
		}
	}
	public function param($key, $value = null)
	{
		if(is_array($key))
		{
			if(is_num_array($key))
			{
				$return = [];
				foreach($key as $k)
				{
					$return[] = $this->param($k);
				}
				return $return;
			}
			foreach($key as $k => $value)
			{
				$this->param($k, $value);
			}
		}
		else
		{
			if($value === null)
			{
				return $this->gParam[$key];
			}
			$this->gParam[$key] = (is_string($this->gParam[$key]) ? strval($value) : max(0, intval($value)));
		}
	}
	public function call($gArg)
	{
		if(!empty($gArg[0]))
		{
			$this->param('key', strval($gArg[0]));
		}
		if(!empty($gArg[1]))
		{
			$this->param('ip', strval($gArg[1]));
		}
		return $this;
	}
	public function getText($name)
	{
		switch($name)
		{
			case 'banned': return s('Votre adresse IP a été bloquée pour des raisons de sécurité');
			case 'locked': return s('Ce formulaire est momentanément verrouillé.');
			case 'bruteForce': return p(
				'Trop de tentatives, veuillez patienter {number} minute',
				'Trop de tentatives, veuillez patienter {number} minutes',
				$this->getLockTime()
			);
		}
		return false;
	}
	public function getLockTime()
	{
		$ip = $this->param('ip');
		$key = $this->param('key');
		$both = $ip.'-'.$key;
		$gTtl = [0];
		foreach(['ip', 'key', 'both'] as $var)
		{
			$value = $$var;
			$by = $this->param('by'.ucfirst($var));
			if($by-$this->mem->get('brute-force-'.$var.'-'.$value) < 1)
			{
				$gTtl[] = $this->param($var.'Ttl');
			}
		}
		return max($gTtl);
	}
	public function check(&$info)
	{
		$ip = $this->param('ip');
		$key = $this->param('key');
		$both = $ip.'-'.$key;
		if($this->mem->get('banned-'.$ip) === true)
		{
			$info = 'banned';
			return false;
		}
		if($this->mem->get('locked-'.$key) === true)
		{
			$info = 'locked';
			return false;
		}
		$gCount = [];
		foreach(['ip', 'key', 'both'] as $var)
		{
			$value = $$var;
			$by = $this->param('by'.ucfirst($var));
			$gCount[] = $by-$this->mem->get('brute-force-'.$var.'-'.$value);
		}
		$info = min($gCount);
		return ($info > 0);
	}
	public function put()
	{
		$ip = $this->param('ip');
		$key = $this->param('key');
		$both = $ip.'-'.$key;
		if(
			$this->mem->get('banned-'.$ip) === true ||
			$this->mem->get('locked-'.$key) === true
		)
		{
			return false;
		}
		foreach(['ip', 'key', 'both'] as $var)
		{
			$value = $$var;
			$ttl = $this->param($var.'Ttl');
			$this->mem->inc('brute-force-'.$var.'-'.$value, $ttl*60);
		}
		return true;
	}
}

?>