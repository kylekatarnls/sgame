<?

MemcachedTest:TestCase

	/**
	 * Vérifie que la classe Memcached fonctionne
	 *
	 * @return void
	 */
	+ testMemcached

		$mustEmulate = !class_exists('Memcache');
		>assertTrue($mustEmulate || class_exists('Memcached'), "Memcached devrait exister si Memcache existe");
		>assertTrue($mustEmulate || method_exists('Memcached', 'get'), "Memcached devrait avoir une méthode get() si Memcache existe");
		>assertTrue($mustEmulate || method_exists('Memcached', 'set'), "Memcached devrait avoir une méthode set() si Memcache existe");
