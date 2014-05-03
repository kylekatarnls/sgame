<?php

namespace Hologame;

class Storage°File°Serialize
{
	protected $file, $initialData = [], $data = [], $memOn = false;
	public function __construct($file)
	{
		if(char_at($file) === 'M' && is_upper(char_at($file, 1)))
		{
			$this->memOn = true;
			$file = substr($file, 1);
		}
		$inCore = false;
		if(char_at($file) === 'C' && is_upper(char_at($file, 1)))
		{
			$inCore = true;
			$file = substr($file, 1);
		}
		$this->file = str_replace('°', '/', strtolower($file));
		if($this->memOn && (new Object)->mem->exists('file-'.$this->file, $data) && is_array($data))
		{
			$this->data = $data;
		}
		else
		{
			$this->data = Storage°File::getContent($this->file, [], $inCore ? 'CORE' : 'HOST');
			$this->initialData = $this->data;
			if(is_string($this->data))
			{
				$this->data = unserialize($this->data);
				if(!is_array($this->data))
				{
					$this->data = [];
				}
			}
		}
	}
	public function get($name, $default = null, $type = null, $nullIfEmpty = false)
	{
		return array_value($this->data, $name, $default, $type, $nullIfEmpty);
	}
	public function __get($name)
	{
		return $this->get($name);
	}
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}
	public function __isset($name)
	{
		return isset($this->data[$name]);
	}
	public function reset()
	{
		$this->data = $this->initialData;
	}
	public function __destruct()
	{
		if($this->initialData !== $this->data)
		{
			Storage°File::putContent($this->file, serialize($this->data));
			if($this->memOn)
			{
				(new Object)->mem->set('file-'.$this->file, $this->data);
			}
		}
	}
}