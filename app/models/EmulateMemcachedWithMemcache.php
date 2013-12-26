<?php

if(!class_exists('Memcache') && class_exists('Memcached'))
{
	class Memcached extends Memcached
	{
		protected $resultCode = -1;

		public function get($key)
		{
			$flags = false;
			$value = parent::get($key, $flags);

			// if $flags has been touched, key was found
			// http://php.net/manual/fr/memcache.get.php#112056
			$resultCode = ($flags !== false ? 0 : -1);
			return $value;
		}

		public function getResultCode()
		{
			return $this->resultCode;
		}

		public function set($key, $value, $seconds)
		{
			parent::set(
				$this->prefix.$key,
				$value,
				strlen($value) > 512 ? MEMCACHE_COMPRESSED : 0,
				$seconds
			);
		}
	}
}

class EmulateMemcachedWithMemcache
{
	function proceed()
	{
		return 'memcached';
	}
}

?>