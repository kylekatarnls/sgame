<?php

class CrawledContentSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$file = explode("\n", file_get_contents(substr(__FILE__, 0, -4).'.csv'));
		$headers = array_map('trim', explode("\t", trim($file[0])));
		unset($file[0]);
		foreach ($file as $line)
		{
			$line = array_map('trim', explode("\t", trim($line)));
			if(count($headers) === count($line))
			{
				CrawledContent::create(array_combine($headers, $line));
			}
		}
	}

}

?>