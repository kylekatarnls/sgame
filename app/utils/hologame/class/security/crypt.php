<?php

namespace Hologame;

class Security°Crypt
{
	protected $cryptKey;
	public function __construct($key = '', $length = 32)
	{
		$this->cCall($key, $length);
	}
	public function cCall($key = '', $length = 32)
	{
		$cryptKey = Storage°File::getContent('security/script/key_'.$key.'.bin');
		if($cryptKey === '')
		{
			for($i = $length; $i; $i--)
			{
				$cryptKey .= chr(mt_rand(0, 255));
			}
			Storage°File::putContent('security/script/key_'.$key.'.bin', $cryptKey);
		}
		$this->cryptKey = $cryptKey;
		return $this;
	}
	public function crypt($string, $raw = false)
	{
		return self::stCrypt($string, $this->cryptKey, $raw);
	}
	static public function stCrypt($string, $key, $raw = false)
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv);
		return ($raw ? $iv.$ciphertext : base64_encode($iv.$ciphertext));
	}
	public function decrypt($string, $raw = false)
	{
		return self::stDecrypt($string, $this->cryptKey, $raw);
	}
	static public function stDecrypt($string, $key, $raw = false)
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		if(!$raw)
		{
			$string = base64_decode($string);
		}
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, substr($string, $iv_size), MCRYPT_MODE_CBC, substr($string, 0, $iv_size)), "\0");
	}
}

?>