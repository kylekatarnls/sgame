$.fn.extend
	exists: ->
		$(@).length > 0
	autocomplete: (callback, onTestValue) ->
		$(@).each ->
			$input = $(@)
			cache = []
			lastValue = ''
			$autocomplete = []
			$('.autocomplete.last-created').removeClass 'last-created'
			testValue = ->
				val = $input.val()
				if typeof onTestValue is 'function'
					onTestValue.call @, val
				if lastValue isnt val
					lastValue = val
					if val is ''
						$autocomplete.html ''
					else if typeof cache[val] is 'undefined'
						callback $input.val(), (list) ->
							if lastValue is val
								html = ''
								if typeof list is 'object'
									$.each list, ->
										html += '<span>' + @ + '</span>'
								cache[val] = html;
								$autocomplete.html html
							return
					else
						$autocomplete.html cache[val]
			$input
				.keydown (event) ->
				
					switch event.keyCode
				
						when 38 #up
							$selected = $autocomplete.find 'span.selected'
							$prev = $autocomplete.find 'span:last'
							if $selected.exists()
								$p = $selected.prev 'span'
								$prev = $p if $p.exists()
								$selected.removeClass 'selected'
							$prev.addClass 'selected'
							event.stopPropagation()
							return false
				
						when 40 #down
							$selected = $autocomplete.find 'span.selected'
							$next = $autocomplete.find 'span:first'
							if $selected.exists()
								$n = $selected.next 'span'
								$next = $n if $n.exists()
								$selected.removeClass 'selected'
							$next.addClass 'selected'
							event.stopPropagation()
							return false
						
						when 39, 13 #left, enter
							$selected = $autocomplete.find 'span.selected'
							if $selected.exists()
								value = $selected.text()
								$autocomplete
									.html('')
									.parent()
									.next('input')
									.val(value)

					return

				.on('keyup change click focus blur mouseup', testValue)
				.before('<span style="position: relative;"><span style="width: ' + $input.parent().width() + 'px;" class="autocomplete last-created"></span></span>')
			$autocomplete = $input
				.parent()
				.find('span.autocomplete')
				.on('click', 'span', ->
					$input
						.val($(@).text())
						.parents('form')
						.submit()
				)
				.on('mouseover', 'span', ->
					$autocomplete.find('span').removeClass 'selected'
					$(@).addClass 'selected'
					return
				)
				.on('mouseout', 'span', ->
					$autocomplete.find('span').removeClass 'selected'
					return
				)