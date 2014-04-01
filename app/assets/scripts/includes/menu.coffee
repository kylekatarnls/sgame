
# Menus déroulant au clic
# Exemple : lorsqu'on clique sur le bouton tout en haut à droite,
# La liste des choix possibles pour le nombre de résultats par page se déroule
$('.dropdown-toggle').click ->
	$('[aria-labelledby="' + $(@).attr('id') + '"]').slideToggle()
	return


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
