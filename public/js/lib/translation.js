if(typeof(window.translateTextOn)==='undefined')
{
	console.log('window.translateTextOn :');
	console.log(window.translateTextOn);
	window.translateTextOn=true;
	var language=$('#language').val(), $submitButton;
	function getLineData(elt){
		var gVal=[], gFile=[], gHost=[];
		$.each(elt, function (){
			var $tr=$(this).parent();
			while(!$tr.is('tr')){
				$tr=$tr.parent();
			}
			while(!$tr.is('.title')){
				$tr=$tr.prev('tr');
			}
			gVal.push(this.value);
			gFile.push($tr.find('td.file').html());
			gHost.push($tr.is('.host'));
		});
		return {
			language: language,
			text: gVal,
			host: gHost,
			file: gFile
		};
	}
	function submitButton(){
		var $body=$('.body');
		if(!$submitButton){
			$body.append('<input type="button" value="Envoyer toutes les traductions" class="send-all" />').trigger('create');
			$submitButton=$body.find('input.send-all');
			while(!$submitButton.is('.ui-btn')){
				$submitButton=$submitButton.parent();
			}
			$submitButton.click(function (){
				ajax('admin/translation/line', getLineData($('textarea[name="translate-text"]')), function (d){
					console.log(d);
				});
			});
		}
		else {
			var f=function (s, c) {
				s=$(s);
				return (s.length && s.is('.'+c));
			};
			if(f('#crypt-key', 'ui-text-input')
			|| f($body.find('input.send-all'), 'ui-btn')) {
				$body.trigger('create');
			}
		}
		$submitButton[$('textarea[name="translate-text"]').length ? 'show' : 'hide']();
	}
	$(document).on('click', '.translate-text', function (){
		var $this=$(this), html=$this.html();
		if($this.find('textarea').length<1){
			$this.html('<textarea name="translate-text" style="width:'+Math.round($this.width()-12)+'px; position:relative; top:-1px;">'+html+'</textarea>')
				.find('textarea')
				.trigger('focus')
				.trigger('select')
				.keypress(function (e){
					if(!e.shiftKey){
						switch(e.keyCode){
							case 13:
								var $tr=$this.parent();
								while(!$tr.is('tr')){
									$tr=$tr.parent();
								}
								while(!$tr.is('.title')){
									$tr=$tr.prev('tr');
								}
								ajax('admin/translation/line', getLineData([this]), function (d){
									console.log(d);
								});
								return false;
							case 27:
								$this.html(html);
								submitButton();
								return false;
						}
					}
				});
			submitButton();
		}
	});
}