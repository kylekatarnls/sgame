<?

JsonController:BaseController

	+ getOutput
		if method_exists($this, 'run')
			$data = >run()
			>data = array_merge(>data, (array) $data)
		<>data