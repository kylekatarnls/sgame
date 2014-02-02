<?php

class MemcachedTest extends TestCase {

	/**
	 * Vérifie que la classe Memcached fonctionne
	 *
	 * @return void
	 */
	public function testMemcached()
	{
		$mustEmulate = !class_exists('Memcache');
		$this->assertTrue($mustEmulate || class_exists('Memcached'), "Memcached devrait exister si Memcache existe");
		$this->assertTrue($mustEmulate || method_exists('Memcached', 'get'), "Memcached devrait avoir une méthode get() si Memcache existe");
		$this->assertTrue($mustEmulate || method_exists('Memcached', 'set'), "Memcached devrait avoir une méthode set() si Memcache existe");

	}

}
