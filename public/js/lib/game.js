function starter(html, fct, button, col){
	$overlay = overlay(col).click(function (e){
		setTimeout(fct, 1);
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	$(centerBox(700, 360))
		.appendTo($overlay)
		.css({
			'font-weight': 'bold',
			'background': 'rgba(140, 140, 140, 0.8)',
			'text-shadow': '0 1px 1px rgba(255, 255, 255, 0.5)'
		})
		.css3({
			'box-shadow': '0 0 5px black',
			'border-radius': '10px'
		})
		.append('<div style="margin: 30px;">'+
			html+
		'</div><div style="margin: 30px; text-align: center;" class="clicker">'+
			(button || '<input type="button" value="Jouer" style="width: 120px; height: 40px; font-weight: bold;" />')+
		'</div>')
		.find('.clicker')
		.click(function (e){
			$overlay.click();
			e.preventDefault();
			e.stopPropagation();
			return false;
		});
}

function laser(col, width, height, strong, sound, img, fct){
	strong = strong||10;
	return $('<div style="width: '+(width||0)+'px; height: '+(height||0)+'px; background: white;"></div>')
		.css3({
			'box-shadow': '0 0 '+Math.round(strong/2)+'px white, 0 0 '+strong+'px '+col,
			'border-radius': '9999px'
		})
		.setExplodeSound(sound || 'c/choc')
		.setExplodeImg(img || '/c/lib/explosion2.gif')
		.die(fct || function (){
			$(this).stop().explode();
		});
}
function getKeyCode(char){
	if(typeof(char) === 'string'){
		return char.charCodeAt(0);
	}
	return intval(char);
}

keyboardList = {};
function keyboard(keys, fct, type){
	return $document.keyboard(keys, fct, type);
}


function overElements(el1, el2, tolerance){
	tolerance = intval(tolerance);
	return (
		el1.offsetLeft+el1.offsetWidth+tolerance > el2.offsetLeft && el2.offsetLeft+el2.offsetWidth+tolerance > el1.offsetLeft
		&&
		el1.offsetTop+el1.offsetHeight+tolerance > el2.offsetTop && el2.offsetTop+el2.offsetHeight+tolerance  > el1.offsetTop
	);
}

function collisionElements(fct, el1, el2, _this){
	var t = (new Date).getTime(),
	t1 = intval($(el1).data('colllisionTime'));
	if(t-t1>500){
		$(el1).data('colllisionTime', t);
		fct.call(_this||el1, el2, el1);
	}
}


function getDirection(x1, y1, x2, y2){
	if(typeof(x2) === 'undefined'){
		var c = $(x1).center();
		y1 = c.top;
		x1 = c.left;
		c = $(y1).center();
		y2 = c.top;
		x2 = c.left;
	}
	else if(typeof(y2) === 'undefined'){
		if(typeof(x1) === 'object'){
			y2 = x2;
			x2 = y1;
			var c = $(x1).center();
			y1 = c.top;
			x1 = c.left;
		}
		else {
			var c = $(x2).center();
			y2 = c.top;
			x2 = c.left;
		}
	}
	var d = Math.sqrt(Math.pow(x1-x2, 2)+Math.pow(y1-y2, 2)),
		a = Math.asin((x1-x2)/d)*180/Math.PI;
	return (y2<y1 ? 180-a : a);
}

(function (targets){
	$.collision = function (el, target, fct){
		targets.push([el, target, fct]);
	};
	$.fn.extend({
		keyboard: function(keys, fct, type){
			var $this = $(this),
			keyboardList = $this.data('keyboardList')||{};
			type = type||'down';
			if(typeof(keys) !== 'object'){
				keys = [keys];
			}
			$.each(keys, function (i, k){
				keys[i] = getKeyCode(k);
			});
			if(typeof(keyboardList[type]) === 'undefined'){
				keyboardList[type] = [];
				$this['key'+type](function (e){
					if($(e.target).is($this) || (['input', 'textarea', 'select']).indexOf(e.target.tagName.toLowerCase()) === -1){
						$.each(keyboardList[type], function (){
							if(this[0].indexOf(e.keyCode) !== -1){
								this[1].call(window, e);
							}
						});
					}
				});
			}
			keyboardList[type].push([keys, fct]);
			return $this.data('keyboardList', keyboardList);
		},
		strike: function (target, fct, tolerance){
			if(typeof(fct) !== 'function'){
				var t = intval(fct);
				fct = tolerance;
				tolerance = t;
			}
			if(typeof(tolerance) === 'undefined'){
				tolerance = 6;
			}
			return $(this).on('strike', function (){
				var _this = this;
				$(this).getBullets().each(function (){
					var bullet = this;
					$(target).each(function (){
						if(this !== bullet && overElements(this, bullet, tolerance)){
							collisionElements(fct, bullet, this, _this);
						}
					});
				});
			});
		},
		collisionTest: function (now, fx){
			var $elem = $(fx ? fx.elem : this),
				$shooter = $elem.data('shooter');
			$elem.trigger('move', [$elem.offset()]);
			if($shooter){
				$shooter.trigger('strike', [now, fx]);
			}
			$.each(targets, function (){
				var el1 = this[0], el2 = this[1], fct = this[2];
				if($elem.is(el1)){
					$(el2).each(function (){
						if(this != fx.elem && overElements(this, fx.elem)){
							collisionElements(fct, fx.elem, this);
						}
					});
				}
				if($elem.is(el2)){
					$(el1).each(function (){
						if(this != fx.elem && overElements(this, fx.elem)){
							collisionElements(fct, this, fx.elem);
						}
					});
				}
			});
			if($elem.data('centerView') === true){
				$elem.centerView();
			}
		},
		fadeFrom: function (from, duration) {
			var $this = $(this), to = {
				opacity: 1,
				left: $this.css('left'),
				top: $this.css('top'),
				scale: 1
			},
			options = {
				step: function (now, fx) {
					if(fx.prop === 'scale') {
						$this.css3({
							transform: 'scale('+now+')'
						});
					}
				}
			};
			if(typeof(duration) !== 'undefined') {
				otpions.duration = duration;
			}
			return $this.appendTo(from)
			.animate($.extend({
				opacity: 0,
				left: 0,
				top: 0,
				scale: 0
			}, css3({
				transform: 'scale(0)'
			})), 0)
			.animate(to, options);
		},
		collision: function (target, fct){
			targets.push([this, target, fct]);
			return $(this);
		},
		center: function (left, top){
			var $this = $(this);
			if(typeof(left) === 'undefined'){
				var o = $this.offset();
				return o ? {
					left: Math.round(o.left+$this.width()/2),
					top: Math.round(o.top+$this.height()/2)
				} : {
					left: Math.round($('body').width()/2),
					top: Math.round($('body').height()/2),
				};
			}
			else {
				if(typeof(top) === 'undefined'){
					top = left.center().top;
					left = left.center().left;
				}
				return $this.css({
					left: Math.round(left-$this.width()/2),
					top: Math.round(top-$this.height()/2)
				});
			}
		},
		centerView: function (fixed){
			var $this = $(this);
			if(typeof(fixed) === 'undefined'){
				var $body = $('body'),
				center = $this.center(),
				time = (new Date).getTime(),
				centerViewTime = intval($this.data('centerViewTime'));
				if(time - centerViewTime > 100){
					$('body').stop().animate({
						scrollLeft: center.left-$body.width()/2,
						scrollTop: center.top-$body.height()/2
					}, {
						duration: 200,
						step: function (now, fx){
							switch(fx.prop){
								case 'scrollLeft':
									$document.scrollLeft(now);
									break;
								case 'scrollTop':
									$document.scrollTop(now);
									break;
							}
						}
					});
					$this.data('centerViewTime', time);
				}
			}
			else {
				$this.data('centerView', !!fixed);
			}
			return $this;
		},
		dir: function (angle){
			if(typeof(angle) !== 'undefined'){
				angle = (floatval(angle)+360)%360;
				return $(this)
					.css3({ transform: 'rotate('+angle+'deg)' })
					.data('direction', angle);
			}
			else {
				return floatval($(this).data('direction'));
			}
		},
		distanceFrom: function (x, y){
			if(typeof(y) === 'undefined'){
				y = x.center().top;
				x = x.center().left;
			}
			var ix = $(this).center().left,
			iy = $(this).center().top;
			return Math.sqrt(Math.pow(ix-x, 2)+Math.pow(iy-y, 2));
		},
		lookAt: function (x, y){
			if(typeof(y) === 'undefined'){
				y = x.center().top;
				x = x.center().left;
			}
			var $this = $(this),
			ix = $this.center().left,
			iy = $this.center().top,
			d = Math.sqrt(Math.pow(ix-x, 2)+Math.pow(iy-y, 2)),
			a = Math.asin((ix-x)/d)*180/Math.PI;
			return $this.dir(y<iy ? 180-a : a);
		},
		setBullet: function (b){
			return $(this).data('defaultBullet', b);
		},
		getBullets: function (){
			var className = $(this).data('bulletClassName');
			if(className){
				return $('.'+className);
			}
			return $('');
		},
		clearMove: function (){
			var $this = $(this);
			$.each(['up', 'down', 'left', 'right'], function (){
				$this.removeData(this);
			});
			return $this.trigger('reMove').stop().setMove({}, true);
		},
		health: function (set){
			var $this = $(this),
			h = intval($this.data('health'));
			if(typeof(set) === 'function'){
				return $this.on('health', set);
			}
			if(typeof(set) === 'undefined'){
				return h;
			}
			if(set < 1){
				set = 0;
			}
			if(set < h){
				$this.trigger('hurt', set);
			}
			else if(set > h){
				$this.trigger('heal', set);
			}
			$this.trigger('health', set);
			if(set === 0){
				$this.trigger('die', set);
			}
			return $this.data('health', set);
		},
		heal: function (set){
			var $this = $(this);
			if(typeof(set) === 'function'){
				return $this.on('heal', fct);
			}
			return $this.health($this.health()+set);
		},
		hurt: function (set){
			var $this = $(this);
			if(typeof(set) === 'function'){
				return $this.on('hurt', fct);
			}
			return $this.health($this.health()-set);
		},
		_die: $.fn.die,
		die: function (fct){
			var $this = $(this);
			if(typeof(fct) === 'undefined'){
				return $this.health(0);
			}
			return $this.on('die', fct);
		},
		switchImages: function (stopped, moving){
			var $this = $(this), f = function () {
				setTimeout(function () {
					$this.attr('src',$this.is(':animated') ? moving : stopped);
				}, 50);
			};
			$(window).mouseup(f).mousedown(f);
			return $this.on('reMove', f).trigger('reMove');
		},
		moveImage: function (moving){
			var $this = $(this),
			img = new Image,
			f = function (){
				$this.switchImages($this.attr('src'), moving);
			};
			img.src = moving;
			img.width > 0 ? f() : (img.onload = f);
			return $this;
		},
		stopImage: function (stopped){
			var $this = $(this),
			img = new Image,
			f = function (){
				$this.switchImages(stopped, $this.attr('src'));
			};
			img.src = stopped;
			img.width > 0 ? f() : (img.onload = f);
			return $this;
		},
		setMove: function (controls, toOff){
			var $this = $(this), c = {},
			controls = controls||{};
			$.each({
				up: 38,
				down: 40,
				left: 37,
				right: 39
			}, function (i, k){
				c[i] = (typeof(controls[i])==='undefined' ? k : controls[i]);
				if(typeof(c[i]) !== 'object'){
					c[i] = [c[i]];
				}
				for(var j in c[i]){
					if(typeof(c[i][j]) === 'string'){
						c[i][j] = getKeyCode(c[i][j]);
					}
				}
			});
			function setDataBool(b){
				b = !!b;
				return function (e){
					if((['input', 'textarea', 'select']).indexOf(e.target.tagName.toLowerCase()) === -1){
						var k = e.keyCode, r = true;
						$.each(['up', 'down', 'left', 'right'], function (){
							var s = ''+this; // Forcer le type
							if(c[s].indexOf(k) !== -1 && $this.data(s) !== b){
								$this.data(s, b).trigger('reMove');
								e.preventDefault();
								r = false;
								return false;
							}
						});
						return r;
					}
				};
			}
			var namespace = $this.data('keyNamespace');
			if(toOff){
				if(namespace){
					$document.off('.'+namespace);
				}
			}
			else {
				if(!namespace){
					$this.data('keyNamespace', namespace = 'kns'+rand(1, 99999999));
				}
				$document.on('keydown.'+namespace, setDataBool(true))
					.on('keyup.'+namespace, setDataBool(false));
			}
			return $this;
		},
		isOver: function ($elt, returnFirstElement, tolerance){
			var r = false;
			tolerance = intval(tolerance);
			$elt = $($elt);
			$(this).each(function (){
				var _this = this, $this = $(this),
				o1 = $this.offset(),
				w1 = $this.width(),
				h1 = $this.height();
				$elt.each(function (){
					$cible = $(this);
					o2 = $cible.offset(),
					w2 = $cible.width(),
					h2 = $cible.height();
					if(
						o1.left+w1+tolerance > o2.left && o2.left+w2+tolerance > o1.left
						&&
						o1.top+h1+tolerance > o2.top && o2.top+h2+tolerance > o1.top
					){
						r = (returnFirstElement ? _this : true);
						return false;
					}
				});
				if(r){
					return false;
				}
			});
			return r;
		},
		spaceOffset: function ($target, tolerance, allOver, sign){
			$target = $($target);
			var $this = $(this),
			o1 = $target.offset(),
			o2 = $this.offset(),
			pos = {},
			i;
			tolerance = 2*intval(tolerance);
			sign = floatval(sign);
			if(allOver || o2.left+$this.width()/2 >= o1.left+$target.width()/2){
				i = o2.left-o1.left-$target.width()-tolerance;
				if(sign === 0 || (sign>0) === (i>0)){
					pos.left = i;
				}
			}
			if(allOver || o2.left+$this.width()/2 <= o1.left+$target.width()/2){
				i = o1.left-o2.left-$this.width()-tolerance;
				if(sign === 0 || (sign>0) === (i>0)){
					pos.right = i;
				}
			}
			if(allOver || o2.top+$this.height()/2 >= o1.top+$target.height()/2){
				i = o2.top-o1.top-$target.height()-tolerance;
				if(sign === 0 || (sign>0) === (i>0)){
					pos.top = i;
				}
			}
			if(allOver || o2.top+$this.height()/2 <= o1.top+$target.height()/2){
				i = o1.top-o2.top-$this.height()-tolerance;
				if(sign === 0 || (sign>0) === (i>0)){
					pos.bottom = i;
				}
			}
			return pos;
		},
		setShootSound: function (s){
			sound.create(s);
			return $(this).data('shootShound', s);
		},
		dataSound: function (key, soundFile){
			var $this = $(this);
			if(soundFile){
				sound.play(soundFile);
			}
			else if(soundFile = $this.data(key)){
				sound.play(soundFile);
			}
			return $this;
		},
		canShoot: function (){
			return $(this).removeData('canNotShoot');
		},
		canNotShoot: function (){
			return $(this).data('canNotShoot', true);
		},
		clearTimeout: function (key){
			var $this = $(this),
			timeout = $this.data(key);
			if(timeout) {
				clearTimeout(timeout);
				$this.removeData(key);
			}
			return $this;
		},
		setTimeout: function (key, fct, delay){
			var _this = this, $this = $(this);
			return $this.data(key, setTimeout(function (){
				$this.removeData(key);
				fct.call(_this);
			}, delay));
		},
		shootRepeat: function (repeat, $dom, soundFile, speed, direction){
			var $this = $(this),
			time = (new Date).getTime(),
			t = intval($this.data('lastShoot'));
			if($this.data('isShooting') || time-t<repeat){
				return $this;
			}
			return $this
				.data('lastShoot', time)
				.setTimeout('isShooting', function (){
					$this.shootRepeat(repeat, $dom, soundFile, speed, direction);
				}, repeat)
				.shoot($dom, soundFile, speed, direction);
		},
		shootStop: function (){
			return $(this).clearTimeout('isShooting');
		},
		shoot: function ($dom, soundFile, speed, direction){
			return $(this).each(function (){
				var $this = $(this),
				bulletClassName = $this.data('bulletClassName');
				if(typeof($dom) === 'function'){
					$this.on('shoot', $dom);
				}
				else if(!$this.data('canNotShoot')) {
					if(typeof($dom) === 'undefined'){
						$dom = $this.data('defaultBullet');
					}
					$dom = $dom.clone(true);
					$this.trigger('shoot')
						.dataSound('shootShound', soundFile)
						.before($dom);
					if(!bulletClassName){
						$this.data('bulletClassName', bulletClassName = 'bullet-'+rand(1, 99999999));
					}
					if(typeof(direction) === 'undefined'){
						direction = $this.dir();
					}
					direction = (typeof(direction) === 'function' ? direction() : direction);
					$dom.data('shooter', $this)
						.dir(direction)
						.addClass(bulletClassName)
						.css('position', 'absolute')
						.center($this)
						.animate({
							left: $dom.offset().left-Math.round(1500*Math.sin(Math.PI*direction/180)),
							top: $dom.offset().top+Math.round(1500*Math.cos(Math.PI*direction/180))
						}, {
							duration: 1000/(speed||1),
							easing: "linear",
							complete: function (){
								$dom.remove();
							},
							step: $.fn.collisionTest
						});
				}
			});
		},
		setExplodeSound: function (s){
			sound.create(s);
			return $(this).data('explodetShound', s);
		},
		setExplodeImg: function (img){
			img = img || '/c/lib/explosion1.gif';
			(new Image).src = img;
			return $(this).data('explodeImg', img);
		},
		explode: function (soundFile){
			return $(this).each(function (){
				var $this = $(this).dataSound('explodetShound', soundFile),
				img = $this.data('explodeImg');
				if($this.length){
					if(!img){
						img = '/c/lib/explosion1.gif';
					}
					var $img = $('<img src="'+img+'" />')
						.appendTo('body')
						.css('position', 'absolute')
						.center($this);
					$this.fadeOut(100, function (){
						$(this).remove();
					});
					setTimeout(function (){
						$img.fadeOut(function (){
							$img.remove();
						});
					}, 300);
				}
			});
		},
		goAhead: function (speed, limit, fct){
			if(typeof(limit) === 'function'){
				var l = fct;
				fct = limit;
				limit= l;
			}
			else if(typeof(speed) === 'function'){
				var v = limit;
				limit = fct;
				fct = speed;
				speed = v;
			}
			if(typeof(limit) === 'undefined'){
				limit = 999999999;
			}
			return $(this).each(function (){
				var $this = $(this),
				angle = $this.dir()*Math.PI/180,
				duration = 100/(speed||1);
				$this.animate({
					left: '-='+Math.round(Math.sin(angle)*100),
					top: '+='+Math.round(Math.cos(angle)*100)
				}, {
					duration: duration,
					easing: "linear",
					complete: function (){
						if(limit > 0){
							$this.goAhead(speed, limit - duration, fct);
						}
						else if(typeof(fct) === 'function'){
							fct.call(this);
						}
					},
					step: $.fn.collisionTest
				});
			});
		},
		eightDirections: function (speed, controls){
			if(typeof(speed) === 'object'){
				var inter = controls;
				controls = speed;
				speed = inter;
			}
			speed = Math.round((speed || 1)*100);
			var diagonale = Math.round(Math.sqrt(Math.pow(speed, 2)/2));
			return $(this)
			.setMove(controls)
			.on('reMove', function (){
				var $this = $(this);
				$this.trigger('control', [$this.data()]);
				function move(dir, pos){
					return $this
						.dir(dir)
						.stop()
						.animate(pos, {
							duration: 100,
							easing: "linear",
							complete: function (){
								$this.trigger('reMove');
							},
							step: $.fn.collisionTest
						});
				}
				if($this.data('up')){
					if($this.data('left')){
						return move(135, {
							top: '-='+diagonale,
							left: '-='+diagonale
						});
					}
					else if($this.data('right')){
						return move(225, {
							top: '-='+diagonale,
							left: '+='+diagonale
						});
					}
					else {
						return move(180, {
							top: '-='+speed
						});
					}
				}
				else if($this.data('down')){
					if($this.data('left')){
						return move(45, {
							top: '+='+diagonale,
							left: '-='+diagonale
						});
					}
					else if($this.data('right')){
						return move(315, {
							top: '+='+diagonale,
							left: '+='+diagonale
						});
					}
					else {
						return move(0, {
							top: '+='+speed
						});
					}
				}
				else if($this.data('left')){
					return move(90, {
						left: '-='+speed
					});
				}
				else if($this.data('right')){
					return move(270, {
						left: '+='+speed
					});
				}
				return $this.stop();
			});
		},
		racer: function (speed, turn, controls){
			if(typeof(speed) === 'object'){
				var inter = turn;
				turn = speed;
				speed = inter;
			}
			if(typeof(turn) === 'object'){
				var inter = controls;
				controls = turn;
				turn = inter;
			}
			turn = turn||10;
			speed = Math.round((speed || 1)*50);
			return $(this)
			.setMove(controls)
			.on('reMove', function (){
				var $this = $(this);
				$this.trigger('control', [$this.data()]);
				if($this.data('left')){
					$this.dir($this.dir()-turn);
				}
				else if($this.data('right')){
					$this.dir($this.dir()+turn);
				}
				var up = $this.data('up');
				if(up || $this.data('down')){
					var angle = ($this.dir()+(up ? 0 : 180))%360*Math.PI/180;
					$this
						.stop()
						.animate({
							left: '-='+Math.round(Math.sin(angle)*speed),
							top: '+='+Math.round(Math.cos(angle)*speed)
						}, {
							duration: 50,
							easing: "linear",
							complete: function (){
								$this.trigger('reMove');
							},
							step: $.fn.collisionTest
						});
				}
				return $this;
			});
		},
		setWall: function (sel){
			return $(this).data('wall', sel);
		},
		closest: function (sel, tolerance){
			if(typeof(sel) === 'number'){
				var t = sel;
				sel = tolerance;
				tolerance = t;
			}
			tolerance = intval(tolerance);
			var $this = $(this),
			elt = $this[0],
			oe = $this.offset(),
			r = {
				left: false,
				right: false,
				top: false,
				bottom: false
			};
			$(sel||$this.data('wall')).each(function (){
				if(this !== elt) {
					var ot = $(this).offset(), i;
					if(oe.top+elt.offsetHeight+tolerance > ot.top && ot.top+this.offsetHeight+tolerance > oe.top){
						if(ot.left+this.offsetWidth/2 >= oe.left+elt.offsetWidth/2){
							i = ot.left-oe.left-elt.offsetWidth;
							if((i>=0 || -i < elt.offsetWidth)
							&& (r.right === false || Math.abs(r.right.distance) > Math.abs(i))) {
								r.right = { distance: i, elem: this };
							}
						}
						else {
							i = oe.left-ot.left-this.offsetWidth;
							if((i>=0 || -i < elt.offsetWidth)
							&& (r.left === false || Math.abs(r.left.distance) > Math.abs(i))) {
								r.left = { distance: i, elem: this };
							}
						}
					}
					if(oe.left+elt.offsetWidth+tolerance > ot.left && ot.left+this.offsetWidth+tolerance > oe.left){
						if(ot.top+this.offsetHeight/2 >= oe.top+elt.offsetHeight/2){
							i = ot.top-oe.top-elt.offsetHeight;
							if((i>=0 || -i < elt.offsetHeight)
							&& (r.bottom === false || Math.abs(r.bottom.distance) > Math.abs(i))) {
								r.bottom = { distance: i, elem: this };
							}
						}
						else {
							i = oe.top-ot.top-this.offsetHeight;
							if((i>=0 || -i < elt.offsetHeight)
							&& (r.top === false || Math.abs(r.top.distance) > Math.abs(i))) {
								r.top = { distance: i, elem: this };
							}
						}
					}
				}
			});
			return r;
		},
		blocked: function (sel, tolerance, returnElem){
			if(typeof(sel) === 'number'){
				var t = sel;
				sel = tolerance;
				tolerance = t;
			}
			tolerance = intval(tolerance);
			var r = $(this).closest(sel, -tolerance);
			$.each(['left', 'right', 'top', 'bottom'], function (){
				if(r[this] && r[this].distance <= tolerance){
					r[this] = (returnElem ? r[this].elem : true);
				}
				else {
					r[this] = false;
				}
			});
			return r;
		},
		unblock: function (sel, tolerance, fx){
			if(typeof(sel) === 'number'){
				var t = sel;
				sel = tolerance;
				tolerance = t;
			}
			tolerance = intval(tolerance);
			var $this = $(this),
			elt = $this[0],
			side = { right: 'left', bottom: 'top'},
			list = [];
			$.each($this.closest(sel), function (dir, o){
				if(o && o.distance < tolerance){
					var i = o.distance-tolerance,
					equal = typeof(side[dir]) === 'undefined',
					prop = (equal ? dir : side[dir]);
					list.push([prop, -i, equal, o.elem]);
				}
			});
			list.sort(function (e1, e2) {
				return e1[1] - e2[1];
			});
			$.each(list, function (){
				if(overElements(this[3], elt)) {
					var i = this[1], equal = this[2], attachedTo = $this.data('attachedTo');
					$this.css(this[0], (equal ? '+=' : '-=')+i);
					if(attachedTo && attachedTo !== this[3]){
						$this.removeData('attachedTo');
					}
					if(fx && fx.prop === this[0]){
						fx.now += (equal ? i : -i);
					}
				}
			});
			return $this;
		},
		animateOptions: function (p, o, d, e, c){
			if(typeof(d) === 'object'){
				if(d.step){
					d._step = d.step;
					d.step = function (now, fx){
						o.step(now, fx);
						return d._step(now, fx);
					};
				}
				$.extend(o, d);
			}
			else {
				if(typeof(d) === 'function'){
					var i = e;
					e = d;
					d = i;
				}
				if(typeof(e) === 'function'){
					var i = c;
					c = e;
					e = i;
				}
				if(typeof(e) === 'number' || e === 'slow' || e === 'fast'){
					var i = e;
					e = d;
					d = i;
				}
				if(c){
					o.complete = c;
				}
				if(d){
					o.duration = d;
				}
				if(e){
					o.easing = e;
				}
			}
			return $.fn.animate.call(this, p, o);
		},
		animateWith: function (p, obj, d, e, c){
			return $.fn.animateOptions.call(this, p, {
				step: $(obj).wallStep()
			}, d, e, c);
		},
		wallStep: function (){
			var $this = $(this);
			return function (now, fx){
				$this.wallStepBy(now, fx);
			};
		},
		wallStepBy: function (now, fx){
			$el = $(fx.elem);
			var $this = $(this),
			attachedTo = $this.data('attachedTo');
			if($this.blocked(fx.elem).bottom || attachedTo === fx.elem){
				if(attachedTo !== fx.elem){
					$this.data('attachedTo', fx.elem);
				}
				var data = $el.data('wallStep')||{},
				offset = $el.offset(),
				u = (typeof(data[fx.prop]) === 'undefined'),
				d = (data[fx.prop] !== offset[fx.prop]);
				if(u || d){
					if(!u && d){
						$this
							.css(fx.prop, '+='+(offset[fx.prop]-data[fx.prop]))
							.collisionTest();
					}
					data[fx.prop] = offset[fx.prop];
					$el.data('wallStep', data);
					if(!$this.blocked(fx.elem, 6).bottom){
						$el.removeData('wallStep');
					}
				}
			}
			else if($el.data('wallStep')){
				$el.removeData('wallStep');
			}
		},
		control: function (key, fct){
			var $this = $(this);
			if(typeof(key) === 'function'){
				fct = key;
				return $this.on('control', function (){
					fct.call(this, $this.data());
				});
			}
			else {
				var initialValue = null;
				return $this.on('control', function (){
					var value = $this.data(key);
					if(value !== initialValue){
						fct.call(this, initialValue = value);
					}
				});
			}
		},
		platform: function (speed, jumpForce, gravityForce, controls){
			if(typeof(speed) === 'object'){
				var inter = jumpForce;
				jumpForce = speed;
				speed = inter;
			}
			if(typeof(jumpForce) === 'object'){
				var inter = gravityForce;
				gravityForce = jumpForce;
				jumpForce = inter;
			}
			if(typeof(gravityForce) === 'object'){
				var inter = controls;
				controls = gravityForce;
				gravityForce = inter;
			}
			if(typeof(jumpForce) === 'undefined'){
				jumpForce = 100;
			}
			if(typeof(gravityForce) === 'undefined'){
				gravityForce = 3;
			}
			var saut = 0, gravity = 0;
			speed = Math.round((speed || 1)*100);
			return $(this)
			.setMove(controls)
			.on('reMove', function (){
				var $this = $(this);
				$this.trigger('control', [$this.data()]).unblock();
				stopped = false,
				blocked = $this.blocked(1);
				if(blocked.bottom){
					saut = 0;
					gravity = 0;
				}
				else {
					gravity += gravityForce;
				}
				if($this.data('up') && !blocked.top && gravity === 0){
					$this.removeData('attachedTo');
					saut = jumpForce;
				}
				var left = 0, top = gravity-saut;
				if($this.data('left') && !blocked.left){
					left = -speed;
				}
				else if($this.data('right') && !blocked.right){
					left = speed;
				}
				if(saut){
					saut = Math.floor(saut/1.2);
				}
				if(left !== 0 || top !== 0){
					var pos = {};
					if(left !== 0){
						pos.left = (left<0 ? '-' : '+')+'='+Math.abs(left);
					}
					if(top !== 0){
						pos.top = (top<0 ? '-' : '+')+'='+Math.abs(top);
					}
					return $this
					.stop()
					.animate(pos, {
						duration: 50,
						easing: "linear",
						complete: function (){
							$this.trigger('reMove');
						},
						step: function (now, fx){
							$.fn.unblock.call(this, null, 0, fx);
							return $.fn.collisionTest.call(this, now, fx);
						}
					});
				}
				return $this.stop();
			})
			.trigger('reMove');
		}
		
	});
})([]);