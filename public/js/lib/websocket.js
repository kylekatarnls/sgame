(function (w)
{
	function wsOpen(gEvt)
	{
		try
		{
			w.wsList = [];
			var ip = $('meta[name="ip"]').attr('content');
			w.websocket = new WebSocket("ws://"+(ip ? ip : 'holowarsql.dyndns.org')+":9001/server.php");
			$.each(gEvt, function (evt, fct)
			{
				websocket['on'+evt] = fct;
			});
		}
		catch(e)
		{
			if(typeof(noWebSocket) === 'function')
			{
				noWebSocket();
			}
			else
			{
				w.noWebSocket = true;
			}
		}
	}
	w.wsSend = function (gData)
	{
		gData = JSON.stringify($.extend({
			'sess': cookie.get('hsid'),
			'ctrl': 'hologame',
			'h_host': siteid
		}, gData));
		try
		{
			websocket.send(gData);
		}
		catch(e)
		{
			w.wsList.push(gData);
		}
	};
	w.wsRegister = function ()
	{
		if(typeof(iduser) !== 'undefined')
		{
			wsSend({action: 'register'});
			w.wsRegister = function (){};
		}
	}
	var failure = 0, ws_gEvt = {
		'open' : function ()
		{
			var ws = this;
			$.each(w.wsList, function (i,m)
			{
				ws.send(m);
			});
			wsRegister();
		},
		'close' : function ()
		{
			if(++failure < 5) setTimeout(function ()
			{
				wsOpen(ws_gEvt);
			}, 1000);
		},
		'message' : function (ev)
		{
			evalDataJs(w, JSON.parse(ev.data), typeof(getMatch) === 'function' ? getMatch : null);
		},
		'error' : function (ev)
		{
			console.trace();
			console.error(ev.data);
		}
	}
	wsOpen(ws_gEvt);

})(window);