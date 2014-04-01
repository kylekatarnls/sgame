localStorage = localStorage||{};
sessionStorage = sessionStorage||{};
(function (w)
{
	w.$document = $(document);
	w.$window = $(w);
	w.siteid = $('meta[name="siteid"]').attr('content');

	w.cookie={
		set:function (name,value,days,path)
		{
			path=path||'/';
			if(days)
			{
				var date=new Date();
				date.setTime(
					days>10000?
						days:
						date.getTime()+(days*24*60*60*1000)
				);
				var expires="; expires="+date.toGMTString();
			}
			else
				var expires="";
			document.cookie=name+"="+value+expires+"; path="+path;
		},
		get:function (name)
		{
			for(var q=name+"=", c=document.cookie.split(';'), l=c.length, i=0; i<l; i++)
			{
				while(c[i].charAt(0)==' ') c[i]=c[i].substring(1,c[i].length);
				if(c[i].indexOf(q)==0) return c[i].substring(q.length,c[i].length);
			}
			return null;
		},
		getAll:function ()
		{
			for(var data={}, c=document.cookie.split(';'), l=c.length, i=0; i<l; i++)
			{
				var egal=c[i].indexOf('=');
				data[c[i].substr(0,egal)]=c[i].substr(egal+1);
			}
			return data;
		},
		sup:function (name)
		{
			cookie.set(name,"",-1);
		},
		__isset:function (name)
		{
			return (cookie.get(name)===null);
		},
		__empty:function (name)
		{
			var c=cookie.get(name);
			return (c===null || c==='');
		}
	};

	w.sound =
	{
		create: function (file, className, id)
		{
			if(typeof(file) === 'string')
			{
				file = file.indexOf('.') === -1 ? [file+'.mp3',file+'.ogg'] : [file];
			}
			if(typeof(file) === 'object')
			{
				var src = '', gType = {
					'mp3' : 'mpeg',
					'oga' : 'ogg',
					'm4a' : 'mp4',
					'webma' : 'webm',
				};
				$.each(file, function ()
				{
					var type = this.split(/\./g);
					type = type[type.length-1];
					if(typeof(gType[type]) === 'string')
					{
						type = gType[type];
					}
					src += '<source src="'+
						(this.charAt(0) !== '/' ? '/' : '')+
						(this.indexOf('/') === -1 ? 's/'+siteid+'/' : '')+
						this+
						'" type="audio/'+type+'"></source>';
				});
				return $('<audio preload="auto"'+
						(className ? ' class="'+className+'"' : '')+
						(id ? ' id="'+id+'"' : '')
						+'>'+src+'</audio>')
					.prependTo('body')
					.on('ended', function ()
					{
						$(this).remove();
					});
			}
			return false;
		},
		play: function (file, className, id)
		{
			var $a=this.create(file, className, id);
			if(!$a)
			{
				return false;
			}
			return $a.attr('autoplay', 'autoplay');
		},
		stop: function (selector)
		{
			var $a=$('body > audio');
			if(selector)
			{
				$a=$a.filter(selector);
			}
			$a.each(function ()
			{
				this.stop();
				$(this).remove();
			});
		}
	};

})(window);

function documentpagechange(event, data)
{
	/*
	if(typeof(window.showOnce)==='undefined')
	{
		window.showOnce=true;
		$('body script').remove();
	}
	else
	{
		$('body script').each(function ()
		{
			var $this=$(this), html=$.trim($this.html()), src=$this.attr('src');
			if(html!=='')
			{
				$.globalEval(html);
			}
			if(src && src!=='')
			{
				$.getScript(src);
			}
		}).remove();
	}
	*/
	window.$body = $('body');
	window.$page = $('#page');
	var $socle = ($page.length ? $page : $body);
	if(window.gPre)
	{
		$.each(window.gPre, function (index, match)
		{
			$socle.prepend(match.replace(/\\NEW-LINE/g, "\n"));
		});
		window.gPre=false;
	}
	if(typeof(wsRegister) === 'function')
		wsRegister();
}
$document.on('click', '.h-button, .hold-focus', function ()
{
	var $button = $(this);
	$button.addClass('focus');
	setTimeout(function ()
	{
		$button.removeClass('focus');
	}, 600);
})
.on('click', '.logout', logOut)
.bind('pageload', function (event, data)
{
	$('body > pre').remove();
	window.gPre = (typeof(data.xhr) === 'undefined' ? null : data.xhr.responseText.replace(/[\r\n]/g, '\\NEW-LINE').match(/<pre\sstyle="([^"]+)">(.+)<\/pre>/ig));
})
.bind('pagechange', documentpagechange)
.bind('pagechangefailed', documentpagechange)
.bind('mobileinit', function()
{
	loaderText("Chargement...");
	$.mobile.loader.prototype.options.textVisible = true;
	$.mobile.loader.prototype.options.theme = "a";
	$.mobile.loader.prototype.options.html = "";
});

