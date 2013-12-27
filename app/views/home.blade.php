@extends('layouts.insearch')

@section('content')

	<h1>{{ §('global.title') }}</h1>

	@if(isset($url))
		<div class="alert alert-success">
			{{ §('global.url-added', array('url' => '<strong>' . $url . '</strong>' )) }}
		</div>
	@endif

	@include('includes.searchbar', array('q' => ''))

@stop