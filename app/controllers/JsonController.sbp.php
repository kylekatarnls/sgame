<?

JsonController:BaseController

	+ getOutput
		if method_exists($this, 'run')
			$data = >run()
			>data = array_merge(>data, (array) $data)
		$js = prop('cJavascript', 'out')
		if ! empty($js)
			if empty(>data['js'])
				>data['js'] = ''
			>data['js'] .= $js
		<>data