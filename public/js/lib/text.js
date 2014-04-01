$.each({
	mouseover: function (e)
	{
		$(this).addClass('highlighted');
	},
	mouseout: function (e)
	{
		$(this).removeClass('highlighted');
	},
	click: function (e)
	{
		var $this = $(this);
		html2data($this);
		ajax('submitTranslation', $.extend($this.data(), {
			text: $this.text()
		}));
	}

}, function (evt, func)
{
	$(document).on(evt, 'span.text', func);
});
function html2data($elt)
{
	var $span = $elt.find('.data');
	if($span.length === 1)
	{
		$elt.data($.parseJSON($span.text()));
		$span.remove();
	}
}
$('span.text').each(function ()
{
	html2data($($this));
});