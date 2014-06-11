<?

use Hologame\Html

// Public
GitHub
	PROJECT = 'kylekatarnls/sgame'
	PROTOCOLE = 'https'
	DOMAIN = 'github.com'

// Private
GitLab
	PROJECT = 'KyleK/game-oriented-web-framework'
	PROTOCOLE = 'https'
	DOMAIN = 'gitlab.com'

Git:GitLab

	UPDATE_CONFIG = false // - /!\ true = Danger
	UPDATE_BRANCH = false
	MERGE_RETINA = true
	MERGE_ASSET = true
	COMPARE_SUFFIXE = 'Compare'

	s- $log = array()

	+ __construct

		if :UPDATE_BRANCH
			>branch("-u origin/master")
			array_pop(static::$log)
		>remote('set-url --track origin ' . :PROTOCOLE . '://' . :DOMAIN . '/' . :PROJECT . '.git')
		array_pop(static::$log)

	+ __get $command

		< call_user_func(array($this, $command))

	- hidePassword $text
		< preg_replace('#(?<=:)[^:@]+(?=@' . preg_quote(:DOMAIN) . ')#', 'xxxx', $text)

	- cmd $command, $args = array()

		if ! is_array($args)
			$args = array_slice(func_get_args(), 1)
		< inRoot(f° use &$command, &$args

			$command = 'git ' . $command . rtrim(' ' . implode(' ', $args))
			static::$log[] = static::hidePassword($command)
			< static::hidePassword(shell_exec($command)) // no-debug
		)

	+ __call $command, array $args

		if ends_with($command, :COMPARE_SUFFIXE)
			$compareTo = array_pop($args)
			$command = substr($command, 0, -strlen(:COMPARE_SUFFIXE))
			$result = call_user_func_array(array($this, $command), $args)
			$result = trim(preg_replace('`#[^\r\n]*(\r\n|\n|\r|$)`', '', $result))
			< $result is $compareTo
		else
			<>cmd($command, $args)

	s+ __callStatic $command, array $args
		$class = get_called_class()
		if method_exists($class, $command . 'Static')
			< call_user_func_array(array($class, $command . 'Static'), $args)
		else
			$git = new static
			< call_user_func_array(array($git, $command), $args)

	s+ pushStatic $username, $password, $project = null

		if is_null($project)
			$project = :PROJECT
		$git = new static
		< $git->push((:UPDATE_CONFIG ? "-u " : "") . "--repo " . :PROTOCOLE . "://" . $username . ":" . $password . "@" . :DOMAIN . "/" . $project . ".git")

	s+ addStatic $list = null
		if ! is_null($list) && empty($list)
			< ""
		if is_null($list)
			$list = array()
		$git = new static
		< $git->add(rtrim("--all " . implode(" ", $list)))

	s+ commitStatic $commitMessage
		$git = new static
		< $git->commit('-m "' . addcslashes($commitMessage, '\\"') . '"')

	s+ getCommands
		< static::$log

	s+ checkable $output
		$only = urldecode(Input::get('only'))
		< preg_replace_callback('`(#\t)((?:([a-z]+):[ \t]+)?([^\s]+))(?=\s|$)`', f° $match use $only

			$file = $match[4]
			$input = new Html('input', {
				type = "checkbox"
				name = "git-add[" . $file . "]"
				value = "1"
			})
			if :MERGE_RETINA
				$file = str_replace('@2x', '', $file)
			if :MERGE_ASSET
				$file = str_replace('app/assets/images/', 'public/img/', $file)
			if !$only || $only is $file
				$input->checked = "checked"
			< $match[1] . (new Html('label', {
				className = "git-add"
				content = "&nbsp; " . $input . $match[2] .  " &nbsp;"
			}))

		, htmlspecialchars($output))