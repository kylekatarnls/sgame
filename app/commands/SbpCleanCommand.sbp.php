<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

SbpCleanCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'sbp:clean';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Clean the SBP cache directory.';

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
		$count = 0;
		$success = 0;
		$directory = app_path().'/storage/sbp/';
		foreach scandir($directory) as $file
			if substr($file, -4) === '.php'
				$count++;
				if unlink($directory . $file)
					$success++;
		echo $success . ' / ' . $count . "\n";