<?

use Illuminate\Database\Eloquent\ModelNotFoundException

ChatController:BaseController

	* posted $canal
		$created = false
		if Input::has('message')
			$created = Message::create(array(
				'content' => Input::get('message'),
				'canal_id' => $canal->id,
				'user_id' => User::current()->id,
				'type' => 1
			))
		< $created

	* getCanal $canalTitle
		try
			$canal = Canal::where('title', $canalTitle)->firstOrFail()
		catch ModelNotFoundException $e
			$canal = Canal::create(array(
				'title' => $canalTitle,
				'type' => 1
			))
		< $canal

	+ html $canalTitle
		$canal = >getCanal($canalTitle)
		<>view('chat', array(
			'canal' => $canalTitle,
			'posted' => >posted($canal),
			'messages' => $canal->newMessages()
		))

	+ json $canalTitle
		$canal = >getCanal($canalTitle)
		$posted = >posted($canal)
		< array(
			'lastTime' => Cache::get('message-canal-' . $canal->id),
			'_token' => Session::token(),
			'posted' => $posted,
			'messages' => $canal->{$posted ? 'newMessages' : 'waitForNewMessages' }()
		)
