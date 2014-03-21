<?

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

NewAdminCommand:Command

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'admin:new';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Create an admin';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct();


	+ getOptions
		< array(
			array('email', 'e', InputOption::VALUE_REQUIRED, 'User e-mail', null),
			array('password', 'p', InputOption::VALUE_REQUIRED, 'Password', null),
			array('contributor', 'c', InputOption::VALUE_NONE, 'Is the user a contributor', null),
			array('moderator', 'm', InputOption::VALUE_NONE, 'Is the user a moderator', null),
			array('administrator', 'a', InputOption::VALUE_NONE, 'Is the user an administrator', null),
		);


	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	+ fire
		try
			< User::create(array(
				'email' => >option('email'),
				'password' => >option('password'),
				'flags' => (
					(>option('contributor') ? User::CONTRIBUTOR : 0) |
					(>option('moderator') ? User::MODERATOR : 0) |
					(>option('administrator') ? User::ADMIN : 0)
				),
			));
		catch Exception $e
			echo $e->getFile() . ':' . $e->getLine() . "\n" . $e->getMessage() . "\n\n" . $e->getTraceAsString();