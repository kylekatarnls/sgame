//- require 'games/mmo'

ajaxUrl = '../..'+$('#chat [name="ajax-url"]').val()

$('#chat [name="message"]').focus().keypress (e) ->
	if e.keyCode is 13
		ajax(
			ajaxUrl,
				message: $(@).val()
				_token: $('#chat [name="_token"]').val()
			,
			(data) ->
				console.log(data)
		)
		$(@).val('')
		e.preventDefault()
		e.stopPropagation()
		false
		#$(@form).submit()

waitForNewMessages = ->
	ajax(
		ajaxUrl, {},
			(data) ->
				console.log(data)
				waitForNewMessages()
	)

$('#content pre').click ->
	ajax 'user/list'
	$(@).text 'Chargement...'