//- require 'jquery-1.11.0.min'
//- require 'bootstrap.min'
//- require 'typeahead'

# Menus déroulant au clic
# Exemple : lorsqu'on clique sur le bouton tout en haut à droite,
# La liste des choix possibles pour le nombre de résultats par page se déroule
$('.dropdown-toggle').click ->
	$('[aria-labelledby="' + $(@).attr('id') + '"]').slideToggle()
	return


# Insertion dynamique du début du titre dans un <span> dans le but de l'afficher/masquer ultérieurement
(->
	string = $('h1').html()
	index = string.indexOf "-"
	if index isnt -1
		$('h1').html("<span class='mobile-hidden'>" + string.substr(0, index + 1) + "</span>" + string.substr(index + 1))
	return
)()

# Afficher/masquer les formulaires en fondu lors du clic sur un bouton
# Exemple : lorsqu'on clique sur le bouton + en haut à droite,
# Le formulaire pour ajouter une URL apparaît
(($panel) ->
	$form = $panel.find 'form'
	$panel.find('a.btn').click ->
		$form.fadeToggle()
		return
	return
) $ '.option-panel'

# Si on N'est PAS sur une page de résultats (résultats de recherche / les plus populaires / historique)
# Alors, 
unless $('h1').is '.results'
	$choicePerPage = $ '[aria-labelledby="choice-per-page"]'
	$choicePerPage.find('a[data-value]').click ->
		$('input[name="resultsPerPage"]').val $(@).data 'value'
		$choicePerPage.slideUp()
		false

# La fonction resize est exécuté à chaque fois que les dimensions de la fenêtre changent, cela inclut :
# - Redimension à la main par l'utilisateur 
# - Zoom de type Control + Molette
# - Rotation de l'appareil (passage de smartphone/tablette à l'horizontal/vertical)
# - etc.
resize = ->
	screenWidth = $('body').width()
	# Desktop
	if(screenWidth > 640)
		$('.navbar-inner .input-group').removeClass 'input-group-sm'
		$('.navbar-inner .btn-group').removeClass 'btn-group-sm'
	# Mobile
	else
		$('.navbar-inner .input-group').addClass 'input-group-sm'
		$('.navbar-inner .btn-group').addClass 'btn-group-sm'
	$('.navbar-inner .btn-group').css
		clear: ''
		float: ''
		margin: ''
	$('h1').css
		marginTop: ''
		fontSize: ''
	$('#footer').css
		fontSize: ''
	$('.mobile-hidden').show()
	# Très étroit (mobile en portrait)
	if $('.navbar-inner').height() > 50
		$('.navbar-inner .btn-group').css
			clear: 'right'
			float: 'right'
			margin: '11px 0 -70px'
		$('h1').css
			marginTop: '0'
			fontSize: '22px'
		$('#footer').css
			fontSize: '12px'
		$('.mobile-hidden').hide()
	return

resize()
$(window).resize resize


# Auto-complétion : lorsqu'on tape dans la barre de recherche, des solutions possibles
# sont proposées à l'utilisateur
$('[name="q"]').autocomplete(
	(query, callback) ->
		$.ajax
			url: '/autocomplete'
			type: 'POST'
			dataType: 'json'
			data:
				q: query
			error: ->
				callback()
				return
			success: (res) ->
				callback res
				return
		return
	, (value) ->
		switch value.toLowerCase()
			when "slide", "bounce"
				$("body").animate
					paddingTop: 160
				, 200, ->
					$("body").animate
						paddingTop: 60
					, 200
					return

			when "roll", "barrel roll", "rotate"
				$("body").animate(
					rotate: 0
				, 0).animate
					rotate: 1
				,
					duration: 800
					step: (now) ->
						prop = "rotate(" + (Math.round(now * 2 * 360) % 360) + "deg)"
						$(@).css
							"-webkit-transform": prop
							"-moz-transform": prop
							transform: prop

						return

			when "shake", "rumble"
				$("body").animate
					marginLeft: -80
				, 100, ->
					$("body").animate
						marginLeft: 80
					, 200, ->
						$("body").animate
							marginLeft: -80
						, 200, ->
							$("body").animate
								marginLeft: 0
							, 100
							return

						return

					return
)

$(document).on('click', '.remember-me', ->
	$this = $(@);
	if $this.is '.selected'
		$(@).removeClass('selected')
			.next('input')
			.val('')
	else
		$(@).addClass('selected')
			.next('input')
			.val('on')
)
