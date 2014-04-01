
class Positionable

	constructor: (content, style, x, y) ->
		@x = x || 0
		@y = y || 0
		@jQueryObject = $('<div class="mobile">' + content + '</div>').appendTo '#origin'
		style = $.extend({
				left: @x + 'px'
				top: @y + 'px'
			},
			style || {
				background: 'gray'
				width: '32px'
				height: '32px'
			}
		)
		@jQueryObject.css style


class Mobile extends Positionable

	move: (x, y) ->
		@x += x
		@y += y
		@jQueryObject.animate(
			left: @x
			top: @y
		)


class Player extends Mobile

	constructor: (x, y) ->
		super 'A', 
			background: 'gray'
			textAlign: 'center'
			lineHeight: '32px'
			width: '32px'
			height: '32px'
		, x, y
		@jQueryObject
			.eightDirections()
			.setWall('.wall')


class Wall extends Positionable

	constructor: (x, y, w, h) ->
		super '', 
			background: 'silver'
			width: w + 'px'
			height: h + 'px'
		, x, y
		@jQueryObject.addClass 'wall'


new Wall -200, -200, 32, 400
new Wall 200-32, -200, 32, 400
new Wall -200, -200, 400, 32
new Wall -200, 200-32, 400, 32
b = new Player -16, -16
