<?

use Hologame\Html

Git

	MERGE_RETINA = true
	COMPARE_SUFFIXE = 'Compare'

	+ __get $command

		< call_user_func(array($this, $command))

	+ __call $command, array $args

		if ends_with($command, :COMPARE_SUFFIXE)
			$compareTo = array_pop($args)
			$command = substr($command, 0, -strlen(:COMPARE_SUFFIXE))
			$result = call_user_func_array(array($this, $command), $args)
			$result = trim(preg_replace('`#[^\r\n]*(\r\n|\n|\r|$)`', '', $result))
			< $result is $compareTo
		else
			< inRoot(f° use $command, $args

				< shell_exec('git ' . $command . rtrim(' ' . implode(' ', $args))) // no-debug
			)

	s+ checkable $output
		$only = urldecode(Input::get('only'))
		< preg_replace_callback('`(#\t)((?:([a-z]+):[ \t]+)?([^\s]+))(?=\s|$)`', f° $match use $only

			$file = $match[4]
			$input = new Html('input', {
				type = "checkbox"
				className = "git-add"
				name = "git-add[" . $file . "]"
			})
			if :MERGE_RETINA
				$file = str_replace('@2x', '', $file)
			if !$only || $only is $file
				$input->checked = "checked"
			< $match[1] . $input . $match[2]

		, htmlspecialchars($output))