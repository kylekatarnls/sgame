<?php

use Illuminate\Console\Command
use Symfony\Component\Console\Input\InputOption
use Symfony\Component\Console\Input\InputArgument
use Hologame\Dir

TextsCommand:Command

	EXTENSIONS = 'php jade html blade'
	EXCLUDE = '/utils/hologame/ /storage/ /lang/ /commands/TextsCommand.sbp.php /tests/FunctionsTest.sbp.php'
	FUNCTION_REGEX = '#§[\t ]*(\(((?>[^\(\)]+)|(?-2))*\))#'
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
	 * Convert offset to a position in a file content (line and column).
	 *
	 * @return void
	 */
	*offsetToPosition $fileContent, $offset
		$fileContent = str_replace(array("\r\n", "\r", "\t"), array("\n", "\n", str_repeat(" ", :TAB_COLUMNS)), substr($fileContent, 0, $offset))
		< 'line ' . (substr_count($fileContent, "\n") + 1) . ', column ' . max(1, strlen($fileContent) - 2 - strrpos($fileContent, "\n"))

	/**
	 * Fill >texts with translatable keys.
	 *
	 * @return void
	 */
	* getTexts $file, $content, $baseOffset = 0
		preg_match_all(:FUNCTION_REGEX, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)
		if ! empty($matches)
			if ! isset(>texts[$file])
				>texts[$file] = array()
			foreach $matches as $match
				$match[1][1] += $baseOffset
				>texts[$file][] = $match[1]
				>getTexts($file, $match[1][0], $match[1][1])

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	+ fire
		Language::setLocale(Config::get('app.dev-locale'))
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

		$messages = array()
		foreach >texts as $file => $matches
			$fileContent = file_get_contents(app_path() . $file)
			$copyContent = $fileContent
			$logMessages = array()
			foreach array_reverse($matches) as $match
				list($content, $offset) = $match
				if preg_match(:OPTION_REGEX, $content, $m)
					$options = array_map('trim', explode(',', $m[1]));
					foreach $options as $option
						list($key, $value) = array_pad(array_map('trim', explode('=', $option)), 2, 'true')
						if >option('verbose')
							$logMessages[] = "    Key with option at " . >offsetToPosition($fileContent, $offset) . " : " . $key . " = " . $value . "\n"
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
		if empty($messages)
			>info("\n\nAll files are up to date.\n")
		else
			foreach $messages as $file => $list
				>info("\n\n" . $file . "\n--------------------\n" . implode("", $list))

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
