<?

BaseController:Controller

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	* setupLayout
		if !is_null(>layout)
			>layout = View::make(>layout);

	* view $view = 'home', $data = array()
		$jadeFile = app_path() . '/views/' . $view . '.jade';
		if(file_exists($jadeFile))
			<new Illuminate\Http\Response((new Jade)->render($jadeFile, View::withShared($data)));
		<View::make($view)->with($data);

	s+ response $view = 'home', $status = 200
		$response = new Symfony\Component\HttpFoundation\Response('', $status);
		$response->setContent((new Jade)->render(app_path() . '/views/' . $view . '.jade', View::getShared()));
		<$response;

	s+ notFound $view = 'errors/notFound', $status = 404
		<static::response($view, $status);
			
