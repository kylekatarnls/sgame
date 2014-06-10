<?php

use Symfony\Component\Console\Input\InputOption
use Symfony\Component\Console\Input\InputArgument

CheckCommand:BaseCommand

	EXCLUDE = '/storage/ /lang/ /utils/hologame/storage/ /utils/exception/resources/pretty-template.php /utils/hologame/class/page/admin/translation/index.php /utils/hologame/twig /database/migrations/'
	FORBIDDEN_FUNCTIONS = 'debug dump var_dump echo exit print print_r shell_exec exec passthru system eval' // no-debug

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'check'

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = "Check some application's rules."

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct()

	+ fire
		try
			$appDirectory = app_path()
			$rootDirectory = realpath($appDirectory . '/..')
			$publicDirectory = realpath($rootDirectory . '/public')
			if >option('mute')
				if >option('verbose')
					>msg("[ERROR] verbose (v) and mute (m) are contradictory.")
					return
			else
				$mustBeWritable = array(
					$publicDirectory . '/css',
					$publicDirectory . '/js',
					$publicDirectory . '/img',
					$appDirectory . '/storage',
					$appDirectory . '/utils/hologame/storage',
				)
				foreach $mustBeWritable as $directory
					if ! is_writable($directory)
						>msg("[NOTICE] " . realpath($directory) . " must be writable.")
			$forbiddenFunctions = preg_split('#\s+#', :FORBIDDEN_FUNCTIONS)
			$forbiddenFunctionsPattern = '(' . implode('|', array_map('preg_quote', $forbiddenFunctions)) . ')'
			$ok = true
			>scanApp(f° $file use $rootDirectory, $forbiddenFunctionsPattern, &$ok

				$fileContent = file_get_contents($rootDirectory . $file)
				if substr($file, -3) is '.js' ||  substr($file, -7) is '.coffee'
					$patterns = array(
						'#(?<![a-zA-Z0-9_\x7f-\xff]|::|->)(debug|console\.(?:log|warn|info|debug|error|trace))(?![a-zA-Z0-9_\x7f-\xff])#',
					)
				else
					$patterns = array(
						>functionRegex('(?<!function\s)' . $forbiddenFunctionsPattern),
						'#(?<![a-zA-Z0-9_\x7f-\xff]|::|->)(echo|exit|print|(?<=[\*/])\s*(?<!no-)debug)(?![a-zA-Z0-9_\x7f-\xff])#', // no-debug
					)
				$patterns[] = '#(//|\#|/\*)\s*debug(?![a-zA-Z0-9_\x7f-\xff])#'
				if substr($file, -4) is '.php'
					$patterns[] = '#`[^`]+`#'
				$matches = >capture($patterns, $fileContent)
				if ! empty($matches)
					$logMessages = array()
					foreach $matches as $match
						list($text, $offset) = $match[0]
						$newLine = strpos($fileContent, "\n", $offset)
						if >isInQuotes($fileContent, $offset, $file)
							if >option('verbose')
								$logMessages[] = "    Debug function found in quotes at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
						elseif >isInComment($fileContent, $offset, $file)
							if >option('verbose')
								$logMessages[] = "    Debug function found in a comment at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
						elseif preg_match('#/[/\*]\s*no-debug#', $newLine is false ? substr($fileContent, $offset) : substr($fileContent, $offset, $newLine))
							if >option('verbose')
								$logMessages[] = "    No-debug function found at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
						else
							$ok = false
							$logMessages[] = "    [WARNING] Debug function found at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
					if ! >option('mute') && ! empty($logMessages)
						>msg("\n\n" . $file . :CONSOLE_HR)
						array_map(array($this, 'msg'), $logMessages)

			, null, null, array($appDirectory, $publicDirectory), true, '/', $rootDirectory)
			if >option('mute')
				>msg($ok ? 'OK' : 'KO', true)
			elseif $ok
				>msg("Everything is good.\n")
		catch Exception $e
			>msg("[ERROR] " . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getMessage() . "\n\n" . $e->getTraceAsString())

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	* getOptions
		< array(
			array('mute', 'm', InputOption::VALUE_NONE, 'Return juste OK or KO.', null),
			//array('verbose', 'v', InputOption::VALUE_NONE, 'Verbose mode.', null),
		)