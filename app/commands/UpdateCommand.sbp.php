<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

UpdateCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'All-in-one Update.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct();

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	+ fire

		$pwd = getcwd();
		chdir(__DIR . '/../..');
		echo shell_exec('php bin/composer.phar update') . "\n"; //no-debug
		chdir($pwd);