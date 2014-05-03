<?php

namespace Hologame;

trait Trait°Option
{
	protected $options = 0;
	public function addOption($flag, $return = null)
	{
		$this->options |= $flag;
		return (func_num_args() > 1 ? $return : $this);
	}
	public function removeOption($flag, $return = null)
	{
		$this->options &= ~$flag;
		return (func_num_args() > 1 ? $return : $this);
	}
	public function switchOption($flag, $return = null)
	{
		$this->options ^= $flag;
		return (func_num_args() > 1 ? $return : $this);
	}
	public function option($flag)
	{
		return (($this->options & $flag) !== 0);
	}
	public function setOption($option = null, $state = true, $return = null)
	{
		$this->{($state? 'add' : 'remove').'Option'}($option);
		return (func_num_args() > 2 ? $return : $this);
	}
}

?>