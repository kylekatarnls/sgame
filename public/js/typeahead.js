$.fn.extend({
	exists: function () {
		return $(this).length > 0;
	},
	autocomplete: function (callback, onTestValue) {
		$(this).each(function () {
			var $input = $(this), cache = [], lastValue = '', $autocomplete;
			$('.autocomplete.last-created').removeClass('last-created');
			function testValue() {
				var val = $input.val();
				if(typeof(onTestValue) === 'function') {
					onTestValue.call(this, val);
				}
				if(lastValue !== val) {
					lastValue = val;
					if(val === '') {
						$autocomplete.html('');
					}
					else if(typeof(cache[val]) === 'undefined') {
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
				}
			}
			$input
				.keydown(function (event){
				
					switch(event.keyCode) {
				
						case 38: //up
							var $selected = $autocomplete.find('span.selected'),
								$prev = $autocomplete.find('span:last');
							if($selected.exists()) {
								var $p = $selected.prev('span');
								if($p.exists()) {
									$prev = $p;
								}
								$selected.removeClass('selected');
							}
							$prev.addClass('selected');
							event.stopPropagation();
							return false;
				
						case 40: //down
							var $selected = $autocomplete.find('span.selected'),
								$next = $autocomplete.find('span:first');
							if($selected.exists()) {
								var $n = $selected.next('span');
								if($n.exists()) {
									$next= $n;
								}
								$selected.removeClass('selected');
							}
							$next.addClass('selected');
							event.stopPropagation();
							return false;
						
						case 39: //left
						case 13: //enter
							var $selected = $autocomplete.find('span.selected');
							if($selected.exists()) {
								var value = $selected.text();
								console.log($autocomplete.next('input'));
								$autocomplete
									.html('')
									.parent()
									.next('input')
									.val(value);
							}
							break;
					}
				})
				.on('keyup change click focus blur mouseup', testValue)
				.before('<span style="position: relative;"><span style="width: ' + $input.parent().width() + 'px;" class="autocomplete last-created"></span></span>');
			$autocomplete = $input
				.parent()
				.find('span.autocomplete')
				.on('click', 'span', function () {
					$input
						.val($(this).text())
						.parents('form')
						.submit();
				})
				.on('mouseover', 'span', function (){
					$autocomplete.find('span').removeClass('selected');
					$(this).addClass('selected');
				})
				.on('mouseout', 'span', function (){
					$autocomplete.find('span').removeClass('selected');
				});
		});
	}
});