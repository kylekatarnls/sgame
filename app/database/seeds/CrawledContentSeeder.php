<?php

class CrawledContentSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$file = fopen(substr(__FILE__, 0, -4).'.csv', 'r');
		$headers = fgetcsv($file, 1024, ';');
		while($line = fgetcsv($file, 1024, ';'))
		{
			CrawledContent::create(array_combine($headers, $line));
		}
	}

}

?>