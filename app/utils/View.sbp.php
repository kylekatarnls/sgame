<?

View:Illuminate\Support\Facades\View

	s+ withShared $data = array()
		< array_merge(static::getShared(), $data)

	s+ hasShared $key
		< array_key_exists($key, static::getShared())