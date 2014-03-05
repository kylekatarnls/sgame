<?

DatabaseSeeder:Seeder

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	+ run()
		Eloquent::unguard();
		>call('CrawledContentSeeder');