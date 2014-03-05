<?

Form:Illuminate\Support\Facades\Form

	s+ open($options)
		< parent::open(is_array($options) ? $options : array('url' => $options));

	s+ text($name, $value = null, $options = array(), $placeholder = null, $autocomplete = true)
		if(is_string($options))
			$options = array('class' => $options);
		if(!is_null($placeholder))
			$options['placeholder'] = $placeholder;
		if(!$autocomplete)
			$options['autocomplete'] = 'off';
		< parent::text($name, $value, $options);