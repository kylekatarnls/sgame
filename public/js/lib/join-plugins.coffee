plugins = (input) ->
	if typeof(input) is 'function'
		(->
			for plugin in plugins
				eval 'var ' + plugin + ' = plugins["' + plugin + '"];';
			if input() is false
				delete window.plugins
		)()
	else
		list = input.split(/\s+/g)
		for plugin in list
			plugins[plugin] = window[plugin]
			delete window[plugin]
