<?

Session:Illuminate\Support\Facades\Session

	use Wait

	s* write $callback = null
		session_write_close()
		if ! is_null($callback)
			$callback()
		session_start()

	s+ sleep $time
		static::write(function use $time
			sleep($time)
		)

	s+ usleep $time
		static::write(function use $time
			usleep($time)
		)