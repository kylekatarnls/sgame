// Menus déroulant au clic
// Exemple : lorsqu'on clique sur le bouton tout en haut à droite,
// La liste des choix possibles pour le nombre de résultats par page se déroule
$('.dropdown-toggle').click(function () {
	$('[aria-labelledby="' + $(this).attr('id') + '"]').slideToggle();
});


// Insertion dynamique du début du titre dans un <span> dans le but de l'afficher/masquer ultérieurement
(function () {
    var string = $('h1').html();
    var index = string.indexOf("-");
    if (index !== -1) {
        $('h1').html("<span class='mobile-hidden'>" + string.substr(0, index + 1) + "</span>" + string.substr(index + 1));
    }
})();

// Afficher/masquer les formulaires en fondu lors du clic sur un bouton
// Exemple : lorsqu'on clique sur le bouton + en haut à droite,
// Le formulaire pour ajouter une URL apparaît
(function ($panel) {
	var $form = $panel.find('form');
	$panel.find('a.btn').click(function () {
		$form.fadeToggle();
	});
})($('.option-panel'));

// Si on N'est PAS sur une page de résultats (résultats de recherche / les plus populaires / historique)
// Alors, 
if(!$('h1').is('.results')) {
	var $choicePerPage = $('[aria-labelledby="choice-per-page"]');
	$choicePerPage.find('a[data-value]').click(function () {
		$('input[name="resultsPerPage"]').val($(this).data('value'));
		$choicePerPage.slideUp();
		return false;
	});
}

// La fonction resize est exécuté à chaque fois que les dimensions de la fenêtre changent, cela inclut :
// - Redimension à la main par l'utilisateur 
// - Zoom de type Control + Molette
// - Rotation de l'appareil (passage de smartphone/tablette à l'horizontal/vertical)
// - etc.
function resize() {
	var screenWidth = $('body').width();
	// Desktop
	if(screenWidth > 640) {
		$('.navbar-inner .input-group').removeClass('input-group-sm');
		$('.navbar-inner .btn-group').removeClass('btn-group-sm');
	}
	// Mobile
	else {
		$('.navbar-inner .input-group').addClass('input-group-sm');
		$('.navbar-inner .btn-group').addClass('btn-group-sm');
	}
	// Très étroit (mobile en portrait)
	if(screenWidth < 420) {
		$('.navbar-inner .btn-group').css({
			clear: 'right',
			float: 'right',
			margin: '11px 0 -70px'
		});
		$('h1').css({
			marginTop: '0',
			fontSize: '22px'
		});
		$('.mobile-hidden').hide();
	}
	else {
		$('.navbar-inner .btn-group').css({
			clear: '',
			float: '',
			margin: ''
		});
		$('h1').css({
			marginTop: '',
			fontSize: ''
		});
		$('.mobile-hidden').show();
	}
}
resize();
$(window).resize(resize);


// Auto-complétion : lorsqu'on tape dans la barre de recherche, des solutions possibles
// sont proposées à l'utilisateur
$('[name="q"]').autocomplete(function(query, callback) {
	$.ajax({
		url: '/autocomplete',
		type: 'POST',
		dataType: 'json',
		data: {
			q: query
		},
		error: function() {
			callback();
		},
		success: function(res) {
			callback(res);
		}
	});
});
