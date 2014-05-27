<?php

use Symfony\Component\Console\Input\InputOption
use Symfony\Component\Console\Input\InputArgument
use Hologame\Dir

TextsCommand:BaseCommand

	EXTENSIONS = 'php jade html blade'
	EXCLUDE = '/utils/hologame/ /storage/ /lang/ /commands/TextsCommand.sbp.php /tests/FunctionsTest.sbp.php'
	OPTION_REGEX = '#/\*@(.*)@\*/#'
	COMMENT_PATTERN = '/\*§(.*)§\*/'
	KEY_PATTERN = '([\'"])([a-zA-Z0-9._-]+)\\1'
	TAB_COLUMNS = 4

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	* $name = 'texts'

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	* $description = 'Get or replace texts to translate.'

	* $texts = array()

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	+ __construct
		parent::__construct()

	/**
	 * Fill >texts with translatable keys.
	 *
	 * @param string $file file path wich be searched in
	 * @param string $content content or content's portion of the file wich be searched in
	 * @param int $baseOffset if the content is a portion of the entire content, this provide the start offset
	 *
	 * @return void
	 */
	* getTexts $file, $content, $baseOffset = 0
		preg_match_all(>functionRegex('(?:§|s)'), $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)
		if ! empty($matches)
			if ! isset(>texts[$file])
				>texts[$file] = array()
			foreach $matches as $match
				$match[1][1] += $baseOffset
				>texts[$file][] = array(($match[0][0][0] === 's' ? 's' : '') . $match[1][0], $match[1][1])
				>getTexts($file, $match[1][0], $match[1][1])

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	+ fire
		$devLocale = Config::get('app.dev-locale')
		Language::setLocale($devLocale)

		Dir::each(f° $file
			if ! preg_match('#^' . implode('|', array_map('preg_quote', explode(' ', :EXCLUDE))) . '#', $file)
				$extension = ''
				$pos = strrpos($file, '.')
				if $pos not false
					$extension = substr($file, $pos + 1)
				$extensions = explode(' ', :EXTENSIONS)
				if $extension in $extensions
					>getTexts($file, file_get_contents(app_path() . $file))
		, app_path(), true, '/')

		$devLangDirectory = app_path() . '/lang/' . $devLocale
		$texts = array()
		Dir::each(f° $file use $devLangDirectory, &$texts
			$file = ltrim($file, '/');
			$group = strtr(substr($file, 0, -4), '/', '.')
			$texts[$group] = include_once($devLangDirectory . '/' . $file)
		, $devLangDirectory, true, '/')
		$texts = array_dot($texts)

		$filesToUpdate = array()
		$messages = array()
		foreach >texts as $file => $matches
			$fileContent = file_get_contents(app_path() . $file)
			$copyContent = $fileContent
			$logMessages = array()
			foreach array_reverse($matches) as $match
				list($content, $offset) = $match
				if >isInQuotes($fileContent, $offset)
					if >option('verbose')
						$logMessages[] = "    [NOTICE] A function has been detected in a quoted string at " . >offsetToPosition($fileContent, $offset) . ": " . $content . "\n"
				elseif $content[0] is 's'
					if preg_match(substr(:STRING_REGEX, 0, strrpos(:STRING_REGEX, '#')) . '\s*(?=,|\))#U', $content, $m)
						>msg("\nA dev text has been found:\n" . $content)
						$originalText = stripcslashes(addcslashes(substr($m[0], 1, -1), $m[1] is '"' ? "'" : '"'))
						$name = array_search($originalText, $texts)
						$ok = true
						if $name && ! >confirm("    The '" . $name . "' key match this text. Would you keep it? [Y/N]")
							$name = false
						while ! $ok || empty($name)
							$ok = true
							$name = >ask("    Please give a key to this text: ")
							if empty($name)
								>msg("    [ERROR] The key cannot be empty.\n")
							if isset($texts[$name]) && $texts[$name] not $originalText
								$ok = >confirm("    [NOTICE] This key is already token by another text:\n        \"" . $texts[$name] . "\"\n    This text will be replaced by:\n        \"" . $originalText . "\"\n    Do you confirm this replacement? [Y/N]")
						if ! isset($texts[$name]) || $texts[$name] not $originalText
							$texts[$name] = $originalText
							$languageFile = array_get(explode('.', $name), 0)
							if ! $languageFile in $filesToUpdate
								$filesToUpdate[] = $languageFile
						$fileContent = substr($fileContent, 0, $offset - 1) . '§(' . var_export($name, true) . '/*§' . $originalText . '§*/' . substr($fileContent, $offset + 1 + strlen($m[0]))
						$logMessages[] = "    Dev text at " . >offsetToPosition($fileContent, $offset) . ": " . $content . "\n"
						$logMessages[] = "    You give to it the key: " . $name . "\n"
					else
						$logMessages[] = "    [WARNING] A dev text has been detected but any entire string has been found in it.\n" .
							"    -> " . >offsetToPosition($fileContent, $offset) . ": " . $content . "\n"
				else
					if preg_match(:OPTION_REGEX, $content, $m)
						$options = array_map('trim', explode(',', $m[1]));
						foreach $options as $option
							list($key, $value) = array_pad(array_map('trim', explode('=', $option)), 2, 'true')
							if >option('verbose')
								$logMessages[] = "    Key with option at " . >offsetToPosition($fileContent, $offset) . ": " . $key . " = " . $value . "\n"
							$key :=
								'dynamic' ::
									strtolower(**$value)
									if ! $value in array('no', 'false', 'off' ,'0')
										if >option('verbose')
											$logMessages[] = "    Skipped because dynamic\n"
										continue 3
									:;
					if preg_match('#' . :KEY_PATTERN . '\s*(' . :COMMENT_PATTERN . ')\s*(?=,|\))#U', $content, $m, PREG_OFFSET_CAPTURE) or preg_match('#' . :KEY_PATTERN . '\s*(?=,|\))#U', $content, $m, PREG_OFFSET_CAPTURE)
						$key = $m[2][0]
						$endKeyOffset = $m[1][1] + 2 * strlen($m[1][0]) + strlen($key)
						$text = §($key)
						if $text not $key
							if isset($m[4])
								$newContent = preg_replace('#' . :COMMENT_PATTERN . '#U', '/*§' . $text . '§*/', $content, 1)
								if $newContent not $content
									$logMessages[] = "    Update key : " . $key . " at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
									$fileContent = substr($fileContent, 0, $offset) . $newContent . substr($fileContent, $offset)
							else
								$logMessages[] = "    New key : " . $key . " at " . >offsetToPosition($fileContent, $offset) . " : " . $text . "\n"
								$after = $offset + $endKeyOffset
								$fileContent = substr($fileContent, 0, $after) . '/*§' . $text . '§*/' . substr($fileContent, $after)
						else
							$logMessages[] = "    [WARNING] No text for key " . $key . " at " . >offsetToPosition($fileContent, $offset) . " : " . $content . "\n"
					else
						$logMessages[] = "    [ERROR] No key at " . >offsetToPosition($fileContent, $offset) . " : " . $content . "\n"
			if >option('clean')
				if ! >option('verbose')
					$logMessages = array()
				$fileContent = preg_replace('#' . :COMMENT_PATTERN . '#U', '', $copyContent)
				if $fileContent not $copyContent
					$logMessages[] = ">> Cleanup"
			if $fileContent not $copyContent
				file_put_contents(app_path() . $file, $fileContent)
			if ! empty($logMessages)
				$messages[$file] = $logMessages
		if empty($messages) && empty($filesToUpdate)
			>msg("\n\nAll files are up to date.\n")
		else
			$texts = array_undot($texts)
			if ! empty($filesToUpdate)
				>msg("\n\nModifications in languages files:\n--------------------\n")
				foreach $filesToUpdate as $file
					>msg(>putLangFile($devLocale, $file, $texts[$file]) ?
						"    [SUCCESS] /lang/" . $devLocale . "/" . $file . ".php have been updated" :
						"    [ERROR] /lang/" . $devLocale . "/" . $file . ".php have not been updated"
					)
			foreach $messages as $file => $list
				>msg("\n\n" . $file . "\n--------------------\n")
				array_map(array($this, 'msg'), $list)

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	* getArguments
		< array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		)

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	* getOptions
		< array(
			array('clean', 'c', InputOption::VALUE_NONE, 'Clean comments.', null),
			//array('verbose', 'v', InputOption::VALUE_NONE, 'Verbose mode.', null),
		)
