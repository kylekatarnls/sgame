<?

Message:Model

	* $table = 'messages'
	* $softDelete = true
	* $fillable = array('content', 'user_id', 'canal_id', 'type')

	+ canal
		<>belongsTo('canal')

	+ user
		<>belongsTo('user')

	+ getAvatarAttribute
		$mail = >user->email
		< '<div style="background-color: #' . substr(md5($mail), 1, 6) . ';">' . strtoupper(substr($mail, 0, 1)) . '</div>'

	+ __toString
		<>content