//- require 'geoobject'

class Player extends Mobile

	tag: 'img'

	constructor: (x, y, w, h) ->
		super '', 
			background: 'gray'
			textAlign: 'center'
			lineHeight: (h || 32) + 'px'
			width: (w || 32) + 'px'
			height: (h || 32) + 'px'
		, x, y
		@jQueryObject
			.attr('src', 'https://www.google.fr/images/srpr/logo11w.png')
			.attr('alt', 'Joueur')
			.setWall('.wall')
			#.eightDirections()
			.platform
				speed: 0.2
				jumpForce: 90
				gravityForce: 6


class Wall extends Positionable

	constructor: (x, y, w, h) ->
		super '', 
			background: 'silver'
			width: (w || 32) + 'px'
			height: (h || 32) + 'px'
		, x, y
		@jQueryObject.addClass 'wall'


cHeight = cWidth = 600
height = width = 64
new Wall -cWidth/2, -cHeight/2, width, cHeight
new Wall cWidth/2-width, -cHeight/2, width, cHeight
new Wall -cWidth/2, -cHeight/2, cWidth, height
new Wall -cWidth/2, cHeight/2-height, cWidth, height
b = new Player -width/2, -height/2, width, height
