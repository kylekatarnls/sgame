<?php

class CrawledContentSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		CrawledContent::create([
			'url' => 'http://test.com/page.php',
			'title' => 'Super site',
			'content' => 'Contenu html, strip tagé.'
		]);
	}

}

?>