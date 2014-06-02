<?

HtmlController:BaseController

	CONTROLLER_SUFFIXE = 'Controller'

	* $view = null

	+ setView $view
		$view = trim(str_replace('/.', '/', '/' . $view), '/')
		if ! preg_match('#[^a-zA-Z0-9._/-]#', $view)
			foreach ['includes', 'errors', 'emails', 'layouts'] as $forbiddenDirectory
				if strpos($view, $forbiddenDirectory . '/') is 0
					< false
			>view = $view
			< true
		< false

	+ getView $view = null, $data = array()
		View::share($data)
		if ! is_null($view)
			>setView($view)
		if is_null(>view)
			>view = strtr(trim(get_called_class(), '\\'), '\\', '/')
			$len = strlen(:CONTROLLER_SUFFIXE)
			if substr(>view, -$len) is :CONTROLLER_SUFFIXE
				>view = substr(>view, 0, -$len)
		if method_exists($this, 'run')
			$data = >run()
			>data = array_merge(>data, (array) $data)
		<>view(>view, >data)

	+ getOutput
		<>getView()