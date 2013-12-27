$('.dropdown-toggle').click(function () {
	$('[aria-labelledby="'+$(this).attr('id')+'"]').slideToggle();
});

if(!$('h1').is('.results'))
{
	$choicePerPage = $('[aria-labelledby="choice-per-page"]');
	$choicePerPage.find('a[data-value]').click(function () {
		$('input[name="resultsPerPage"]').val($(this).data('value'));
		$choicePerPage.slideUp();
		return false;
	});
}