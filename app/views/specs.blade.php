@extends('home')

@section('content')

	@parent

	<h3>Tests unitaires JavaScript</h3>

	<div id="specs-container"></div>
	
@stop

@section('css')

	@parent

	{{ HTML::style('css/jasmine.css') }}

@stop

@section('js')

	@parent

	{{ HTML::script('js/jasmine/jasmine.js') }}
	{{ HTML::script('js/jasmine/jasmine-html.js') }}
	{{ HTML::script('js/jasmine/boot.js') }}
	{{ HTML::script('js/tests/insearch.js') }}
	{{ HTML::script('js/tests/typeahead.js') }}

@stop