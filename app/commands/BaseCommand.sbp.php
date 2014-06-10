<?

use Illuminate\Console\Command
use Symfony\Component\Console\Input\ArgvInput
use Symfony\Component\Console\Output\ConsoleOutput
use Hologame\Dir

a BaseCommand:Command

	CODE_FILE_EXTENSIONS = 'php jade html blade js coffee'
	EXCLUDE = ''
	FUNCTION_REGEX = '#(?<![a-zA-Z0-9_\x7f-\xff]|::|->)function[\t ]*(\(((?>[^\(\)]+)|(?-2))*\))#'
	STRING_REGEX = '#([\'"]).*(?<!\\\\)(?:\\\\\\\\)*\\1#U'
	TAB_COLUMNS = 4
	CONSOLE_HR = "\n--------------------\n"

	s* $foregroundColors = {
		black = '0;30'
		dark_gray = '1;30'
		blue = '0;34'
		light_blue = '1;34'
		green = '0;32'
		light_green = '1;32'
		cyan = '0;36'
		light_cyan = '1;36'
		red = '0;31'
		light_red = '1;31'
		purple = '0;35'
		light_purple = '1;35'
		brown = '0;33'
		yellow = '1;33'
		light_gray = '0;37'
		white = '1;37'
	}

	s* $backgroundColors = {
		black = '40'
		red = '41'
		green = '42'
		yellow = '43'
		blue = '44'
		magenta = '45'
		cyan = '46'
		light_gray = '47'
	}

	/**
	 * Method that can be used instead of defaut ouput method of Command class.
	 *
	 * @var callable
	 */
	- $outputMethod = null

	/**
	 * Replace the default output method.
	 *
	 * @return string
	 */
	+ setOutputMethod $outputMethod
		if ! is_callable($outputMethod)
			throw new \InvalidArgumentException("The output method must be callable.", 1);
		>outputMethod = $outputMethod

	/**
	 * Get command name (typed after "php artisan")
	 *
	 * @return string
	 */
	+ getName
		<>name

	/**
	 * Get command result as string.
	 *
	 * @return string
	 */
	s+ getResult $options = ''
		$command = new static
		$pwd = getcwd()
		chdir(__DIR . '/../..')
		$output = shell_exec('php artisan ' . rtrim($command->getName() . ' ' . $options)) // no-debug
		chdir($pwd)
		< $output

	/**
	 * Returns colored string.
	 *
	 * @param string $string text to color in the console
	 * @param string $foregroundColor color of the text
	 * @param string $backgroundColor color of the background
	 *
	 * @return array
	 */
	s* getColoredString $string, $foregroundColor = null, $backgroundColor = null
		if substr(__FILE, 0, 1) not '/'
			< $string
		$coloredString = ""

		// Check if given foreground color found
		if isset(static::$foregroundColors[$foregroundColor])
			$coloredString .= "\033[" . static::$foregroundColors[$foregroundColor] . "m"

		// Check if given background color found
		if isset(static::$backgroundColors[$backgroundColor])
			$coloredString .= "\033[" . static::$backgroundColors[$backgroundColor] . "m"

		// Add string and end coloring
		$coloredString .=  $string . "\033[0m"

		< $coloredString

	/**
	 * Returns all foreground color names.
	 *
	 * @return array
	 */
	s* getForegroundColors
		< array_keys(static::$backgroundColors)

	/**
	 * Returns all background color names.
	 *
	 * @return array
	 */
	s* getBackgroundColors
		< array_keys(static::$backgroundColors)

	/**
	 * Detect message type and execute info or error method with somme color (on a linux console).
	 *
	 * @param string $msg message to display in the console
	 *
	 * @return void
	 */
	* msg $msg, $raw = false
		if ! is_null(>outputMethod)
			<>call_user_func(>outputMethod, $msg, $raw)
		if $raw
			<>output->writeln($msg)
		$types = {
			warning = 'yellow'
			error = 'red'
			notice = 'magenta'
			success = 'green'
			help = 'cyan'
		}
		foreach $types as $type => $color
			if '[' . $type . ']' in strtolower($msg)
				$method = ($type in array('warning', 'error') ? 'error' : 'info')
				<>$method(static::getColoredString($msg, $color))
		<>info($msg)

	/**
	 * Convert an array into a PHP return syntax.
	 *
	 * @param array $texts texts to be converted
	 *
	 * @return string
	 */
	* langFile $texts
		< "<?php\nreturn " . preg_replace("#(?<=\n|\t)  #", "\t", var_export(array_undot($texts), true)) . ";\n?>"

	/**
	 * Save texts (array) with a given language and a given fileName in the app/lang directory.
	 *
	 * @param string $language language of the texts
	 * @param string $file file name (text group)
	 * @param array $texts texts to be saved
	 *
	 * @return boolean
	 */
	* putLangFile $language, $file, $texts
		< file_put_contents(app_path() . '/lang/' . $language . '/' . $file . '.php', >langFile($texts))

	/**
	 * Eqivalent of Javascript String.indexOf.
	 *
	 * @param string $haystack where to search
	 * @param string $needle what to search
	 *
	 * @return int
	 */
	* indexOf $haystack, $needle
		$pos = strpos($haystack, $needle)
		< $pos === false ? -1 : $pos

	/**
	 * Eqivalent of Javascript String.lastIndexOf.
	 *
	 * @param string $haystack where to search
	 * @param string $needle what to search
	 *
	 * @return int
	 */
	* lastIndexOf $haystack, $needle
		$pos = strrpos($haystack, $needle)
		< $pos === false ? -1 : $pos

	/**
	 * Scan the app directory and execute a function to each file.
	 *
	 * @param callable $function function to be executed with each file passed in it
	 * @param string $exclude list of directories and files to exclude (in a string separated by spaces)
	 * @param string $fileExtensions list of file extensions to be scanned (in a string separated by spaces)
	 *
	 * @return array
	 */
	s+ staticScanApp $function, $exclude = null, $fileExtensions = null, $directory = null, $filesOnly = true, $separator = '/', $dirRootList = null

		if is_null($directory)
			$directory = app_path()

		$exclude = is_null($exclude) ? :EXCLUDE : $exclude
		$fileExtensions = is_null($fileExtensions) ? :CODE_FILE_EXTENSIONS : $fileExtensions

		< Dir::each(f° $file use $function, $exclude, $fileExtensions

			if empty($exclude) || ! preg_match('#^' . implode('|', array_map('preg_quote', explode(' ', $exclude))) . '#', $file)
				$extension = ''
				$pos = strrpos($file, '.')
				if $pos not false
					$extension = substr($file, $pos + 1)
				$extensions = explode(' ', $fileExtensions)
				if $extension in $extensions
					call_user_func($function, $file)

		, $directory, $filesOnly, $separator, $dirRootList)

	// Non-static version
	* scanApp $function, $exclude = null, $fileExtensions = null, $directory = null, $filesOnly = true, $separator = '/', $dirRootList = null
		< static::staticScanApp($function, is_null($exclude) ? :EXCLUDE : $exclude, is_null($fileExtensions) ? :CODE_FILE_EXTENSIONS : $fileExtensions, $directory, $filesOnly, $separator, $dirRootList)

	/**
	 * Return elements and offsets that match to regex given or regex list given.
	 *
	 * @param string|array $regexList regex to be detected
	 *
	 * @return array
	 */
	* capture $regexList, $contents
		$matches = array()
		if ! is_traversable($regexList)
			$regexList = array($regexList)
		foreach $regexList as $regex
			preg_match_all($regex, $contents, $moreMatches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)
			$matches **= array_merge($moreMatches)
		< $matches

	/**
	 * Return true if the offset is in a quoted string within a content, false else.
	 *
	 * @param string $functionName name of the function to be detected
	 *
	 * @return boolean
	 */
	* isInQuotes $content, $offset, $extension = null
		if ! is_null($extension)
			list(, $extension) = end_separator('.', str_replace('.blade.php', '.blade', $extension))
		$extension = empty($extension) ? "" : strval($extension)
		$subContent = preg_replace(:STRING_REGEX, '', substr($content, 0, $offset))
		$nextChar = substr($content, $offset, 1)
		$subContent = preg_replace('#\?>.*<\?#U', ' ', $subContent)
		$subContent = preg_replace('#(/\*[\s\S]*\*/|' . ($extension is 'coffee' ? '\\#' : '//') . '.*(?=\n|\r' . ($nextChar in array("\n", "\r") ? '|$' : '') . '))#U', ' ', $subContent)
		$pos = max(>lastIndexOf($subContent, "'"), >lastIndexOf($subContent, '"'))
		return (
			$pos > -1 &&
			$pos > max(
				$extension in array('twig', 'blade') ? >lastIndexOf($subContent, '{{') : -1,
				>lastIndexOf($subContent, '<?')
			)
		)

	/**
	 * Return true if the offset is in a comment within a content, false else.
	 *
	 * @param string $functionName name of the function to be detected
	 *
	 * @return boolean
	 */
	* isInComment $content, $offset, $extension = null
		if ! is_null($extension)
			list(, $extension) = end_separator('.', str_replace('.blade.php', '.blade', $extension))
		$extension = empty($extension) ? "" : strval($extension)
		$subContent = preg_replace(:STRING_REGEX, '', substr($content, 0, $offset))
		$nextChar = substr($content, $offset, 1)
		$subContent = preg_replace('#\?>.*<\?#U', ' ', $subContent)
		$extension :=
			'coffee' ::
				$pattern = '#(/\*[\s\S]*\*/|\#.*(?=\n|\r' . ($nextChar in array("\n", "\r") ? '|$' : '') . '))#U'
				:;
			'css' ::
				$pattern = '#(/\*[\s\S]*\*/)#U'
				:;
			'jade' ::
				$pattern = '#(?<=^|\n|\r)(\t*)//-(.*)((?:\r\n|\n|\r)\\1.*)+#'
				:;
			'js' ::
				$pattern = '#(/\*[\s\S]*\*/|//.*(?=\n|\r' . ($nextChar in array("\n", "\r") ? '|$' : '') . '))#U'
				:;
			d:
				$pattern = '#(/\*[\s\S]*\*/|(?://|(?<=\r|\n)[\t ]*\#).*(?=\n|\r' . ($nextChar in array("\n", "\r") ? '|$' : '') . '))#U'
				:;
			
		$subContent = preg_replace($pattern, ' ', $subContent)
		$newLine = max(>lastIndexOf($subContent, "\n"), >lastIndexOf($subContent, "\r"))
		return (
			(
				! $extension in array('coffee', 'css', 'jade', 'js') &&
				substr($subContent, $newLine + 1, 1) is '#'
			) ||
			>lastIndexOf($subContent, $extension is 'coffee' ? '#' : '//') > $newLine ||
			"/*" in $subContent ||
			(
				$extension is 'coffee' &&
				'//-' in $subContent
			)
		)

	/**
	 * Return a regex wich permit to detect a function.
	 *
	 * @param string $functionName name of the function to be detected (by default, only anonymous function will be detected)
	 *
	 * @return string
	 */
	* functionRegex $functionName = null
		$functionRegex = :FUNCTION_REGEX
		if ! is_null($functionName)
			$functionRegex = str_replace('function', $functionName, $functionRegex)
		< $functionRegex

	/**
	 * Convert offset to a position in a file content (line and column).
	 *
	 * @param string $fileContent content of the original file
	 * @param int $ofsset position to convert
	 *
	 * @return string
	 */
	* offsetToPosition $fileContent, $offset
		$fileContent = str_replace(array("\r\n", "\r", "\t"), array("\n", "\n", str_repeat(" ", :TAB_COLUMNS)), substr($fileContent, 0, $offset))
		< 'line ' . (substr_count($fileContent, "\n") + 1) . ', column ' . max(1, strlen($fileContent) - 2 - strrpos($fileContent, "\n"))