<?

trait Wait

	s+ waitFor $time, $intervalle, $key, $value = null
		$microtime = microtime(true)
		$changed = false
		if is_null($value)
			$value = static::get($key)
		elseif $value not static::get($key)
			$changed = true
		if ! $changed
			do
				Session::usleep($intervalle * 1000)
				if $value not static::get($key)
					$changed = true
					break
			while(microtime(true) < $microtime + $time)
		< $changed
