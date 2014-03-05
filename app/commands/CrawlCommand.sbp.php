<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

CrawlCommand:Command

	/**
	 * Si FOLLOW_LINKS = true le crawler scannera les liens qu'il trouve
	 */
	FOLLOW_LINKS = true;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'crawl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Crawl all ressources and links contented in them.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct()
		parent::__construct();

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	+ fire()
		$urlCount = 0;
		foreach(CrawledContent::all() as $crawledContent)
			$urlCount++;
			if(scanUrl($crawledContent->url, :FOLLOW_LINKS) === Crawler::NOT_FOUND)
				echo "crawledContent deleted : " . $crawledContent->url . " Not found\n";
				$crawledContent->delete();
			echo Crawler::getLog();
		$urlCount += Crawler::countLinks();
		echo §('crawler.crawled-url', $urlCount)."\n";