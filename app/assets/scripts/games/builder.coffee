
class Positionable

	constructor: (content, style, x, y) ->
		@x = x || 0
		@y = y || 0
		@jQueryObject = $('<div class="mobile">' + content + '</div>')
			.appendTo('#origin')
			.css($.extend({
					left: @x + 'px'
					top: @y + 'px'
				},
				style || {
					background: 'gray'
					width: '32px'
					height: '32px'
				}
			))


class Mobile extends Positionable

	move: (x, y) ->
		@x += x
		@y += y
		@jQueryObject.animate(
			left: @x
			top: @y
		)


class Player extends Mobile

	constructor: (x, y, w, h) ->
		super 'A', 
			background: 'gray'
			textAlign: 'center'
			lineHeight: (h || 32) + 'px'
			width: (w || 32) + 'px'
			height: (h || 32) + 'px'
		, x, y
		@jQueryObject
			.eightDirections()
			.setWall('.wall')
			.collision '.wall', ->
				$(@).stop()


class Wall extends Positionable

	constructor: (x, y, w, h) ->
		super '', 
			background: 'silver'
			width: (w || 32) + 'px'
			height: (h || 32) + 'px'
		, x, y
		@jQueryObject.addClass 'wall'


cHeight = cWidth = 500
height = width = 64
new Wall -cWidth/2, -cHeight/2, width, cHeight
new Wall cWidth/2-width, -cHeight/2, width, cHeight
new Wall -cWidth/2, -cHeight/2, cWidth, height
new Wall -cWidth/2, cHeight/2-height, cWidth, height
b = new Player -width/2, -height/2, width, height
