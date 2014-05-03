<?

Canal:Model

	* $table = 'canals'
	* $softDelete = true
	* $fillable = array('title', 'type', 'canal')

	+messages
		<>hasMany('message')

	+newMessages
		<>messages
			//->orderBy('created_at', 'desc')
			// ->take(20)

	+waitForNewMessages $time = 20, $intervalle = 0.5
		< Cache::waitFor($time, $intervalle, 'message-canal-' . >id) ?
			>newMessages() :
			null