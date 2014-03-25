<?

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\MassAssignmentException;

UserController:BaseController

	+ login
		<>view('login');

	+ tryLogin
		Session::regenerateToken();
		$auth = Auth::attempt(
			Input::only(array('email', 'password')),
			Input::get('remember-me') === 'on'
		);
		if $auth
			flashAlert('user.login.logged', 'success');
			$uri = Input::get('back-url', '/');
		else
			$comeFromLoginPage = (preg_replace('#^[a-z]+://[^/]+#', '', Request::server('HTTP_REFERER')) === '/user/login');
			flashAlert('error.login.user-or-pass');
			$uri = $comeFromLoginPage ? '/user/login' : Input::get('back-url', '/user/login');
		< Redirect::to($uri);

	+ logout
		Auth::logout();
		< Redirect::to('/');

	+ signin
		<>view('signin');

	+ trySignin
		Session::regenerateToken();
		$email = Input::get('email');
		$password = Input::get('password');
		if filter_var($email, FILTER_VALIDATE_EMAIL) !== false
			if strlen($password) > 3
				if $password === Input::get('password-confirm')
					try
						$created = User::create(array(
							'email' => $email,
							'password' => $password,
							'flags' => (
								(User::DEFAULT_CONTRIBUTOR ? User::CONTRIBUTOR : 0) |
								(User::DEFAULT_MODERATOR ? User::MODERATOR : 0) |
								(User::DEFAULT_ADMIN ? User::ADMIN : 0)
							),
						));
					catch MassAssignmentException $e
						$created = false;
					catch QueryException $e
						$created = false;
					if $created
						flashAlert('user.signin.created', 'success');
						Auth::attempt(
							Input::only(array('email', 'password')),
							Input::get('remember-me') === 'on'
						);
						$backUrl = Input::get('back-url', '/');
						< Redirect::to($backUrl is '/user/signin' ? '/' : $backUrl);
					else
						flashAlert('error.signin.not-created');
				else
					flashAlert('error.signin.wrong-password-confirm');
			else
				flashAlert('error.signin.wrong-password');
		else
			flashAlert('error.signin.wrong-email');
		<>view('signin');

	+ listAll
		if ! User::current()->isAdministrator()
			Session::flash('back-url', '/user/list');
			< Redirect::to('/user/login');
		<>view('user-list', array(
			'users' => User::all()
		));