function loaderText(text)
{
	$.mobile.loader.prototype.options.text = text;
}
function ucfirst(text, lowerEnd)
{
	if(text && text.length && text+'' === text)
	{
		var end = text.substring(1);
		if(lowerEnd)
		{
			end = end.toLowerCase();
		}
		return text.charAt(0).toUpperCase()+end;
	}
	return '';
}
function css3(gProp, css2)
{
	var ext = {};
	css2 = css2 || {};
	$.each(gProp, function (i, val)
	{
		$.each(['-o-', '-ms-', '-moz-', '-khtml-', '-webkit-'], function (j, prefix)
		{
			ext[prefix+i] = val;
		});
	});
	return $.extend(ext, gProp, css2);
}
$.fn.error = function (duration)
{
	duration = duration || 1000;
	$this = $(this);
	var frame = 40, last = Math.round(duration/frame);
	for(var i=0; i<=last; i++)
	{
		(function (i)
		{
			setTimeout(function ()
			{
				if(i === last)
				{
					$this.css(css3({
						'box-shadow' : ''
					},{
						border : ''
					}));
				}
				else
				{
					var r = Math.sin(Math.PI*i/2), c = Math.round(255*Math.sqrt(0.4-0.4*r));
					$this.css(css3({
						'box-shadow' : (
							'0 0 '+Math.round(1+r*4)+'px rgb(255, '+c+', '+c+'),'+
							'inset 0 0 '+Math.round(r*3)+'px rgb(255, '+c+', '+c+')'
						)
					},{
						border : '1px solid rgb(255, '+c+', '+c+')'
					}));
				}
			}, i*40);
		})(i);
	}
};
function toggle(id)
{
	var r = true;
	id = id || 'main';
	if(typeof(toggle.data[id]) === 'undefined')
	{
		toggle.data[id] = r;
	}
	else
	{
		r = (!toggle.data[id]);
		toggle.data[id] = r;
	}
	return r;
}
toggle.data = {};
function logOut()
{
	$page.append('<form action="" method="post" id="logout"><input type="hidden" name="logout" value="1" /></form>');
	$('#logout').submit();
}

function settype(v, type)
{
	switch(type)
	{
		case 'string':
			v = v+'';
			break;
		case 'object':
		case 'array':
			if(typeof(v) !== 'object')
				v = [v];
			break;
		case 'int':
		case 'integer':
			v = parseInt(v);
			if(isNaN(v)) v = 0;
			break;
		case 'float':
		case 'number':
			v = parseFloat(v);
			if(isNaN(v)) v = 0;
			break;
		case 'bool':
		case 'boolean':
			v = !!v;
			break;
		default:
			v = null;
	}
}

function array_value(arr, key, def, type, nullIfEmpty)
{
	var v = (typeof(arr[key]) === 'undefined' ? null : arr[key]);
	if(
		typeof(v) === 'undefined' ||
		(
			nullIfEmpty &&
			(v === 0 || v === '' || v === false || v === [] || v === {} || v === '0')
		)
	)
	{
		v = null;
	}
	if(v !== null & type)
	{
		settype(v, type);
	}
	if(v === null && typeof(def) !== 'undefined')
	{
		v = def;
	}
	return v;
}
window.arrayValue=array_value;

function dateFromSql(sqlDate)
{
	sqlDate=sqlDate.split(/\s+/g);
	var heure=sqlDate[1], date=sqlDate[0];
	date=date.split(/-/g);
	heure=heure.split(/:/g);
	var d=new Date;
	d.setFullYear(date[0]);
	d.setMonth(date[1]-1);
	d.setDate(date[2]);
	d.setHours(heure[0]);
	d.setMinutes(heure[1]);
	d.setSeconds(heure[2]);
	return d;
}

function evalDataJs(context, data, fct)
{
	if(typeof(data.js)==='string'){
		$.globalEval(data.js);
		delete data.js;
	}
	if(fct){
		fct.call(context,data);
	}
}

function ajax(url,data,done,settings,fail)
{
	if(typeof(settings)==='function'){
		var s=fail;
		fail=settings;
		settings=s;
	}
	return $.ajax('/'+url,
		$.extend({
				type:'POST',
				dataType:'JSON',
				data:data
			},
			settings||{}
		)
	).done(function (data){
		evalDataJs(this, data, done);
	}).fail(fail||function (){});
}

function intval(n){
	n = parseInt(n);
	return isNaN(n) ? 0 : n;
}

function floatval(n){
	n = parseFloat(n);
	return isNaN(n) ? 0 : n;
}

function rand(min, max){
	return Math.floor(min+(max-min+1)*Math.random());
}

function centerBox(width, height){
	return $('<div style="overflow: auto; position: absolute; left: 50%; top: 50%; width: '+Math.round(width)+'px; height: '+Math.round(height)+'px; margin: -'+Math.round(height/2)+'px 0 0 -'+Math.round(width/2)+'px;"></div>');
}

