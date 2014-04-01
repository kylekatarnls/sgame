function rotateYStep(now, fx){
	var $this = $(fx.elem||this);
	return $this.rotateY(
		(1-fx.pos) * intval($this.data('rotateY'))  +
		fx.pos * fx.end
	);
}

$.fn.extend({
	rotateY: function (a){
		return $(this).css3('transform','rotateY('+a+'deg)');
	},
	flip: function (duration, fct){
		if(typeof(duration) === 'function'){
			var d = fct;
			fct = duration;
			duration = d;
		}
		return $(this)
			.stop()
			.css3({
				'transform-style': 'preserve-3d',
				perspective: 100
			})
			.data('rotateY', 0)
			.rotateY(0)
			.animate({
				rotateY: 90
			}, {
				step: rotateYStep,
				duration: duration,
				complete: function (){
					if(fct){
						fct.call(this);
					}
					$(this)
						.data('rotateY', -90)
						.rotateY(-90)
						.animate({
							rotateY: 0
						}, {
						step: rotateYStep,
							duration: duration,
						});
				}
			});
	}
});