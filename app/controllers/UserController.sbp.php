<?

UserController:BaseController

	+ login
		<>view('login');

	+ tryLogin
		$auth = Auth::attempt(
			Input::only(array('email', 'password')),
			Input::get('remember-me') === 'on'
		);
		if $auth
			Session::flash('alert', 'user.login.logged');
			Session::flash('alert-type', 'success');
			$uri = Input::get('back-url', '/');
		else
			$comeFromLoginPage = (preg_replace('#^[a-z]+://[^/]+#', '', Request::server('HTTP_REFERER')) === '/user/login');
			Session::flash('alert', 'error.login.user-or-pass');
			Input::flash();
			$uri = $comeFromLoginPage ? '/user/login' : Input::get('back-url', '/user/login');
		< Redirect::to($uri);

	+ logout
		Auth::logout();
		< Redirect::to('/');