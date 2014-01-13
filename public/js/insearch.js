$('.dropdown-toggle').click(function () {
	$('[aria-labelledby="'+$(this).attr('id')+'"]').slideToggle();
});

(function () {
    var string = $('h1').html();
    var index = string.indexOf("-");
    if (index !== -1)
    {
        $('h1').html("<span class='mobile-hidden'>" + string.substr(0, index+1) + "</span>" + string.substr(index+1));
    }
})();

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
	}
}
resize();
$(window).resize(resize);

$('[name="q"]').autocomplete(function(query, callback) {
	if(!query.length) {
		return callback();
	}
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
