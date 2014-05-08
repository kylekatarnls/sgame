<?

PluginManager

	STYLE_EXTENSIONS = 'css|styl|stylus|scss|sass|less'

	s* $plugins = array()


	s+ isStyle $ressource
		< !! preg_match('#\.(' . :STYLE_EXTENSIONS . ')$#', $ressource)

	s+ isScript $ressource
		< ! static::isStyle($ressource)

	s+ getStyles
		< array_filter(static::getRessources(), array(get_called_class(), 'isStyle'))

	s+ getScripts
		< array_filter(static::getRessources(), array(get_called_class(), 'isScript'))

	s+ getRessources
		< array_flatten(static::$plugins)

	s+ getPlugins
		< static::$plugins

	s+ getPlugin $name
		< static::$plugins[$name]

	s+ addPlugin $name, $ressources = null
		if is_null($ressources)
			$ressources = $name
		static::$plugins[$name] = $ressources

	s+ hasPlugin $name
		< isset(static::$plugins[$name])

	s+ removePlugin $name
		unset(static::$plugins[$name])