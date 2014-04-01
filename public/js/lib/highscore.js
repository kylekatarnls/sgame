function highscore(score, col, fct){
	$overlay = overlay(col, 'body', fct);
	$(centerBox(400, 400))
		.appendTo($overlay)
		.css('background', 'rgba(140, 140, 140, 0.8)')
		.css3({
			'box-shadow': '0 0 5px black',
			'border-radius': '10px'
		})
		.append('<div style="margin: 190px auto 0; text-align: center;" id="highscore">'+
			'<input type="text" placeholder="Entrez votre pseudo" />'+
		'</div>');
	var $input = $('#highscore input');
	if(localStorage.pseudo){
		$input.val(localStorage.pseudo);
	}
	$input.keyboard('\r', function (){
		$highscore = $('#highscore').text('Chargement en cours...');
		ajax(
			'game/highscore',
			{
				score: score,
				pseudo: (localStorage.pseudo = $input.val())
			},
			function (data){
				var table = '<tr><th colspan="3" style="font-size: 17px; font-weight: bold; padding-bottom: 5px;">Meilleurs scores</th></tr>';
				$(data.list).each(function (){
					var date = this.date.substr(0, 10).split('-');
					table += '<tr><td style="padding: 2px 5px;">'+date[2]+'/'+date[1]+'/'+date[0]+'</td><td style="padding: 2px 5px;">'+this.score+'</td><td style="padding: 2px 5px;">'+(this.name === '' ? 'Anonyme' : this.name)+'</td></tr>';
				});
				$highscore
					.css('margin', '20px 10px 0')
					.html('<table style="width: 100%;">'+table+'</table>');
			}
		);
	}, 'press');
	setTimeout(function (){$input.focus();}, 800);
}