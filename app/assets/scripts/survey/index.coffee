
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
		ajax 'survey/image/to-be-replaced', data, (res) ->
			#console.log res

	.on 'click', '.git-diff', ->
		$link = $ @
		$diff = $link.next('.diff')
		if $diff.hasClass('open')
			$diff.removeClass('open').html ''
		else
			$diff.addClass('open').html '\nLoading...'
			ajax 'survey/diff', { file: $link.attr('href') }, (res) ->
				console.log(res)
				if res.diff
					html = ''
					$.each(res.diff.split(/\n/g), ->
						classAttr = switch this.charAt(0)
							when '@' then ' class="comment-line"'
							when '+' then ' class="add-line"'
							when '-' then ' class="remove-line"'
							else ''

						html += '<div' + classAttr + '>' + this + '</div>'
					)
					$diff.html html
		false



$(->
	$('p.image').hover(
		->
			$img = $(@).find('img')
			w = $img.width()
			h = $img.height()
			timeout = $img.data('timeout-detail')
			if timeout
				clearTimeout(timeout)
			else
				$img.next('span.detail').remove()
				text = w + ' x ' + h + ' &nbsp; Retina : ' + (w*2) + ' x ' + (h*2)
				$('<span class="detail">' + text + '</span>')
					.fadeOut(0)
					.fadeIn()
					.insertAfter($img)
		,
		->
			$img = $(@).find('img')
			$img.data('timeout-detail', setTimeout(->
				$img.removeData('timeout-detail')
				.next('span.detail').fadeOut(->
					$(@).remove()
				)
			, 400))
	)
)