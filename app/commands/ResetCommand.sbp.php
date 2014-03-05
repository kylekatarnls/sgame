<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

ResetCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'reset';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Delete all ressources.';

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
		CrawledContent::truncate();
		echo "Ressources vidées\n";