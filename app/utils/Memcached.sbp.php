<?

if class_exists('Memcache')
	Memcached:Memcache

		* $resultCode = -1;

		+ get $key
			$flags = false;
			$value = parent::get($key, $flags);
			$value = is_array($key) ? array_map('unserialize', $value) : unserialize($value);

			// if $flags has been touched, key was found
			// http://php.net/manual/fr/memcache.get.php#112056
			$resultCode = ($flags !== false ? 0 : -1);
			<$value;

		+ getResultCode
			<>resultCode;

		+ set $key, $value, $seconds
			$value = is_array($key) ? array_map('serialize', $value) : serialize($value);
			parent::set(
				$key,
				$value,
				(is_array($value) ? array_sum(array_map('strlen', $value)) : strlen($value)) > 512 ? MEMCACHE_COMPRESSED : 0,
				$seconds
			);

?>