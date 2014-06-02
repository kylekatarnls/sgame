<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

PreUpdateCommand:BaseCommand

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'pre:update'

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Before All-in-one Update or Install.'

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

		>msg(shell_exec('git pull') . "\n", true); // no-debug