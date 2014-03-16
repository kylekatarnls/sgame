<?

Form:Illuminate\Support\Facades\Form

	s+ open $options, $second = null
		$options = is_array($options) ? $options : array('url' => $options);
		if ! is_null($second)
			array_merge(**$options, $second);
		< parent::open($options);

	s+ input $type, $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		if is_string($options)
			$options = array('class' => $options);
		if !is_null($placeholder)
			$options['placeholder'] = $placeholder;
		if !$autocomplete
			$options['autocomplete'] = 'off';
		< parent::input($type, $name, $value, $options);

	s+ text $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('text', $name, $value, $options, $placeholder, $autocomplete);

	s+ pass $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('password', $name, $value, $options, $placeholder, $autocomplete);

	s+ password $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('password', $name, $value, $options, $placeholder, $autocomplete);

	s+ email $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('email', $name, $value, $options, $placeholder, $autocomplete);

	s+ number $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('number', $name, $value, $options, $placeholder, $autocomplete);

	s+ color $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('color', $name, $value, $options, $placeholder, $autocomplete);

	s+ date $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('date', $name, $value, $options, $placeholder, $autocomplete);

	s+ dateTime $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('datetime', $name, $value, $options, $placeholder, $autocomplete);

	s+ localDateTime $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('datetime-local', $name, $value, $options, $placeholder, $autocomplete);

	s+ dateTimeLocal $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('datetime-local', $name, $value, $options, $placeholder, $autocomplete);

	s+ file $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('file', $name, $value, $options, $placeholder, $autocomplete);

	s+ month $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('month', $name, $value, $options, $placeholder, $autocomplete);

	s+ range $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('range', $name, $value, $options, $placeholder, $autocomplete);

	s+ search $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('search', $name, $value, $options, $placeholder, $autocomplete);

	s+ tel $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('tel', $name, $value, $options, $placeholder, $autocomplete);

	s+ time $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('time', $name, $value, $options, $placeholder, $autocomplete);

	s+ url $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('url', $name, $value, $options, $placeholder, $autocomplete);

	s+ week $name, $value = null, $options = array(), $placeholder = null, $autocomplete = true
		< static::input('week', $name, $value, $options, $placeholder, $autocomplete);