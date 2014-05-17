<?php

use Illuminate\Console\Command
use Symfony\Component\Console\Input\InputOption
use Symfony\Component\Console\Input\InputArgument
use Hologame\Dir

TextsCommand:Command

	EXTENSIONS = 'php jade html blade'
	EXCLUDE = '/utils/hologame/ /storage/ /lang/ /commands/TextsCommand.sbp.php'
	FUNCTION_REGEX = '#§[\t ]*(\(((?>[^\(\)]+)|(?-2))*\))#'
	COMMENT_PATTERN = '/\*§(.*)§\*/'
	KEY_PATTERN = '([\'"])([a-zA-Z0-9._-]+)\\1'

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

	* getTexts $file, $content, $baseOffset = 0
		preg_match_all(:FUNCTION_REGEX, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)
		if ! empty($matches)
			if ! isset(>texts[$file])
				>texts[$file] = array()
			foreach $matches as $match
				$match[1][1] += $baseOffset
				>getTexts($file, $match[1][0], $match[1][1])
				>texts[$file][] = $match[1]

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
			$messages[$file] = array()
			foreach $matches as $match
				list($content, $offset) = $match
				if preg_match('#' . :KEY_PATTERN . '[\s\S]*(' . :COMMENT_PATTERN . ')?#U', $content, $m)
					$key = $m[2]
					$text = §($key)
					if $text not $key
						if isset($m[4])
							$newContent = preg_replace('#' . :COMMENT_PATTERN . '#U', '/*§' . $text . '§*/', $content, 1)
							if $newContent not $content
								$messages[$file][] = "    Update key : " . $key . " at " . $offset . " : " . $text . "\n"
								$fileContent = substr($fileContent, 0, $offset) . $newContent . substr($fileContent, $offset)
						else
							$messages[$file][] = "    New key : " . $key . " at " . $offset . " : " . $text . "\n"
							$after = $offset + strlen($content)
							$fileContent = substr($fileContent, 0, $after) . '/*§' . $text . '§*/' . substr($fileContent, $after)
					else
						$messages[$file][] = "    [WARNING] No text for key " . $key . " at " . $offset . " : " . $content . "\n"
				else
					$messages[$file][] = "    [ERROR] No key at " . $offset . " : " . $content . "\n"
			if $fileContent not $copyContent
				file_put_contents(app_path() . $file, preg_replace('#' . :COMMENT_PATTERN . '#U', '', $copyContent))
				//file_put_contents(app_path() . $file, $fileContent)
		foreach $messages as $file => $list
			echo "\n\n" . $file . "\n--------------------\n" . implode("", $list)

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
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		)
