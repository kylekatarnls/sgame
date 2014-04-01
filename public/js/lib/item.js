var Recorder = {
	data: {},
	toSession: function (key){
		key = key || 'dataRecorder';
		if(typeof(sessionStorage) !== 'oject'){
			return false;
		}
		sessionStorage[key] = Recorder.data;
		return true;
	},
	toLocal: function (key){
		key = key || 'dataRecorder';
		if(typeof(localStorage) !== 'oject'){
			return false;
		}
		localStorage[key] = Recorder.data;
		return true;
	},
	fromSession: function (key){
		key = key || 'dataRecorder';
		if(typeof(sessionStorage) !== 'oject' || typeof(sessionStorage[key]) !== 'object'){
			return false;
		}
		Recorder.data = sessionStorage[key];
		return true;
	},
	fromLocal: function (key){
		key = key || 'dataRecorder';
		if(typeof(localStorage) !== 'oject' || typeof(localStorage[key]) !== 'object'){
			return false;
		}
		Recorder.data = localStorage[key];
		return true;
	},
	set: function (table, obj){
		var keys = [], vals = [];
		for(var i in obj){
			vals.push(obj[i]);
			keys.push(i);
		}
		Recorder.data[table] = {
			keys: keys,
			vals: [vals]
		};
		return 1;
	},
	add: function (table, obj){
		var o = Recorder.data[table];
		if(typeof(o.keys) === 'undefined'){
			return Recorder.set(table, obj);
		}
		var keys = [], vals = [];
		for(var i in o.keys){
			var k = o.keys[i], v = typeof(obj[k]) === 'undefined' ? null : obj[k];
			vals.push(v);
		}
		o.vals.push(vals);
		return o.vals.length;
	},
	get: function (table, id, obj){
		obj = obj || {};
		var o = Recorder.data[table];
		if(typeof(o.keys) === 'undefined'){
			return null;
		}
		id--;
		for(var i in o.keys){
			var k = o.keys[i];
			obj[k] = o.vals[id][i];
		}
		return obj;
	}
};
function Item(){
	
}