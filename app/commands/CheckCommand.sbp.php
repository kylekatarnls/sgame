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
		if >option('verbose') && >option('mute')
			>msg("[ERROR] verbose (v) and mute (m) are contradictory.")
			exit // no-debug
		$forbiddenFunctions = preg_split('#\s+#', :FORBIDDEN_FUNCTIONS)
		$forbiddenFunctionsPattern = '(' . implode('|', $forbiddenFunctions) . ')'
		$ok = true
		>scanApp(f° $file use $forbiddenFunctionsPattern, &$ok

			$fileContent = file_get_contents(app_path() . $file)
			preg_match_all(>functionRegex('(?<!function\s)' . $forbiddenFunctionsPattern), $fileContent, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)
			preg_match_all('#(?<![a-zA-Z0-9_\x7f-\xff]|::|->)(echo|exit|print|(?<=[\*/])\s*debug)(?![a-zA-Z0-9_\x7f-\xff])#', $fileContent, $moreMatches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)  // no-debug
			$matches **= array_merge($moreMatches)
			if ! empty($matches)
				$logMessages = array()
				foreach $matches as $match
					list($text, $offset) = $match[0]
					$newLine = strpos($fileContent, "\n", $offset)
					if preg_match('#/[/\*]\s*no-debug#', $newLine is false ? substr($fileContent, $offset) : substr($fileContent, $offset, $newLine))
						if >option('verbose')
							$logMessages[] = "    No-debug function found at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
					else
						$ok = false
						$logMessages[] = "    [WARNING] Debug function found at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
				if ! >option('mute') && ! empty($logMessages)
					>msg("\n\n" . $file . :CONSOLE_HR)
					array_map(array($this, 'msg'), $logMessages)

		)
		if >option('mute')
			exit($ok ? 'OK' : 'KO') // no-debug
		if $ok
			>msg("Everything is good.\n")

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