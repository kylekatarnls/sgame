<?php

class TranslatableDateTimeTest extends TestCase {

	/**
	 * Vérifie que la classe TranslatableDateTime->recentDate fonctionne
	 *
	 * @return void
	 */
	public function testRecentDate()
	{

		Language::setLocale('en');
		$this->assertTrue((new TranslatableDateTime)->recentDate === 'today', "recentDate doit retourner 'today' en anglais");
		Language::setLocale('fr');
		$this->assertTrue((new TranslatableDateTime)->recentDate === "aujourd'hui", "recentDate doit retourner 'aujourd'hui' en français");

	}

	/**
	 * Vérifie que la classe TranslatableDateTime->recentTime fonctionne
	 *
	 * @return void
	 */
	public function testRecentTime()
	{

		Language::setLocale('en');
		$this->assertTrue(str_is('*ago', (new TranslatableDateTime)->recentTime), "recentTime doit se terminer par 'ago' en anglais");
		Language::setLocale('fr');
		$this->assertTrue(str_is('il y a*', (new TranslatableDateTime)->recentTime), "recentTime doit commencer par 'il y a' en français");

	}

}