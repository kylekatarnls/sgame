<?

Git

	+ __get $command
		$dir = getcwd()
		chdir(__DIR . '/../..')
		$return = shell_exec('git ' . $command) // no-debug
		chdir($dir)
		< $return