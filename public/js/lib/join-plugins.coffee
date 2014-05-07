plugins = (list) ->
	list = list.split(/\s+/g)
	for plugin in list
		plugins[plugin] = window[plugin]
		delete window[plugin]