function overlay(col, $to, fct){
	$to = $($to||'body');
	var overflow = $to.css('overflow');
	$to.css('overflow', 'hidden');
	var $overlay = $('<div class="overlay" style="position: fixed; left: 0; top: 0; width: 100%; height:100%; background: '+(col||'rgba(0, 0, 0, 0.3)')+';"></div>')
		.appendTo($to)
		.fadeOut(0)
		.fadeIn();
	var fo = $overlay.fadeOut;
	$overlay.fadeOut = function (a, b, c){
		function fin(){
			$overlay.remove();
		}
		var d = a;
		if(typeof(a) === 'object'){
			d = typeof(a.duration) === 'undefined' ? 400 : a.duration;
		}
		switch(typeof(d)){
			case 'undefined':
				setTimeout(fin, 400);
				break;
			case 'number':
				setTimeout(fin, d);
				break;
			case 'string':
				setTimeout(fin, d === 'slow' ? 600 : 200);
				break;
		}
		$to.css('overflow', overflow);
		return fo.call(this, a, b, c);
	};
	if(fct !== false) {
		$overlay.click(function (e){
			if($overlay[0] === e.target){
				typeof(fct) === 'function' ?
					fct.call($overlay[0]):
					$overlay.fadeOut();
			}
		});
	}
	return $overlay
}

Array.prototype.pick = function (n){
	var l = this.length,
		n = Math.max(1, intval(n)),
		r = [];
	if(l <= n){
		for(var i = 0; i < l; i++){
			r.push(this[i]);
		}
	}
	else {
		for(var k = []; k.length < n;){
			var rd = rand(0, l-1);
			if(k.indexOf(rd) === -1){
				k.push(rd);
				r.push(this[rd]);
			}
		}
	}
	return r;
}

$.fn.extend({
	disable: function (events){
		if(typeof(events) !== 'object'){
			events = events.split(/\s+/g);
		}
		var $this = $(this);
		$.each(events, function (){
			$this.on(this, function (e){
				e.preventDefault();
				return false;
			});
		});
		return $this;
	},
	css3: function (props, val){
		var elt=$(this);
		if(typeof(props) === 'string'){
			if(typeof(val) === 'undefined'){
				var r = elt.css(props);
				if(typeof(r) === 'undefined'){
					$.each(['O', 'ms', 'Moz', 'Khtml', 'Webkit'], function (i, pref){
						r = elt.css(pref+props.charAt(0).toUpperCase()+props.substr(1));
						if(typeof(r) !== 'undefined'){
							return false;
						}
					});
				}
				return r;
			}
			else {
				var prop = props;
				props = {};
				props[prop] = val;
			}
		}
		$.each(props, function (prop, val){
			elt.css(prop, val);
			prop=prop.charAt(0).toUpperCase()+prop.substr(1);
			$.each(['O', 'ms', 'Moz', 'Khtml', 'Webkit'], function (i, pref){
				elt.css(pref+prop, val);
			})
		});
		return elt;
	},
	pos: function (x, y){
		return $(this).css({
			left: x+'px',
			top: y+'px'
		});
	},
	pick: function (n){
		return $($(this).get().pick(n));
	},
	blink: function (d, it){
		d=d||600;
		it=it||100;
		var elt=this, c=Math.floor(d/it), it2=Math.floor(it/2);
		elt.fadeTo(it2, 0);
		for(var i=0; i<=c; i++){
			(function (i){
				setTimeout(function (){
					elt.fadeTo(it2, i%2 ? 0 : 1);
				}, d-i*it);
			})(i);
		}
		return elt;
	},
	roll: function (d, angle){
		var elt=this;
		d=d||600;
		if((['null', 'undefined']).indexOf(typeof(angle)) !== -1){
			angle=360;
		}
		setTimeout(function (){
			elt.css3({
				transition: '',
				transform: ''
			});
		}, d);
		return elt.css3({
			transition: 'transform '+d+'ms',
			transform: 'rotate('+angle+'deg)'
		});
	},
	rumble: function (d, it, rx, ry){
		d=d||600;
		it=it||40;
		rx=rx||18;
		ry=ry||18;
		var c=Math.floor(d/it);
		return this.each(function (){
			var elt=$(this), props=elt.css(['position', 'left', 'top']),
			left=parseInt(props.left), top=parseInt(props.top);
			if(isNaN(left)){
				left=0;
			}
			if(isNaN(top)){
				top=0;
			}
			if(('fixed', 'relative', 'absolute').indexOf(props.position)===-1){
				elt.css('position', 'relative');
			}
			for(var i=1; i<=c; i++){
				elt.animate({
					left:left+Math.round(Math.random()*2*rx)-rx,
					top:top+Math.round(Math.random()*2*ry)-ry
				}, it);
			}
			elt.animate({
				left:left,
				top:top
			}, it, function (){
				elt.css({
					position: props.position ? props.position : '',
					left: props.left,
					top: props.top
				});
			});
		});
	}
});

jQuery.expr[':'].regex = function(elem, index, match) {
	var matchParams = match[3].split(','),
		validLabels = /^(data|css):/,
		attr = {
			method: matchParams[0].match(validLabels) ? matchParams[0].split(':')[0] : 'attr',
			property: matchParams.shift().replace(validLabels,'')
		},
		regexFlags = 'ig',
		regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
	return regex.test(jQuery(elem)[attr.method](attr.property));
}
