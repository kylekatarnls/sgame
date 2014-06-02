
$(document)

	.on 'click', 'a.toggle-next', ->
		$a = $ @
		$next = $a.nextAll('.toggleable')
		if ! $next.length
			$next = $a.parent().nextAll('.toggleable')
		$next.slideToggle()
		false

	.on 'click', 'p.image, p.no-image, .retina, .no-retina', ->
		$p = $ @
		$prev = $p.prevAll('.upload')
		if ! $prev.length
			$prev = $a.parent().prevAll('.toggleable')
		$prev.find('input[type="file"]').click()
		false

	.on 'change', '.upload input[type="file"]', ->
		@form.submit()

	.on 'change', '.upload input[type="checkbox"]', ->
		data = {}
		data[$(@).attr('name')] = if $(@).prop('checked') then '1' else '0'
		ajax 'survey/image/to-be-replaced', data, (r) ->
			console.log r