<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

PostUpdateCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'post:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'After All-in-one Update or Install.';

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
		echo shell_exec('php bin/composer.phar self-update') . "\n"; //no-debug
		chdir($pwd);