$('.dropdown-toggle').click(function () {
	$('[aria-labelledby="'+$(this).attr('id')+'"]').slideToggle();
});

(function ($panel) {
	var $form = $panel.find('form');
	$panel.find('a.btn').click(function () {
		$form.fadeToggle();
	});
})($('.option-panel'));

if(!$('h1').is('.results')) {
	var $choicePerPage = $('[aria-labelledby="choice-per-page"]');
	$choicePerPage.find('a[data-value]').click(function () {
		$('input[name="resultsPerPage"]').val($(this).data('value'));
		$choicePerPage.slideUp();
		return false;
	});
}