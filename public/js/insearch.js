$('.dropdown-toggle').click(function () {
	$('[aria-labelledby="'+$(this).attr('id')+'"]').slideToggle();
});