$.fn.autocomplete = function (callback) {
	$(this).each(function () {
		var $input = $(this), cache = [], lastValue = '', $autocomplete;
		$input
			.keyup(function () {
				var val = $input.val();
				lastValue = val;
				if(typeof(cache[val]) === 'undefined') {
					callback($input.val(), function (list) {
						if(lastValue === val) {
							var html = '';
							if(typeof(list) === 'object') {
								$.each(list, function () {
									html += '<span>' + this + '</span>';
								});
							}
							cache[val] = html;
							$autocomplete.html(html);
						}
					});
				}
				else {
					$autocomplete.html(cache[val]);
				}
			})
			.before('<span style="position: relative;"><span style="width: ' + $input.parent().width() + 'px;" class="autocomplete"></span></span>');
		$autocomplete = $input
			.parent()
			.find('.autocomplete')
			.on('click', 'span', function () {
				$input
					.val($(this).text())
					.parents('form')
					.submit();
			});
	});
};