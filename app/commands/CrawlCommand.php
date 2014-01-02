<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrawlCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crawl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Crawl all ressources and links contented in them.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$urlCount = 0;
		foreach(CrawledContent::all() as $crawledContent)
		{
			$urlCount++;
			scanUrl($crawledContent->url, true);
		}
		$urlCount += Crawler::countLinks();
		echo §('crawler.crawled-url', $urlCount)."\n";
	}

}
