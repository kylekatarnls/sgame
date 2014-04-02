class Positionable

	tag: 'div'

	constructor: (content, style, x, y) ->
		@x = x || 0
		@y = y || 0
		ctor = this.constructor
		id = ctor.name.toLowerCase()
		classes = [id]
		while typeof(ctor.__super__) is 'object'
			ctor = ctor.__super__.constructor
			classes.push ctor.name.toLowerCase()
		@jQueryObject = $('<' + @tag + ' ' + (if $('#' + id).length then '' else id = 'id="' + id + '"') + ' class="' + classes.join(' ') + '">' + content + '</' + @tag + '>')
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
