<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

TestCommand:BaseCommand

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'test'

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'After All-in-one Update or Install.'

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct()

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	+ fire

		>msg(inRoot(f°
			< shell_exec('php bin/phpunit.phar') // no-debug
		) . "\n", true)