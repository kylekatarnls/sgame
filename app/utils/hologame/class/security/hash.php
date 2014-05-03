<?php

namespace Hologame;

class Security°Hash
{
	protected $string = '';
	public function __construct($input = null, $salt = null, $raw = null)
	{
		if($input !== null)
		{
			$this->string = self::hash($input, $salt);
		}
	}
	static public function hash($input, $salt = null, $raw = null)
	{
		if($salt === null)
		{
			$salt = '~@`_\uJ4dh{[--^eSz';
		}
		$input = bin2hex($input);
		$salt = bin2hex(strval($salt));
		$len = strlen($salt);
		for($result = '', $i = strlen($input)-2; $i>=0; $i-=2)
		{
			$result .= str_pad(
				dechex((
					hexdec(substr($input, $i, 2))+
					hexdec(substr($salt, ($i+14)%$len, 2))
				)%256),
				2,
				'0',
				STR_PAD_LEFT
			);
		}
		return sha1(gzencode(hex2bin($result), 9), $raw !== false);
	}
	public function __toString()
	{
		return $this->string;
	}
}

?>